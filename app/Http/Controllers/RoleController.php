<?php

namespace App\Http\Controllers;

use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $roles = Role::with('permissions')->get();

            return DataTables::of($roles)
                ->addColumn('permissions', function ($role) {
                    return $role->permissions->map(function ($p) {
                        return '<span class="badge bg-success">' . $p->name . '</span>';
                    })->implode(' ');
                })
                ->addColumn('created_at', function ($role) {
                    return $role->created_at ? $role->created_at->format('M d, Y') : '';
                })
                ->addColumn('action', function ($role) {
                    return view('roles.partials.action', compact('role'))->render();
                })
                ->rawColumns(['permissions', 'action'])
                ->make(true);
        }
        return view('roles.view');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = Permission::all();

        return view('roles.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request)
    {       
        $role = Role::create(['name' => $request->name]);
        $role->syncPermissions($request->permissions);

        session()->flash('success', 'Role added successfully.');
        return response()->json([
            'status' => true,
            'message' => 'Role added successfully.',
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        $permissions = Permission::all();

        return view('roles.edit', compact('role', 'permissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, Role $role)
    {
        // Check if anything has actually changed
        $nameChanged = $role->name !== $request->name;
        $oldPermissions = $role->permissions->pluck('name')->sort()->values()->toArray();
        $newPermissions = collect($request->permissions)->sort()->values()->toArray();
        $permissionsChanged = $oldPermissions !== $newPermissions;

        if (!$nameChanged && !$permissionsChanged) {
            return response()->json([
                'status' => false,
                'message' => 'Nothing to update homie.',
            ]);
        }

        // Only update if there are changes
        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions);

        session()->flash('success', 'Role updated successfully.');
        return response()->json([
            'status' => true,
            'message' => 'Role updated successfully.',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        // Prevent deleting special roles if needed, e.g. 'admin'
        // if (in_array($role->name, ['admin'])) {
        //     return response()->json(['status' => false, 'message' => 'Cannot delete this role.'], 403);
        // }
        $role->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'status' => true,
                'message' => 'Role deleted successfully.'
            ]);
        }
        return redirect()->route('roles.index')->with('success', 'Role deleted successfully');
    }
}
