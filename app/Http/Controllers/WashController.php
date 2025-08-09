<?php

namespace App\Http\Controllers;

use App\Exports\WashExport;
use App\Http\Requests\Wash\WashStoreRequest;
use App\Http\Requests\Wash\WashUpdateRequest;
use App\Models\Cutting;
use App\Models\Order;
use App\Models\Production;
use App\Models\Wash;
use App\Notifications\WashCreatedNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class WashController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view-washes',   only: ['index', 'show']),
            new Middleware('permission:create-washes', only: ['create', 'store']),
            new Middleware('permission:edit-washes',   only: ['edit', 'update']),
            new Middleware('permission:delete-washes', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $washes = Wash::with('order')->latest();

            if ($request->range) {
                switch ($request->range) {
                    case 'today':
                        $washes->whereDate('date', today());
                        break;
                    case 'this_week':
                        $washes->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()]);
                        break;
                    case 'this_month':
                        $washes->whereMonth('date', now()->month)->whereYear('date', now()->year);
                        break;
                    case 'this_year':
                        $washes->whereYear('date', now()->year);
                        break;
                    case 'last_week':
                        $washes->whereBetween('date', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()]);
                        break;
                    case 'last_month':
                        $washes->whereMonth('date', now()->subMonth()->month)->whereYear('date', now()->subMonth()->year);
                        break;
                    case 'last_year':
                        $washes->whereYear('date', now()->subYear()->year);
                        break;
                    default:
                        // No filtering
                }
            }

            return DataTables::of($washes)
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
                    $arr = is_array($row->wash_data) ? $row->wash_data : json_decode($row->wash_data, true);
                    return collect($arr)->sum('order_qty') ?? 0;
                })
                ->addColumn('total_output_qty', function ($row) {
                    $arr = is_array($row->wash_data) ? $row->wash_data : json_decode($row->wash_data, true);
                    return collect($arr)->sum('output_qty') ?? 0;
                })
                ->addColumn('total_send_qty', function ($row) {
                    $arr = is_array($row->wash_data) ? $row->wash_data : json_decode($row->wash_data, true);
                    return collect($arr)->sum('send') ?? 0;
                })
                ->addColumn('total_receive_qty', function ($row) {
                    $arr = is_array($row->wash_data) ? $row->wash_data : json_decode($row->wash_data, true);
                    return collect($arr)->sum('received') ?? 0;
                })
                ->editColumn('date', function ($row) {
                    return Carbon::parse($row->date)->format('M d, Y');
                })
                ->addColumn('actions', function ($wash) {
                    return view('washes.partials.actions', compact('wash'))->render();
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('washes.view');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $orders = Order::with('garmentTypes')->get();

        // Grab latest Production per order_id (includes production_data JSON)
        $latestProductions = Production::select('id', 'order_id', 'production_data', 'date')
            ->whereIn('order_id', $orders->pluck('id'))
            ->latest('date')
            ->get()
            ->groupBy('order_id')
            ->map->first();

        // Build: [order_id => [ "Army Green" => 320, "Gold" => 100, ... ]]
        $latestProdTotals = $latestProductions->map(function ($prod) {
            $rows = is_array($prod->production_data)
                ? $prod->production_data
                : json_decode($prod->production_data ?: '[]', true);

            $byColor = [];
            foreach ($rows as $row) {
                $color = $row['color'] ?? null;
                $val   = (int) ($row['total_output'] ?? 0);
                if ($color) {
                    $byColor[$color] = ($byColor[$color] ?? 0) + $val;
                }
            }
            return $byColor;
        });

        return view('washes.create', compact('orders', 'latestProdTotals'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(WashStoreRequest $request)
    {
        // Get latest IDs
        $latest = Production::where('order_id', $request->order_id)
            ->where('garment_type', $request->garment_type)
            ->latest('id')->first();

        if (!$latest) {
            // If no cutting report found, return error
            return response()->json([
                'status' => false,
                'message' => 'No cutting report found for this style. Please add cutting first!',
            ]);
        }

        // Create report
        $wash = Wash::create([
            'order_id' => $request->order_id,
            'garment_type' => $request->garment_type,
            'wash_data' => $request->wash_data,
            'date' => $request->date,
        ]);

        $wash = $wash->fresh('order.user');
        $order = $wash->order;
        if ($order && $order->user) {
            $order->user->notify(new WashCreatedNotification($wash));
        }

        session()->flash('success', 'Wash report added successfully.');
        return response()->json([
            'status' => true,
            'message' => 'Wash report added successfully.',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($wash)
    {
        $wash = Wash::with('order')->findOrFail($wash);

        return view('washes.show', compact('wash'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Wash $wash)
    {
        // Load orders with garment types
        $orders = Order::with('garmentTypes')->get();

        // Latest production per order_id (by date desc), including JSON
        $latestProductions = Production::select('id', 'order_id', 'production_data', 'date')
            ->whereIn('order_id', $orders->pluck('id'))
            ->latest('date')
            ->get()
            ->groupBy('order_id')
            ->map->first();

        // Map: [order_id => ['Army Green' => 320, 'Gold' => 100, ...]]
        $latestProdTotals = $latestProductions->map(function ($prod) {
            $rows = is_array($prod->production_data)
                ? $prod->production_data
                : json_decode($prod->production_data ?: '[]', true);

            $byColor = [];
            foreach ($rows as $row) {
                $color = $row['color'] ?? null;
                $val   = (int)($row['total_output'] ?? 0);
                if ($color) {
                    $byColor[$color] = ($byColor[$color] ?? 0) + $val;
                }
            }
            return $byColor;
        });

        return view('washes.edit', compact('wash', 'orders', 'latestProdTotals'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(WashUpdateRequest $request, $wash)
    {
        $wash = Wash::findOrFail($wash);

        // Compare the array directly
        $orderChanged = $wash->order_id != $request->order_id;
        $embroideryChanged = $wash->wash_data != $request->wash_data;
        $dateChanged = $wash->date != $request->date;
        $garmentTypeChanged = $wash->garment_type != $request->garment_type;

        if (!$orderChanged && !$embroideryChanged && !$dateChanged && !$garmentTypeChanged) {
            return response()->json([
                'status' => false,
                'message' => 'Nothing to update.',
            ]);
        }

        // Update report
        $wash->update([
            'order_id' => $request->order_id,
            'wash_data' => $request->wash_data,
            'garment_type' => $request->garment_type,
            'date' => $request->date,
        ]);

        session()->flash('success', 'Wash report updated successfully.');
        return response()->json([
            'status' => true,
            'message' => 'Wash Report update successfully.',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($wash)
    {
        $wash = Wash::findOrFail($wash);

        $wash->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'status' => true,
                'message' => 'Wash report deleted successfully.'
            ]);
        }

        return redirect()->route('washes.index')->with('success', 'wash report deleted successfully');
    }

    // Excel download
    public function exportExcel($wash)
    {
        $wash = Wash::with('order')->findOrFail($wash);
        $fileName = 'wash_report_' . $wash->order->style_no . '_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new WashExport($wash), $fileName);
    }

    // PDF download
    public function exportPdf($wash)
    {
        $wash = Wash::with('order')->findOrFail($wash);

        $pdf = Pdf::loadView('washes.partials.report_pdf', compact('wash'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('wash_report_' . $wash->order->style_no . '_' . now()->format('Ymd_His') . '.pdf');
    }
}
