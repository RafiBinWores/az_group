<?php

namespace App\Http\Controllers;

use App\Exports\EmbroideryExport;
use App\Http\Requests\Embroidery\EmbroideryStoreRequest;
use App\Http\Requests\Embroidery\EmbroideryUpdateRequest;
use App\Models\Cutting;
use App\Models\Embroidery;
use App\Models\Order;
use App\Notifications\EmbroideryCreatedNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class EmbroideryController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view-embroideries',   only: ['index', 'show']),
            new Middleware('permission:create-embroideries', only: ['create', 'store']),
            new Middleware('permission:edit-embroideries',   only: ['edit', 'update']),
            new Middleware('permission:delete-embroideries', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    // public function index(Request $request)
    // {
    //     if ($request->ajax()) {
    //         $embroideries = Embroidery::with('order')->latest();

    //         if ($request->range) {
    //             switch ($request->range) {
    //                 case 'today':
    //                     $embroideries->whereDate('date', today());
    //                     break;
    //                 case 'this_week':
    //                     $embroideries->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()]);
    //                     break;
    //                 case 'this_month':
    //                     $embroideries->whereMonth('date', now()->month)->whereYear('date', now()->year);
    //                     break;
    //                 case 'this_year':
    //                     $embroideries->whereYear('date', now()->year);
    //                     break;
    //                 case 'last_week':
    //                     $embroideries->whereBetween('date', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()]);
    //                     break;
    //                 case 'last_month':
    //                     $embroideries->whereMonth('date', now()->subMonth()->month)->whereYear('date', now()->subMonth()->year);
    //                     break;
    //                 case 'last_year':
    //                     $embroideries->whereYear('date', now()->subYear()->year);
    //                     break;
    //                 default:
    //                     // No filtering
    //             }
    //         }

    //         return DataTables::of($embroideries)
    //             ->addIndexColumn()
    //             ->editColumn('style_no', function ($row) {
    //                 return $row->order->style_no ?? 'N/A';
    //             })
    //             ->editColumn('buyer_name', function ($row) {
    //                 return $row->order->buyer_name ?? 'N/A';
    //             })
    //             ->editColumn('garment_type', function ($row) {
    //                 return $row->garment_type ?? 'N/A';
    //             })
    //             ->addColumn('total_order_qty', function ($row) {
    //                 $arr = is_array($row->embroidery_data) ? $row->embroidery_data : json_decode($row->embroidery_data, true);
    //                 return collect($arr)->sum('order_qty') ?? 0;
    //             })
    //             ->addColumn('total_send_qty', function ($row) {
    //                 $arr = is_array($row->embroidery_data) ? $row->embroidery_data : json_decode($row->embroidery_data, true);
    //                 return collect($arr)->sum('send') ?? 0;
    //             })
    //             ->addColumn('total_receive_qty', function ($row) {
    //                 $arr = is_array($row->embroidery_data) ? $row->embroidery_data : json_decode($row->embroidery_data, true);
    //                 return collect($arr)->sum('received') ?? 0;
    //             })
    //             ->editColumn('date', function ($row) {
    //                 return Carbon::parse($row->date)->format('M d, Y');
    //             })
    //             ->addColumn('actions', function ($embroidery) {
    //                 return view('embroideries.partials.actions', compact('embroidery'))->render();
    //             })
    //             ->rawColumns(['actions'])
    //             ->make(true);
    //     }

    //     return view('embroideries.view');
    // }

public function index(Request $request)
{
    if ($request->ajax()) {
        $q = Embroidery::with('order')->latest('date');

        // Client-side filtering param (optional)
        if ($request->filled('range')) {
            switch ($request->range) {
                case 'today':
                    $q->whereDate('date', today());
                    break;
                case 'this_week':
                    $q->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'this_month':
                    $q->whereMonth('date', now()->month)->whereYear('date', now()->year);
                    break;
                case 'this_year':
                    $q->whereYear('date', now()->year);
                    break;
                case 'last_week':
                    $q->whereBetween('date', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()]);
                    break;
                case 'last_month':
                    $q->whereMonth('date', now()->subMonth()->month)->whereYear('date', now()->subMonth()->year);
                    break;
                case 'last_year':
                    $q->whereYear('date', now()->subYear()->year);
                    break;
                // default: no filtering
            }
        }

        $rows = $q->get()->map(function ($row) {
            $arr = is_array($row->embroidery_data)
                ? $row->embroidery_data
                : (json_decode($row->embroidery_data, true) ?: []);

            $totalOrder   = collect($arr)->sum(fn ($r) => (int)($r['order_qty'] ?? 0));
            $totalSend    = collect($arr)->sum(fn ($r) => (int)($r['send'] ?? 0));
            $totalReceive = collect($arr)->sum(fn ($r) => (int)($r['received'] ?? 0));

            return [
                'style_no'           => optional($row->order)->style_no ?? 'N/A',
                'buyer_name'         => optional($row->order)->buyer_name ?? 'N/A',
                'garment_type'       => $row->garment_type ?? 'N/A',
                'total_order_qty'    => $totalOrder,
                'total_send_qty'     => $totalSend,
                'total_receive_qty'  => $totalReceive,
                'date'               => $row->date ? Carbon::parse($row->date)->format('M d, Y') : '',
                'actions'            => view('embroideries.partials.actions', ['embroidery' => $row])->render(),
            ];
        });

        // DataTables client-side expects { data: [...] }
        return response()->json(['data' => $rows]);
    }

    return view('embroideries.view');
}


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $orders = Order::with('garmentTypes')->get();

        // Get latest cutting per order (map by order_id)
        $latestCuttings = Cutting::latest('date')->get()->groupBy('order_id')->map->first();

        return view('embroideries.create', compact('orders', 'latestCuttings'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EmbroideryStoreRequest $request)
    {
        // Get latest IDs
        $latestEmbroidery = Cutting::where('order_id', $request->order_id)
            ->where('garment_type', $request->garment_type)
            ->latest('id')->first();

        if (!$latestEmbroidery) {
            // If no cutting report found, return error
            return response()->json([
                'status' => false,
                'message' => 'No cutting report found for this style. Please add cutting first!',
            ]);
        }

        // Create report
        $embroidery = Embroidery::create([
            'order_id' => $request->order_id,
            'garment_type' => $request->garment_type,
            'embroidery_data' => $request->embroidery_data,
            'date' => $request->date,
        ]);

        $embroidery = $embroidery->fresh('order.user');
        $order = $embroidery->order;
        if ($order && $order->user) {
            $order->user->notify(new EmbroideryCreatedNotification($embroidery));
        }

        session()->flash('success', 'Embroidery report added successfully.');
        return response()->json([
            'status' => true,
            'message' => 'Embroidery report added successfully.',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($embroidery)
    {
        $embroidery = Embroidery::with('order')->findOrFail($embroidery);

        return view('embroideries.show', compact('embroidery'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($embroidery)
    {
        $embroidery = Embroidery::findOrFail($embroidery);
        $orders = Order::with(['garmentTypes'])->get();

        $latestCuttings = Cutting::latest('date')->get()->groupBy('order_id')->map->first();

        return view('embroideries.edit', compact('embroidery', 'orders', 'latestCuttings'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EmbroideryUpdateRequest $request, $embroidery)
    {
        $embroideryPrint = Embroidery::findOrFail($embroidery);

        // Compare the array directly
        $orderChanged = $embroideryPrint->order_id != $request->order_id;
        $embroideryChanged = $embroideryPrint->embroidery_data != $request->embroidery_data;
        $dateChanged = $embroideryPrint->date != $request->date;
        $garmentTypeChanged = $embroideryPrint->garment_type != $request->garment_type;

        if (!$orderChanged && !$embroideryChanged && !$dateChanged && !$garmentTypeChanged) {
            return response()->json([
                'status' => false,
                'message' => 'Nothing to update.',
            ]);
        }

        // Update report
        $embroideryPrint->update([
            'order_id' => $request->order_id,
            'embroidery_data' => $request->embroidery_data,
            'garment_type' => $request->garment_type,
            'date' => $request->date,
        ]);

        session()->flash('success', 'Embroidery report updated successfully.');
        return response()->json([
            'status' => true,
            'message' => 'Embroidery Report update successfully.',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($embroidery)
    {
        $embroidery = Embroidery::findOrFail($embroidery);

        $embroidery->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'status' => true,
                'message' => 'Embroidery report deleted successfully.'
            ]);
        }

        return redirect()->route('embroideries.index')->with('success', 'Embroidery report deleted successfully');
    }

    // Excel download
    public function exportExcel($embroidery)
    {
        $embroidery = Embroidery::with('order')->findOrFail($embroidery);
        $fileName = 'embroidery_report_' . $embroidery->order->style_no . '_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new EmbroideryExport($embroidery), $fileName);
    }

    // PDF download
    public function exportPdf($embroidery)
    {
        $embroidery = Embroidery::with('order')->findOrFail($embroidery);

        $pdf = Pdf::loadView('embroideries.partials.report_pdf', compact('embroidery'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('embroidery_report_' . $embroidery->order->style_no . '_' . now()->format('Ymd_His') . '.pdf');
    }
}
