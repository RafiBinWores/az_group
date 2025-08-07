<x-layouts.app>
    <x-slot name="title">Print Details</x-slot>
    <x-slot name="pageTitle">Print Details</x-slot>

    <div class="card">
        <div class="card-body">
            <div class="dropdown float-end">
                <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <i class="mdi mdi-dots-vertical"></i>
                </a>
                 <div class="dropdown-menu dropdown-menu-end" style="">
                    <a href="{{ route('prints.exportPdf', $print->id) }}" class="dropdown-item">Pdf Export</a>
                    <!-- item-->
                    <a href="{{ route('prints.exportExcel', $print->id) }}" class="dropdown-item">Excel Export</a>
                    <!-- item-->
                    <a href="{{ route('prints.edit', $print->id) }}" class="dropdown-item">Edit Order</a>
                </div>
            </div>
            <h4 class="mt-0">Buyer: <span class="text-primary">{{ $print->order->buyer_name }}</span></h4>
            <h4 class="mt-0 header-title">Style: <span class="text-primary">{{ $print->order->style_no }}</span></h4>
            <p class="text-muted font-14 mb-3">
                <strong>Garments Type:</strong>
                {{ $print->order->garmentTypes->pluck('name')->join(', ') ?: 'N/A' }}<br>
                <strong>Order Quantity:</strong> {{ $print->order->order_qty }}<br>
                <strong>Date:</strong> {{ $print->date }}<br>
            </p>

            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th style="min-width: 200px;">Color</th>
                            <th style="min-width: 150px;">Order Quantity</th>
                            <th style="min-width: 150px;">Cutting Quantity</th>
                            <th>Send</th>
                            <th>Received</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $sn = 1;
                            $totalOrderQty = collect($print->print_data)->sum('order_qty');
                            $totalCuttingQty = collect($print->print_data)->sum('cutting_qty');
                            $totalSendQty = collect($print->print_data)->sum('send');
                            $totalReceiveQty = collect($print->print_data)->sum('received');
                            
                        @endphp
                        @foreach ($print->print_data as $row)
                            <tr>
                                <th scope="row">{{ $sn++ }}</th>
                                <td>{{ $row['color'] }}</td>
                                <td>{{ $row['order_qty'] }}</td>
                                <td>{{ $row['cutting_qty'] }}</td>
                                <td>{{ $row['send'] }}</td>
                                <td>{{ $row['received'] }}</td>
                            </tr>
                        @endforeach
                        <tr class="fw-bold">
                            <th colspan="2" class="text-center">Total</th>
                            {{-- <td></td> --}}
                            <td>{{ $totalOrderQty }}</td>
                            <td>{{ $totalCuttingQty }}</td>
                            <td>{{ $totalSendQty }}</td>
                            <td>{{ $totalReceiveQty }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
