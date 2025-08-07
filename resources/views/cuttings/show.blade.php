<x-layouts.app>
    <x-slot name="title">CUtting Details</x-slot>
    <x-slot name="pageTitle">CUtting Details</x-slot>

    <div class="card">
        <div class="card-body">
            <div class="dropdown float-end">
                <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <i class="mdi mdi-dots-vertical"></i>
                </a>
                 <div class="dropdown-menu dropdown-menu-end" style="">
                    <a href="{{ route('cuttings.exportPdf', $cutting->id) }}" class="dropdown-item">Pdf Export</a>
                    <!-- item-->
                    <a href="{{ route('cuttings.exportExcel', $cutting->id) }}" class="dropdown-item">Excel Export</a>
                    <!-- item-->
                    <a href="{{ route('cuttings.edit', $cutting->id) }}" class="dropdown-item">Edit Order</a>
                </div>
            </div>
            <h4 class="mt-0">Buyer: <span class="text-primary">{{ $cutting->order->buyer_name }}</span></h4>
            <h4 class="mt-0 header-title">Style: <span class="text-primary">{{ $cutting->order->style_no }}</span></h4>
            <p class="text-muted font-14 mb-3">
                <strong>Garments Type:</strong>
                {{ $cutting->order->garmentTypes->pluck('name')->join(', ') ?: 'N/A' }}<br>
                <strong>Order Quantity:</strong> {{ collect($cutting->cutting)->sum('order_qty') }}<br>
                <strong>Date:</strong> {{ $cutting->created_at->format('M d, Y') }}<br>
            </p>

            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Color</th>
                            <th>Order Quantity</th>
                            <th>Cutting Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $sn = 1;
                            $totalCuttingQty = collect($cutting->cutting)->sum('cutting_qty');
                            $totalOrderQty = collect($cutting->cutting)->sum('order_qty');
                        @endphp
                        @foreach ($cutting->cutting as $row)
                            <tr>
                                <th scope="row">{{ $sn++ }}</th>
                                <td>{{ $row['color'] }}</td>
                                <td>{{ $row['order_qty'] }}</td>
                                <td>{{ $row['cutting_qty'] }}</td>
                            </tr>
                        @endforeach
                        <tr class="fw-bold">
                            <th colspan="2" class="text-center">Total</th>
                            {{-- <td></td> --}}
                            <td>{{ $totalOrderQty }}</td>
                            <td>{{ $totalCuttingQty }}</td>


                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
