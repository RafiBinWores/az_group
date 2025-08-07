<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $users = User::with('roles')->select(['id', 'name', 'email', 'avatar', 'created_at']);

            return DataTables::of($users)
                ->addColumn('avatar', function ($user) {
                    if ($user->avatar) {
                        return '<img src="' . asset('storage/' . $user->avatar) . '" class="rounded-circle" style="width:40px; height:40px; object-fit:cover;" alt="Avatar">';
                    } else {
                        return '<span class="badge bg-secondary rounded-circle" style="width:40px; height:40px; display: inline-flex; align-items: center; justify-content: center;">' .
                            strtoupper(substr($user->name, 0, 1)) .
                            '</span>';
                    }
                })
                ->addColumn('roles', function ($user) {
                    return $user->roles->pluck('name')->implode(', ');
                })
                ->addColumn('created_at', function ($role) {
                    return $role->created_at ? $role->created_at->format('M d, Y') : '';
                })
                ->addColumn('actions', function ($user) {
                    return view('users.partials.action', compact('user'))->render();
                })
                ->rawColumns(['avatar', 'actions'])
                ->make(true);
        }
        return view('users.view');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();

        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {

        // Handle avatar upload
        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $timestamp = now()->format('Ymd_His');
            $customName = $originalName . '_' . $timestamp . '.' . $extension;

            $avatarPath = $file->storeAs('avatar', $customName, 'public');
        }

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'avatar' => $avatarPath,
        ]);

        // Assign role
        if ($request->role) {
            $user->syncRoles($request->role);
        }

        session()->flash('success', 'User added successfully.');
        return response()->json([
            'status' => true,
            'message' => 'User added successfully.',
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($user)
    {
        $user = User::findOrFail($user);
        $roles = Role::all();

        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        // $user = User::findOrFail($user);

        // Handle avatar upload
        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar && file_exists(public_path('storage/' . $user->avatar))) {
                @unlink(public_path('storage/' . $user->avatar));
            }

            $file = $request->file('avatar');
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $timestamp = now()->format('Ymd_His');
            $customName = $originalName . '_' . $timestamp . '.' . $extension;

            $avatarPath = $file->storeAs('avatar', $customName, 'public');
        }

        // Only update fields that were changed
        $updateData = [
            'name' => trim($request->name),
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = bcrypt($request->password);
        }
        if ($avatarPath) {
            $updateData['avatar'] = $avatarPath;
        }

        $user->update($updateData);

        // Assign role
        if ($request->role) {
            $user->syncRoles($request->role);
        }

        session()->flash('success', 'User updated successfully.');
        return response()->json([
            'status' => true,
            'message' => 'User updated successfully.',
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'status' => true,
                'message' => 'User deleted successfully.'
            ]);
        }

        return redirect()->route('users.index')->with('success', 'User deleted successfully');
    }
}
