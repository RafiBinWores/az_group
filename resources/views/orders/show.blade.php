<x-layouts.app>
    <x-slot name="title">Order Details</x-slot>
    <x-slot name="pageTitle">Order Details</x-slot>

    <div class="card">
        <div class="card-body">
            <div class="dropdown float-end">
                <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <i class="mdi mdi-dots-vertical"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-end" style="">
                    <a href="{{ route('orders.exportPdf', $order->id) }}" class="dropdown-item">Pdf Export</a>
                    <!-- item-->
                    <a href="{{ route('orders.export', $order->id) }}" class="dropdown-item">Excel Export</a>
                    <!-- item-->
                    <a href="{{ route('orders.edit', $order->id) }}" class="dropdown-item">Edit Order</a>
                </div>
            </div>
            <h4 class="mt-0">Buyer: <span class="text-primary">{{ $order->buyer_name }}</span></h4>
            <h4 class="mt-0 header-title">Style: <span class="text-primary">{{ $order->style_no }}</span></h4>
            <p class="text-muted font-14 mb-3">
                <strong>Garments Type:</strong> {{ $order->garmentTypes->pluck('name')->join(', ') ?: 'N/A' }}<br>
                <strong>Order Quantity:</strong> {{ $order->order_qty }}<br>
                <strong>Date:</strong> {{ $order->created_at->format('M d, Y') }}<br>
            </p>

            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Color</th>
                            <th>Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalQty = collect($order->color_qty ?? [])->sum('qty');
                            $sn = 1;
                        @endphp
                        @foreach ($order->color_qty as $row)
                            <tr>
                                <th scope="row">{{ $sn++ }}</th>
                                <td>{{ $row['color'] }}</td>
                                <td>{{ $row['qty'] }}</td>
                            </tr>
                        @endforeach
                        <tr class="fw-bold">
                            <th colspan="2" class="text-center">Total</th>
                            {{-- <td></td> --}}
                            <td>{{ $totalQty }}</td>

                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
