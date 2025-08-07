<?php

namespace App\Http\Controllers;

use App\Exports\OrderReportExport;
use App\Http\Requests\Order\OrderStoreRequest;
use App\Http\Requests\Order\OrderUpdateRequest;
use App\Models\GarmentType;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $orders = Order::query()->latest();

            return DataTables::of($orders)
                ->addColumn('created_at', function ($order) {
                    return $order->created_at ? $order->created_at->format('M d, Y') : '';
                })
                ->addColumn('actions', function ($order) {
                    return view('orders.partials.actions', compact('order'))->render();
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('orders.view');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $types = GarmentType::where('status', 1)->latest()->get();

        return view('orders.create', compact('types'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OrderStoreRequest $request)
    {
        // Create Order
        $order = Order::create([
            'user_id' => $request->user_id,
            'buyer_name' => $request->buyer_name,
            'style_no' => $request->style_no,
            'order_qty' => $request->order_quantity,
            'color_qty' => $request->color_qty,
        ]);

        $order->garmentTypes()->sync($request->garment_types);

        session()->flash('success', 'Order created successfully.');
        return response()->json([
            'status' => true,
            'message' => 'Order added successfully.',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($order)
    {
        $order = Order::findOrFail($order);

        return view('orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($order)
    {

        $order = Order::findOrFail($order);
        $types = GarmentType::where('status', 1)->latest()->get();

        if ($order->user_id !== Auth::user()->id) {
            session()->flash('error', 'You are not authorized to edit this order.');
            return redirect()->route('orders.index');
            abort(403, 'Unauthorized action.');
        }

        return view('orders.edit', compact('order', 'types'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(OrderUpdateRequest $request, $order)
    {
        $order = Order::findOrFail($order);

        if ($order->user_id !== Auth::user()->id) {
            session()->flash('error', 'You are not authorized to edit this order.');
            return redirect()->route('orders.index');
            abort(403, 'Unauthorized action.');
        }

        // Gate::authorize('update', $order);

        // Check if anything has actually changed
        $userChanged = $order->user_id != $request->user_id;
        $buyerChanged = $order->buyer_name !== $request->buyer_name;
        $styleChanged = $order->style_no !== $request->style_no;
        $qtyChanged = $order->order_qty != $request->order_quantity;
        $colorChanged = $order->color_qty != $request->color_qty;

        // Compare garment types
        $existingGarments = $order->garmentTypes->pluck('id')->sort()->values()->toArray();
        $newGarments = collect($request->garment_types)->sort()->values()->toArray();
        $garmentsChanged = $existingGarments !== $newGarments;

        if (!$userChanged && !$buyerChanged && !$styleChanged && !$qtyChanged && !$colorChanged && !$garmentsChanged) {
            return response()->json([
                'status' => false,
                'message' => 'Nothing to update.',
            ]);
        }

        // Create user
        $order->update([
            'user_id' => $request->user_id,
            'buyer_name' => $request->buyer_name,
            'style_no' => $request->style_no,
            'order_qty' => $request->order_quantity,
            'color_qty' => $request->color_qty,
        ]);

        $order->garmentTypes()->sync($request->garment_types);

        session()->flash('success', 'Order updated successfully.');
        return response()->json([
            'status' => true,
            'message' => 'Order update successfully.',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $order = Order::findOrFail($id);

        if ($order->user_id !== auth()->id()) {
            // For web (non-AJAX), set flash and redirect
            if (!request()->ajax() && !request()->wantsJson()) {
                session()->flash('error', 'You are not authorized to delete this order.');
                return redirect()->route('orders.index');
            }

            // For AJAX, return 403 JSON
            return response()->json(['status' => false, 'message' => 'You are not authorized to delete this order.'], 403);
        }

        $order->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'status' => true,
                'message' => 'Order deleted successfully.'
            ]);
        }

        return redirect()->route('orders.index')->with('success', 'Order deleted successfully');
    }

    // Excel download
    public function export($order)
    {
        $order = Order::findOrFail($order);
        $filename = 'order_' . $order->style_no . '_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new OrderReportExport($order), $filename);
    }

    // PDF download
    public function exportPdf($order)
    {
        $order = Order::with('garmentTypes')->findOrFail($order);

        $pdf = Pdf::loadView('orders.partials.report_pdf', compact('order'));
        return $pdf->download('order_report_' . $order->style_no . '_' . now()->format('Ymd_His') . '.pdf');
    }
}
