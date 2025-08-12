<?php

namespace App\Http\Controllers;

use App\Exports\FinishingExport;
use App\Exports\FinisingExport;
use App\Http\Requests\Finishing\FinishingStoreRequest;
use App\Http\Requests\Finishing\FinishingUpdateRequest;
use App\Models\Finishing;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class FinishingController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view-finishing-report',   only: ['index', 'show']),
            new Middleware('permission:create-finishing-report', only: ['create', 'store']),
            new Middleware('permission:edit-finishing-report',   only: ['edit', 'update']),
            new Middleware('permission:delete-finishing-report', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    // public function index(Request $request)
    // {
    //     if ($request->ajax()) {
    //         $finishing = Finishing::with('order')->select([
    //             'id',
    //             'user_id',
    //             'order_id',
    //             'thread_cutting',
    //             'qc_check',
    //             'button_rivet_attach',
    //             'iron',
    //             'hangtag',
    //             'poly',
    //             'carton',
    //             'today_finishing',
    //             'total_finishing',
    //             'plan_to_complete',
    //             'dpi_inline',
    //             'fri_final',
    //             'date',
    //         ])->latest();

    //         if ($request->range) {
    //             switch ($request->range) {
    //                 case 'today':
    //                     $finishing->whereDate('date', today());
    //                     break;
    //                 case 'this_week':
    //                     $finishing->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()]);
    //                     break;
    //                 case 'this_month':
    //                     $finishing->whereMonth('date', now()->month)->whereYear('date', now()->year);
    //                     break;
    //                 case 'this_year':
    //                     $finishing->whereYear('date', now()->year);
    //                     break;
    //                 case 'last_week':
    //                     $finishing->whereBetween('date', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()]);
    //                     break;
    //                 case 'last_month':
    //                     $finishing->whereMonth('date', now()->subMonth()->month)->whereYear('date', now()->subMonth()->year);
    //                     break;
    //                 case 'last_year':
    //                     $finishing->whereYear('date', now()->subYear()->year);
    //                     break;
    //                 default:
    //                     // No filtering
    //             }
    //         }

    //         return DataTables::of($finishing)
    //             ->addIndexColumn()
    //             ->editColumn('buyer_name', function ($row) {
    //                 return $row->order->buyer_name ?? 'N/A';
    //             })
    //             ->editColumn('style_no', function ($row) {
    //                 return $row->order->style_no ?? 'N/A';
    //             })
    //             ->editColumn('thread_cutting', function ($row) {
    //                 return $row->thread_cutting ?? 'N/A';
    //             })
    //             ->editColumn('qc_check', function ($row) {
    //                 return $row->qc_check ?? 'N/A';
    //             })
    //             ->editColumn('button_rivet_attach', function ($row) {
    //                 return $row->button_rivet_attach ?? 'N/A';
    //             })
    //             ->editColumn('iron', function ($row) {
    //                 return $row->iron ?? 'N/A';
    //             })
    //             ->editColumn('hangtag', function ($row) {
    //                 return $row->hangtag ?? 'N/A';
    //             })
    //             ->editColumn('poly', function ($row) {
    //                 return $row->poly ?? 'N/A';
    //             })
    //             ->editColumn('carton', function ($row) {
    //                 return $row->carton ?? 'N/A';
    //             })
    //             ->editColumn('today_finishing', function ($row) {
    //                 return $row->today_finishing ?? 'N/A';
    //             })
    //             ->editColumn('total_finishing', function ($row) {
    //                 return $row->total_finishing ?? 'N/A';
    //             })
    //             ->editColumn('plan_to_complete', function ($row) {
    //                 return $row->plan_to_complete ?? 'N/A';
    //             })
    //             ->editColumn('dpi_inline', function ($row) {
    //                 return $row->dpi_inline ?? 'N/A';
    //             })
    //             ->editColumn('fri_final', function ($row) {
    //                 return $row->fri_final ?? 'N/A';
    //             })
    //             ->editColumn('date', function ($row) {
    //                 return Carbon::parse($row->date)->format('M d, Y');
    //             })
    //             ->addColumn('actions', function ($finishing) {
    //                 return view('finishing.partials.actions', compact('finishing'))->render();
    //             })
    //             ->rawColumns(['actions'])
    //             ->make(true);
    //     }

    //     return view('finishing.view');
    // }

    public function index(Request $request)
{
    if ($request->ajax()) {
        $q = Finishing::query()
            ->with(['order:id,style_no,buyer_name'])
            ->select([
                'id',
                'user_id',
                'order_id',
                'thread_cutting',
                'qc_check',
                'button_rivet_attach',
                'iron',
                'hangtag',
                'poly',
                'carton',
                'today_finishing',
                'total_finishing',
                'plan_to_complete',
                'dpi_inline',
                'fri_final',
                'date',
            ])
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
            $fmt = fn($v) => ($v === null || $v === '') ? 'N/A' : $v;

            return [
                'buyer_name'       => optional($row->order)->buyer_name ?? 'N/A',
                'style_no'         => optional($row->order)->style_no ?? 'N/A',
                'thread_cutting'   => $fmt($row->thread_cutting),
                'qc_check'         => $fmt($row->qc_check),
                'button_rivet_attach' => $fmt($row->button_rivet_attach),
                'iron'             => $fmt($row->iron),
                'hangtag'          => $fmt($row->hangtag),
                'poly'             => $fmt($row->poly),
                'carton'           => $fmt($row->carton),
                'today_finishing'  => $fmt($row->today_finishing),
                'total_finishing'  => $fmt($row->total_finishing),
                'plan_to_complete' => $fmt($row->plan_to_complete),
                'dpi_inline'       => $fmt($row->dpi_inline),
                'fri_final'        => $fmt($row->fri_final),
                'date'             => $row->date ? Carbon::parse($row->date)->format('M d, Y') : '',
                'actions'          => view('finishing.partials.actions', ['finishing' => $row])->render(),
            ];
        });

        return response()->json(['data' => $rows]);
    }

    return view('finishing.view');
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $orders = Order::all();

        return view('finishing.create', compact('orders'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FinishingStoreRequest $request)
    {
        // Create Order
        Finishing::create([
            'user_id' => $request->user_id,
            'order_id' => $request->order_id,
            'thread_cutting' => $request->thread_cutting,
            'qc_check' => $request->qc_check,
            'button_rivet_attach' => $request->button_rivet_attach,
            'iron' => $request->iron,
            'hangtag' => $request->hangtag,
            'poly' => $request->poly,
            'carton' => $request->carton,
            'today_finishing' => $request->today_finishing,
            'total_finishing' => $request->total_finishing,
            'plan_to_complete' => $request->plan_to_complete,
            'dri_inline' => $request->dri_inline,
            'fri_final' => $request->fri_final,
            'date' => $request->date,
        ]);

        session()->flash('success', 'Finishing report created successfully.');
        return response()->json([
            'status' => true,
            'message' => 'Finishing report added successfully.',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($finishing)
    {
        $finishing = Finishing::findOrFail($finishing);

        $order = Order::all();

        return view('finishing.show', compact('order', 'finishing'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($finishing)
    {
        $finishing = Finishing::findOrFail($finishing);

        $orders = Order::all();

        return view('finishing.edit', compact('finishing', 'orders'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FinishingUpdateRequest $request, $finishing)
    {
        $finishing = finishing::findOrFail($finishing);
        $data = $request->validated();

        // Fill, detect changes, then save
        $finishing->fill($data);

        if (! $finishing->isDirty()) {
            return response()->json([
                'status'  => false,
                'message' => 'Nothing to update.',
            ]);
        }

        // Create user
        $finishing->update([
            'user_id' => $request->user_id,
            'order_id' => $request->order_id,
            'thread_cutting' => $request->thread_cutting,
            'qc_check' => $request->qc_check,
            'button_rivet_attach' => $request->button_rivet_attach,
            'iron' => $request->iron,
            'hangtag' => $request->hangtag,
            'poly' => $request->poly,
            'carton' => $request->carton,
            'today_finishing' => $request->today_finishing,
            'total_finishing' => $request->total_finishing,
            'plan_to_complete' => $request->plan_to_complete,
            'dri_inline' => $request->dri_inline,
            'fri_final' => $request->fri_final,
            'date' => $request->date,
        ]);

        session()->flash('success', 'Finishing report updated successfully.');
        return response()->json([
            'status' => true,
            'message' => 'Finishing report update successfully.',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $finishing = Finishing::findOrFail($id);

        $finishing->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'status' => true,
                'message' => 'Finishing report deleted successfully.'
            ]);
        }
        return redirect()->route('finishing.index')->with('success', 'Finishing report deleted successfully');
    }

    // Excel download
    public function exportExcel($finishing)
    {
        $finishing = Finishing::with('order')->findOrFail($finishing);
        $filename = 'finishing_' . $finishing->order->style_no . '_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new FinishingExport($finishing), $filename);
    }

    // PDF download
    public function exportPdf($id)
    {
        $finishing = Finishing::with('order.garmentTypes')->findOrFail($id);

        $pdf = Pdf::loadView('finishing.partials.report_pdf', compact('finishing'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('finishing_report_' . ($finishing->order->style_no ?? 'N_A') . '_' . now()->format('Ymd_His') . '.pdf');
    }
}
