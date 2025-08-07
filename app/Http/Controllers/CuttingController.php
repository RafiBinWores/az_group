<?php

namespace App\Http\Controllers;

use App\Exports\CuttingExport;
use App\Http\Requests\Cutting\CuttingStoreRequest;
use App\Http\Requests\Cutting\CuttingUpdateRequest;
use App\Models\Cutting;
use App\Models\Order;
use App\Notifications\CuttingCreatedNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class CuttingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $cuttings = Cutting::with('order')->latest();

            if ($request->range) {
                switch ($request->range) {
                    case 'today':
                        $cuttings->whereDate('date', today());
                        break;
                    case 'this_week':
                        $cuttings->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()]);
                        break;
                    case 'this_month':
                        $cuttings->whereMonth('date', now()->month)->whereYear('date', now()->year);
                        break;
                    case 'this_year':
                        $cuttings->whereYear('date', now()->year);
                        break;
                    case 'last_week':
                        $cuttings->whereBetween('date', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()]);
                        break;
                    case 'last_month':
                        $cuttings->whereMonth('date', now()->subMonth()->month)->whereYear('date', now()->subMonth()->year);
                        break;
                    case 'last_year':
                        $cuttings->whereYear('date', now()->subYear()->year);
                        break;
                    default:
                        // No filtering
                }
            }

            return DataTables::of($cuttings)
                ->addIndexColumn()
                ->editColumn('style_no', function ($row) {
                    return $row->order->style_no ?? 'N/A';
                })
                ->editColumn('buyer_name', function ($row) {
                    return $row->order->buyer_name ?? 'N/A';
                })
                ->editColumn('garment_type', function ($row) {
                    return $row->garment_type ?? 'N/A';
                })
                ->addColumn('total_order_qty', function ($row) {
                    $arr = is_array($row->cutting) ? $row->cutting : json_decode($row->cutting, true);
                    return collect($arr)->sum('order_qty') ?? 0;
                })
                ->addColumn('total_cutting_qty', function ($row) {
                    $arr = is_array($row->cutting) ? $row->cutting : json_decode($row->cutting, true);
                    return collect($arr)->sum('cutting_qty') ?? 0;
                })
                ->editColumn('date', function ($row) {
                    return Carbon::parse($row->date)->format('M d, Y');
                })
                ->addColumn('actions', function ($cutting) {
                    return view('cuttings.partials.actions', compact('cutting'))->render();
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('cuttings.view');
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $orders = Order::with('garmentTypes')->latest()->get();

        return view('cuttings.create', compact('orders'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CuttingStoreRequest $request)
    {

        // Create Cutting report
        $cutting = Cutting::create([
            'order_id' => $request->order_id,
            'garment_type' => $request->garment_type,
            'date' => $request->date,
            'cutting' => $request->cutting,
        ]);

        // Eager load order.user
        $cutting = $cutting->fresh('order.user');
        $order = $cutting->order;

        if ($order && $order->user) {
            $order->user->notify(new CuttingCreatedNotification($cutting));
        }


        session()->flash('success', 'Cutting report added successfully.');
        return response()->json([
            'status' => true,
            'message' => 'Cutting report added successfully.',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($cutting)
    {
        $cutting = Cutting::with('order.garmentTypes')->findOrFail($cutting);

        return view('cuttings.show', compact('cutting'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($cutting)
    {
        $cutting = Cutting::findOrFail($cutting);
        $orders = Order::with('garmentTypes')->latest()->get();

        return view('cuttings.edit', compact('cutting', 'orders'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CuttingUpdateRequest $request, $cutting)
    {
        $cutting = Cutting::findOrFail($cutting);

        // Compare the order_id and the cutting array directly
        $orderChanged = $cutting->order_id != $request->order_id;
        $cuttingChanged = $cutting->cutting != $request->cutting;
        $dateChanged = $cutting->date != $request->date;
        $typeChanged = $cutting->garment_type != $request->garment_type;

        if (!$orderChanged && !$cuttingChanged && !$typeChanged && !$dateChanged) {
            return response()->json([
                'status' => false,
                'message' => 'Nothing to update.',
            ]);
        }

        // Create Cutting report
        $cutting->update([
            'order_id' => $request->order_id,
            'garment_type' => $request->garment_type,
            'date' => $request->date,
            'cutting' => $request->cutting,
        ]);

        session()->flash('success', 'Cutting report updated successfully.');
        return response()->json([
            'status' => true,
            'message' => 'Cutting Report update successfully.',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($cutting)
    {
        $cutting = Cutting::findOrFail($cutting);

        $cutting->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'status' => true,
                'message' => 'Cutting report deleted successfully.'
            ]);
        }

        return redirect()->route('cuttings.index')->with('success', 'Cutting report deleted successfully');
    }

    // Excel download
    public function exportExcel($cutting)
    {
        $cutting = Cutting::with('order')->findOrFail($cutting);
        $filename = 'cutting_' . $cutting->order->style_no . '_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new CuttingExport($cutting), $filename);
    }

    // PDF download
    public function exportPdf($order)
    {
        $cutting = Cutting::with('order')->findOrFail($order);

        $pdf = Pdf::loadView('cuttings.partials.report_pdf', compact('cutting'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('cutting_report_' . $cutting->order->style_no . '_' . now()->format('Ymd_His') . '.pdf');
    }
}
