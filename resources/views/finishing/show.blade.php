<x-layouts.app>
    <x-slot name="title">Finishing Details</x-slot>
    <x-slot name="pageTitle">Finishing Details</x-slot>

    <div class="card">
        <div class="card-body">
            <div class="dropdown float-end">
                <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <i class="mdi mdi-dots-vertical"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-end" style="">
                    <a href="{{ route('finishing.exportPdf', $finishing->id) }}" class="dropdown-item">Pdf Export</a>
                    <!-- item-->
                    <a href="{{ route('finishing.exportExcel', $finishing->id) }}" class="dropdown-item">Excel Export</a>
                    <!-- item-->
                    <a href="{{ route('finishing.edit', $finishing->id) }}" class="dropdown-item">Edit Report</a>
                </div>
            </div>
            <h4 class="mt-0">Buyer: <span class="text-primary">{{ $finishing->order->buyer_name }}</span></h4>
            <h4 class="mt-0 header-title">Style: <span class="text-primary">{{ $finishing->order->style_no }}</span></h4>
            <p class="text-muted font-14 mb-3">
                <strong>Order Quantity:</strong> {{ $finishing->order->order_qty }}<br>
                <strong>Date:</strong> {{ $finishing->date }}<br>
            </p>

            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Thread Cutting</th>
                            <th>QC Check</th>
                            <th>Button & Rivet Attach</th>
                            <th>Iron</th>
                            <th>Hangtag</th>
                            <th>Poly</th>
                            <th>Carton</th>
                            <th>Today Finishing</th>
                            <th>Total FInishing</th>
                            <th>Plan To Complete</th>
                            <th>DPI Inline</th>
                            <th>FRI Final</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $sn =1;
                        @endphp
                            <tr>
                                <th scope="row">{{ $sn++ }}</th>
                                <td>{{ $finishing->thread_cutting ?? 'N/A' }}</td>
                            <td>{{ $finishing->qc_check ?? 'N/A' }}</td>
                            <td>{{ $finishing->button_rivet_attach ?? 'N/A' }}</td>
                            <td>{{ $finishing->iron ?? 'N/A' }}</td>
                            <td>{{ $finishing->hangtag ?? 'N/A' }}</td>
                            <td>{{ $finishing->poly ?? 'N/A' }}</td>
                            <td>{{ $finishing->carton ?? 'N/A' }}</td>
                            <td>{{ $finishing->today_finishing ?? 'N/A' }}</td>
                            <td>{{ $finishing->total_finishing ?? 'N/A' }}</td>
                            <td>{{ $finishing->plan_to_complete ?? 'N/A' }}</td>
                            <td>{{ $finishing->dpi_inline ?? 'N/A' }}</td>
                            <td>{{ $finishing->fri_final ?? 'N/A' }}</td>
                            <td>{{ $finishing->date ?? 'N/A' }}</td>
                            </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
