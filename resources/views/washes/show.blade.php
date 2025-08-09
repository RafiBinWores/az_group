<x-layouts.app>
    <x-slot name="title">wash Details</x-slot>
    <x-slot name="pageTitle">wash Details</x-slot>

    <div class="card">
        <div class="card-body">
            <div class="dropdown float-end">
                <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <i class="mdi mdi-dots-vertical"></i>
                </a>
                 <div class="dropdown-menu dropdown-menu-end" style="">
                    <a href="{{ route('washes.exportPdf', $wash->id) }}" class="dropdown-item">Pdf Export</a>
                    <!-- item-->
                    <a href="{{ route('washes.exportExcel', $wash->id) }}" class="dropdown-item">Excel Export</a>
                    <!-- item-->
                    <a href="{{ route('washes.edit', $wash->id) }}" class="dropdown-item">Edit Report</a>
                </div>
            </div>
            <h4 class="mt-0">Buyer: <span class="text-primary">{{ $wash->order->buyer_name }}</span></h4>
            <h4 class="mt-0 header-title">Style: <span class="text-primary">{{ $wash->order->style_no }}</span></h4>
            <p class="text-muted font-14 mb-3">
                <strong>Garments Type:</strong>
                {{ $wash->order->garmentTypes->pluck('name')->join(', ') ?: 'N/A' }}<br>
                <strong>Order Quantity:</strong> {{ $wash->order->order_qty }}<br>
                <strong>Date:</strong> {{ $wash->date }}<br>
            </p>

            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th style="min-width: 200px;">Color</th>
                            <th style="min-width: 150px;">Order Quantity</th>
                            <th style="min-width: 150px;">Production Quantity</th>
                            <th>Send</th>
                            <th>Received</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $sn = 1;
                            $totalOrderQty = collect($wash->wash_data)->sum('order_qty');
                            $totalCuttingQty = collect($wash->wash_data)->sum('output_qty');
                            $totalSendQty = collect($wash->wash_data)->sum('send');
                            $totalReceiveQty = collect($wash->wash_data)->sum('received');
                            
                        @endphp
                        @foreach ($wash->wash_data as $row)
                            <tr>
                                <th scope="row">{{ $sn++ }}</th>
                                <td>{{ $row['color'] }}</td>
                                <td>{{ $row['order_qty'] }}</td>
                                <td>{{ $row['output_qty'] }}</td>
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
