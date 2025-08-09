<x-layouts.app>
    <x-slot name="title">Production Details</x-slot>
    <x-slot name="pageTitle">Production Details</x-slot>

    <div class="card">
        <div class="card-body">
            <div class="dropdown float-end">
                <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <i class="mdi mdi-dots-vertical"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-end" style="">
                    <a href="{{ route('productions.exportPdf', $production->id) }}" class="dropdown-item">Pdf Export</a>
                    <!-- item-->
                    <a href="{{ route('productions.exportExcel', $production->id) }}" class="dropdown-item">Excel
                        Export</a>
                    <!-- item-->
                    <a href="{{ route('productions.edit', $production->id) }}" class="dropdown-item">Edit Report</a>
                </div>
            </div>
            <h4 class="mt-0">Buyer: <span class="text-primary">{{ $production->order->buyer_name }}</span></h4>
            <h4 class="mt-0 header-title">Style: <span class="text-primary">{{ $production->order->style_no }}</span>
            </h4>
            <p class="text-muted font-14 mb-3">
                <strong>Garments Type:</strong>
                {{ $production->order->garmentTypes->pluck('name')->join(', ') ?: 'N/A' }}<br>
                <strong>Order Quantity:</strong> {{ $production->order->order_qty }}<br>
                <strong>Date:</strong> {{ $production->date }}<br>
            </p>

            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                                                        <th style="">Factory</th>
                            <th style="">Line</th>
                            <th>Color</th>
                            <th style="">Order Quantity</th>
                            <th style="">Cutting Quantity</th>
                            <th>Input</th>
                            <th>Total Input</th>
                            <th>Output</th>
                            <th>Total Output</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $sn = 1;
                            $totalOrderQty = collect($production->production_data)->sum('order_qty');
                            $totalCuttingQty = collect($production->production_data)->sum('cutting_qty');
                            $InputQty = collect($production->production_data)->sum('input');
                            $totalInputQty = collect($production->production_data)->sum('total_input');
                            $outputQty = collect($production->production_data)->sum('output');
                            $totalOutputQty = collect($production->production_data)->sum('total_output');

                        @endphp
                        @foreach ($production->production_data as $row)
                            <tr>
                                <th scope="row">{{ $sn++ }}</th>
                                                                <td>{{ $row['factory'] }}</td>
                                <td>{{ $row['line'] }}</td>
                                <td>{{ $row['color'] }}</td>
                                <td>{{ $row['order_qty'] }}</td>
                                <td>{{ $row['cutting_qty'] }}</td>
                                <td>{{ $row['input'] }}</td>
                                <td>{{ $row['total_input'] }}</td>
                                <td>{{ $row['output'] }}</td>
                                <td>{{ $row['total_output'] }}</td>
                            </tr>
                        @endforeach
                        <tr class="fw-bold">
                            <th colspan="4" class="text-center">Total</th>
                            {{-- <td></td> --}}
                            <td>{{ $totalOrderQty }}</td>
                            <td>{{ $totalCuttingQty }}</td>
                            <td>{{ $InputQty }}</td>
                            <td>{{ $totalInputQty }}</td>
                            <td>{{ $outputQty }}</td>
                            <td>{{ $totalOutputQty }}</td>
                            {{-- <td>{{ $totalReceiveQty }}</td> --}}
                            {{-- <td>{{ $totalReceiveQty }}</td> --}}
                            {{-- <td>{{ $totalReceiveQty }}</td> --}}
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
