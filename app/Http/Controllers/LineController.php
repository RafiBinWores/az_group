<?php

namespace App\Http\Controllers;

use App\Http\Requests\Line\LineStoreRequest;
use App\Http\Requests\Line\LineUpdateRequest;
use App\Models\Line;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Yajra\DataTables\Facades\DataTables;

class LineController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view-lines',   only: ['index', 'show']),
            new Middleware('permission:create-lines', only: ['create', 'store']),
            new Middleware('permission:edit-lines',   only: ['edit', 'update']),
            new Middleware('permission:delete-lines', only: ['destroy']),
        ];
    }


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $lines = Line::all();

            return DataTables::of($lines)
                ->addColumn('created_at', function ($line) {
                    return $line->created_at ? $line->created_at->format('M d, Y') : '';
                })
                ->addColumn('status', function ($line) {
                    // Return a checkbox switch (Bootstrap style)
                    $checked = $line->status ? 'checked' : '';
                    return '
                    <div class="form-check form-switch">
                        <input class="form-check-input toggle-status" type="checkbox" data-id="' . $line->id . '" ' . $checked . '>
                        <label class="form-check-label">' . ($line->status ? 'Active' : 'Inactive') . '</label>
                    </div>
                ';
                })
                ->addColumn('actions', function ($line) {
                    return view('lines.partials.actions', compact('line'))->render();
                })
                ->rawColumns(['status', 'actions'])
                ->make(true);
        }

        return view('lines.view');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('lines.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LineStoreRequest $request)
    {
        // Create Garment type
        Line::create([
            'name' => trim($request->name),
            'status' => $request->lineStatus ? 1 : 0,
        ]);

        session()->flash('success', 'Line created successfully.');
        return response()->json([
            'status' => true,
            'message' => 'Line created successfully.',
        ]);
    }

    /**
     * update the status.
     */
    public function updateStatus(Request $request)
    {
        $line = Line::findOrFail($request->id);
        $line->status = $request->status;
        $line->save();

        return response()->json(['status' => true, 'message' => 'Status updated!']);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($line)
    {
        $line = Line::findOrFail($line);

        return view('lines.edit', compact('line'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LineUpdateRequest $request, Line $line)
    {
        $typeChanged = $line->name !== $request->name;
        $statusChanged = $line->status !== (int)$request->lineStatus;

        if (!$typeChanged && !$statusChanged) {
            return response()->json([
                'status' => false,
                'message' => 'Nothing to update.',
            ]);
        }
        // Create 
        $line->update([
            'name' => trim($request->name),
            'status' => $request->lineStatus ? 1 : 0,
        ]);

        session()->flash('success', 'Line updated successfully.');
        return response()->json([
            'status' => true,
            'message' => 'Line updated successfully.',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($line)
    {
        $line = Line::findOrFail($line);

        $line->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'status' => true,
                'message' => 'Line data deleted successfully.'
            ]);
        }

        return redirect()->route('lines.index')->with('success', 'Line data deleted successfully');
    }
}
