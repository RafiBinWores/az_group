<?php

namespace App\Http\Controllers;

use App\Models\Cutting;
use App\Models\Order;
use App\Models\Production;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ProductionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $productions = Production::with('order')->latest();

            if ($request->range) {
                switch ($request->range) {
                    case 'today':
                        $productions->whereDate('date', today());
                        break;
                    case 'this_week':
                        $productions->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()]);
                        break;
                    case 'this_month':
                        $productions->whereMonth('date', now()->month)->whereYear('date', now()->year);
                        break;
                    case 'this_year':
                        $productions->whereYear('date', now()->year);
                        break;
                    case 'last_week':
                        $productions->whereBetween('date', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()]);
                        break;
                    case 'last_month':
                        $productions->whereMonth('date', now()->subMonth()->month)->whereYear('date', now()->subMonth()->year);
                        break;
                    case 'last_year':
                        $productions->whereYear('date', now()->subYear()->year);
                        break;
                    default:
                        // No filtering
                }
            }

            return DataTables::of($productions)
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
                    $arr = is_array($row->print_data) ? $row->print_data : json_decode($row->print_data, true);
                    return collect($arr)->sum('order_qty') ?? 0;
                })
                ->addColumn('total_send_qty', function ($row) {
                    $arr = is_array($row->print_data) ? $row->print_data : json_decode($row->print_data, true);
                    return collect($arr)->sum('send') ?? 0;
                })
                ->addColumn('total_receive_qty', function ($row) {
                    $arr = is_array($row->print_data) ? $row->print_data : json_decode($row->print_data, true);
                    return collect($arr)->sum('received') ?? 0;
                })
                ->editColumn('date', function ($row) {
                    return Carbon::parse($row->date)->format('M d, Y');
                })
                ->addColumn('actions', function ($production) {
                    return view('productions.partials.actions', compact('production'))->render();
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('productions.view');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $orders = Order::with('garmentTypes')->get();

        // Get latest cutting per order (map by order_id)
        $latestCuttings = Cutting::latest('date')->get()->groupBy('order_id')->map->first();

        return view('productions.create', compact('orders', 'latestCuttings'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PrintReportStoreRequest $request)
    {
        // Get latest IDs
        $latestCutting = Cutting::where('order_id', $request->order_id)
            ->where('garment_type', $request->garment_type)
            ->latest('id')->first();

        if (!$latestCutting) {
            // If no cutting report found, return error
            return response()->json([
                'status' => false,
                'message' => 'No cutting report found for this style. Please add cutting first!',
            ]);
        }

        // Create report
        $print = PrintReport::create([
            'order_id' => $request->order_id,
            'garment_type' => $request->garment_type,
            'print_data' => $request->print_data,
            'date' => $request->date,
        ]);

        $print = $print->fresh('order.user');
        $order = $print->order;
        if ($order && $order->user) {
            $order->user->notify(new PrintCreatedNotification($print));
        }

        session()->flash('success', 'Print report added successfully.');
        return response()->json([
            'status' => true,
            'message' => 'Print report added successfully.',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($print)
    {
        $print = PrintReport::with('order')->findOrFail($print);

        return view('productions.show', compact('print'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($print)
    {
        $print = PrintReport::findOrFail($print);
        $orders = Order::with(['garmentTypes'])->get();

        $latestCuttings = Cutting::latest('date')->get()->groupBy('order_id')->map->first();

        return view('productions.edit', compact('print', 'orders', 'latestCuttings'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PrintReportUpdateRequest $request, $print)
    {
        $print = PrintReport::findOrFail($print);

        // Compare the array directly
        $orderChanged = $print->order_id != $request->order_id;
        $embroideryChanged = $print->print_data != $request->print_data;
        $dateChanged = $print->date != $request->date;
        $garmentTypeChanged = $print->garment_type != $request->garment_type;

        if (!$orderChanged && !$embroideryChanged && !$dateChanged && !$garmentTypeChanged) {
            return response()->json([
                'status' => false,
                'message' => 'Nothing to update.',
            ]);
        }

        // Update report
        $print->update([
            'order_id' => $request->order_id,
            'print_data' => $request->print_data,
            'garment_type' => $request->garment_type,
            'date' => $request->date,
        ]);

        session()->flash('success', 'Print report updated successfully.');
        return response()->json([
            'status' => true,
            'message' => 'Print Report update successfully.',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($print)
    {
        $print = PrintReport::findOrFail($print);

        $print->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'status' => true,
                'message' => 'Print report deleted successfully.'
            ]);
        }

        return redirect()->route('prints.index')->with('success', 'Print report deleted successfully');
    }

    // Excel download
    public function exportExcel($print)
    {
        $print = PrintReport::with('order')->findOrFail($print);
        $fileName = 'print_report_' . $print->order->style_no . '_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new PrintExport($print), $fileName);
    }

    // PDF download
    public function exportPdf($print)
    {
        $print = PrintReport::with('order')->findOrFail($print);

        $pdf = Pdf::loadView('productions.partials.report_pdf', compact('print'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('print_report_' . $print->order->style_no . '_' . now()->format('Ymd_His') . '.pdf');
    }
}
