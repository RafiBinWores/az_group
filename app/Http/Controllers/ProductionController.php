<?php

namespace App\Http\Controllers;

use App\Exports\ProductionExport;
use App\Http\Requests\Production\ProductionStoreRequest;
use App\Http\Requests\Production\ProductionUpdateRequest;
use App\Models\Cutting;
use App\Models\Factory;
use App\Models\Line;
use App\Models\Order;
use App\Models\Production;
use App\Notifications\ProductionCreatedNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class ProductionController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view-production-report',   only: ['index', 'show']),
            new Middleware('permission:create-production-report', only: ['create', 'store']),
            new Middleware('permission:edit-production-report',   only: ['edit', 'update']),
            new Middleware('permission:delete-production-report', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    // public function index(Request $request)
    // {
    //     if ($request->ajax()) {
    //         $productions = Production::with('order')->latest();

    //         if ($request->range) {
    //             switch ($request->range) {
    //                 case 'today':
    //                     $productions->whereDate('date', today());
    //                     break;
    //                 case 'this_week':
    //                     $productions->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()]);
    //                     break;
    //                 case 'this_month':
    //                     $productions->whereMonth('date', now()->month)->whereYear('date', now()->year);
    //                     break;
    //                 case 'this_year':
    //                     $productions->whereYear('date', now()->year);
    //                     break;
    //                 case 'last_week':
    //                     $productions->whereBetween('date', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()]);
    //                     break;
    //                 case 'last_month':
    //                     $productions->whereMonth('date', now()->subMonth()->month)->whereYear('date', now()->subMonth()->year);
    //                     break;
    //                 case 'last_year':
    //                     $productions->whereYear('date', now()->subYear()->year);
    //                     break;
    //                 default:
    //                     // No filtering
    //             }
    //         }

    //         return DataTables::of($productions)
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
    //             ->addColumn('total_cutting_qty', function ($row) {
    //                 $arr = is_array($row->production_data) ? $row->production_data : json_decode($row->production_data, true);
    //                 return collect($arr)->sum('cutting_qty') ?? 0;
    //             })
    //             ->addColumn('total_order_qty', function ($row) {
    //                 $arr = is_array($row->production_data) ? $row->production_data : json_decode($row->production_data, true);
    //                 return collect($arr)->sum('order_qty') ?? 0;
    //             })
    //             ->addColumn('input_qty', function ($row) {
    //                 $arr = is_array($row->production_data) ? $row->production_data : json_decode($row->production_data, true);
    //                 return collect($arr)->sum('input') ?? 0;
    //             })
    //             ->addColumn('total_input_qty', function ($row) {
    //                 $arr = is_array($row->production_data) ? $row->production_data : json_decode($row->production_data, true);
    //                 return collect($arr)->sum('total_input') ?? 0;
    //             })
    //             ->addColumn('output_qty', function ($row) {
    //                 $arr = is_array($row->production_data) ? $row->production_data : json_decode($row->production_data, true);
    //                 return collect($arr)->sum('output') ?? 0;
    //             })
    //             ->addColumn('total_output_qty', function ($row) {
    //                 $arr = is_array($row->production_data) ? $row->production_data : json_decode($row->production_data, true);
    //                 return collect($arr)->sum('total_output') ?? 0;
    //             })
    //             ->editColumn('date', function ($row) {
    //                 return Carbon::parse($row->date)->format('M d, Y');
    //             })
    //             ->addColumn('actions', function ($production) {
    //                 return view('productions.partials.actions', compact('production'))->render();
    //             })
    //             ->rawColumns(['actions'])
    //             ->make(true);
    //     }

    //     return view('productions.view');
    // }


    public function index(Request $request)
    {
        if ($request->ajax()) {
            $q = Production::query()
                ->with(['order:id,style_no,buyer_name'])
                ->select(['id', 'order_id', 'garment_type', 'date', 'production_data'])
                ->latest('date');

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
                        // default: no filter
                }
            }

            $rows = $q->get()->map(function ($row) {
                $arr = is_array($row->production_data)
                    ? $row->production_data
                    : (json_decode($row->production_data, true) ?: []);

                $sum = fn(string $key) => collect($arr)->sum(fn($r) => (int)($r[$key] ?? 0));

                return [
                    'style_no'          => optional($row->order)->style_no ?? 'N/A',
                    'buyer_name'        => optional($row->order)->buyer_name ?? 'N/A',
                    'garment_type'      => $row->garment_type ?? 'N/A',
                    'total_order_qty'   => $sum('order_qty'),
                    'total_cutting_qty' => $sum('cutting_qty'),
                    'input_qty'         => $sum('input'),
                    'total_input_qty'   => $sum('total_input'),
                    'output_qty'        => $sum('output'),
                    'total_output_qty'  => $sum('total_output'),
                    'date'              => $row->date ? Carbon::parse($row->date)->format('M d, Y') : '',
                    'actions'           => view('productions.partials.actions', ['production' => $row])->render(),
                ];
            });

            return response()->json(['data' => $rows]);
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

        $factories = Factory::all();
        $lines = Line::all();

        return view('productions.create', compact('orders', 'latestCuttings', 'factories', 'lines'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductionStoreRequest $request)
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
        $production = Production::create([
            'order_id' => $request->order_id,
            'garment_type' => $request->garment_type,
            'production_data' => $request->production_data,
            'date' => $request->date,
        ]);

        $production = $production->fresh('order.user');
        $order = $production->order;
        if ($order && $order->user) {
            $order->user->notify(new ProductionCreatedNotification($production));
        }

        session()->flash('success', 'Production report added successfully.');
        return response()->json([
            'status' => true,
            'message' => 'Production report added successfully.',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($production)
    {
        $production = Production::with('order')->findOrFail($production);

        return view('productions.show', compact('production'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($production)
    {
        $production = Production::findOrFail($production);
        $orders = Order::with(['garmentTypes'])->get();

        $latestCuttings = Cutting::latest('date')->get()->groupBy('order_id')->map->first();
        $factories = Factory::all();
        $lines = Line::all();

        return view('productions.edit', compact('production', 'orders', 'latestCuttings', 'factories', 'lines'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductionUpdateRequest $request, $production)
    {
        $production = Production::findOrFail($production);

        // Compare the array directly
        $orderChanged = $production->order_id != $request->order_id;
        $embroideryChanged = $production->production_data != $request->production_data;
        $dateChanged = $production->date != $request->date;
        $garmentTypeChanged = $production->garment_type != $request->garment_type;

        if (!$orderChanged && !$embroideryChanged && !$dateChanged && !$garmentTypeChanged) {
            return response()->json([
                'status' => false,
                'message' => 'Nothing to update.',
            ]);
        }

        // Update report
        $production->update([
            'order_id' => $request->order_id,
            'production_data' => $request->production_data,
            'garment_type' => $request->garment_type,
            'date' => $request->date,
        ]);

        session()->flash('success', 'Production report updated successfully.');
        return response()->json([
            'status' => true,
            'message' => 'Production Report update successfully.',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($production)
    {
        $production = Production::findOrFail($production);

        $production->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'status' => true,
                'message' => 'Production report deleted successfully.'
            ]);
        }

        return redirect()->route('productions.index')->with('success', 'Production report deleted successfully');
    }

    // Excel download
    public function exportExcel($production)
    {
        $production = Production::with('order')->findOrFail($production);
        $fileName = 'production_report_' . $production->order->style_no . '_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new ProductionExport($production), $fileName);
    }

    // PDF download
    public function exportPdf($production)
    {
        $production = Production::with('order')->findOrFail($production);

        $pdf = Pdf::loadView('productions.partials.report_pdf', compact('production'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('production_report_' . $production->order->style_no . '_' . now()->format('Ymd_His') . '.pdf');
    }
}
