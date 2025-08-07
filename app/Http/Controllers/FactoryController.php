<?php

namespace App\Http\Controllers;

use App\Http\Requests\Factory\FactoryStoreRequest;
use App\Http\Requests\Factory\FactoryUpdateRequest;
use App\Models\Factory;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class FactoryController extends Controller
{
     /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $factories = Factory::all();

            return DataTables::of($factories)
                ->addColumn('created_at', function ($factory) {
                    return $factory->created_at ? $factory->created_at->format('M d, Y') : '';
                })
                ->addColumn('status', function ($factory) {
                    // Return a checkbox switch (Bootstrap style)
                    $checked = $factory->status ? 'checked' : '';
                    return '
                    <div class="form-check form-switch">
                        <input class="form-check-input toggle-status" type="checkbox" data-id="' . $factory->id . '" ' . $checked . '>
                        <label class="form-check-label">' . ($factory->status ? 'Active' : 'Inactive') . '</label>
                    </div>
                ';
                })
                ->addColumn('actions', function ($factory) {
                    return view('factories.partials.actions', compact('factory'))->render();
                })
                ->rawColumns(['status', 'actions'])
                ->make(true);
        }

        return view('factories.view');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('factories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FactoryStoreRequest $request)
    {
        // Create Garment type
        Factory::create([
            'name' => trim($request->name),
            'status' => $request->factoryStatus ? 1 : 0,
        ]);

        session()->flash('success', 'Factory created successfully.');
        return response()->json([
            'status' => true,
            'message' => 'Factory created successfully.',
        ]);
    }

    /**
     * update the status.
     */
    public function updateStatus(Request $request)
    {
        $factory = Factory::findOrFail($request->id);
        $factory->status = $request->status;
        $factory->save();

        return response()->json(['status' => true, 'message' => 'Status updated!']);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($factory)
    {
        $factory = Factory::findOrFail($factory);

        return view('factories.edit', compact('factory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FactoryUpdateRequest $request,Factory $factory)
    {
        $typeChanged = $factory->name !== $request->name;
        $statusChanged = $factory->status !== (int)$request->factoryStatus;

        if (!$typeChanged && !$statusChanged) {
            return response()->json([
                'status' => false,
                'message' => 'Nothing to update.',
            ]);
        }
        // Create 
        $factory->update([
            'name' => trim($request->name),
            'status' => $request->factoryStatus ? 1 : 0,
        ]);

        session()->flash('success', 'Factory updated successfully.');
        return response()->json([
            'status' => true,
            'message' => 'Factory updated successfully.',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($factory)
    {
        $factory = Factory::findOrFail($factory);

        $factory->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'status' => true,
                'message' => 'Factory data deleted successfully.'
            ]);
        }

        return redirect()->route('factories.index')->with('success', 'Factory data deleted successfully');
    }
}
