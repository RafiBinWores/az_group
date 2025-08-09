<?php

namespace App\Http\Controllers;

use App\Http\Requests\GarmentType\GarmentTypeStoreRequest;
use App\Http\Requests\GarmentType\GarmentTypeUpdateRequest;
use App\Models\GarmentType;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class GarmentTypeController extends Controller implements HasMiddleware
{
        public static function middleware(): array
    {
        return [
            new Middleware('permission:view-garments',   only: ['index', 'show']),
            new Middleware('permission:create-garments', only: ['create', 'store']),
            new Middleware('permission:edit-garments',   only: ['edit', 'update']),
            new Middleware('permission:delete-garments', only: ['destroy']),
        ];
    }


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $garmentTypes = GarmentType::all();

            return DataTables::of($garmentTypes)
                ->addColumn('created_at', function ($garment) {
                    return $garment->created_at ? $garment->created_at->format('M d, Y') : '';
                })
                ->addColumn('status', function ($garment) {
                    // Return a checkbox switch (Bootstrap style)
                    $checked = $garment->status ? 'checked' : '';
                    return '
                    <div class="form-check form-switch">
                        <input class="form-check-input toggle-status" type="checkbox" data-id="' . $garment->id . '" ' . $checked . '>
                        <label class="form-check-label">' . ($garment->status ? 'Active' : 'Inactive') . '</label>
                    </div>
                ';
                })
                ->addColumn('actions', function ($garment) {
                    return view('garments.partials.actions', compact('garment'))->render();
                })
                ->rawColumns(['status', 'actions'])
                ->make(true);
        }

        return view('garments.view');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('garments.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(GarmentTypeStoreRequest $request)
    {
        // Create Garment type
        GarmentType::create([
            'name' => trim($request->garment_type),
            'status' => $request->garmentTypeStatus ? 1 : 0,
        ]);

        session()->flash('success', 'Garment type created successfully.');
        return response()->json([
            'status' => true,
            'message' => 'Garment types created successfully.',
        ]);
    }

    /**
     * update the status.
     */
    public function updateStatus(Request $request)
    {
        $garment = GarmentType::findOrFail($request->id);
        $garment->status = $request->status;
        $garment->save();

        return response()->json(['status' => true, 'message' => 'Status updated!']);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($garment_type)
    {
        $garment = GarmentType::findOrFail($garment_type);

        return view('garments.edit', compact('garment'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(GarmentTypeUpdateRequest $request,GarmentType $garment_type)
    {
        $typeChanged = $garment_type->name !== $request->garment_type;
        $statusChanged = $garment_type->status !== (int)$request->garmentTypeStatus;

        if (!$typeChanged && !$statusChanged) {
            return response()->json([
                'status' => false,
                'message' => 'Nothing to update.',
            ]);
        }
        // Create Garment type
        $garment_type->update([
            'name' => trim($request->garment_type),
            'status' => $request->garmentTypeStatus ? 1 : 0,
        ]);

        session()->flash('success', 'Garment type updated successfully.');
        return response()->json([
            'status' => true,
            'message' => 'Garment types updated successfully.',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($garment_type)
    {
        $type = GarmentType::findOrFail($garment_type);

        $type->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'status' => true,
                'message' => 'Garment types deleted successfully.'
            ]);
        }

        return redirect()->route('garment_types.index')->with('success', 'Garment types deleted successfully');
    }
}
