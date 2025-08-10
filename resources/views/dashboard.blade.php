<x-layouts.app>
    <x-slot name="title">Dashboard</x-slot>
    <x-slot name="pageTitle">Dashboard</x-slot>

    <div class="container-fluid">

        @php
        $dirStyles = function (string $direction) {
        return $direction === 'up'
        ? ['text' => 'text-success', 'bg' => 'bg-success-subtle', 'icon' => 'mdi mdi-arrow-up-bold']
        : ['text' => 'text-danger', 'bg' => 'bg-danger-subtle', 'icon' => 'mdi mdi-arrow-down-bold'];
        };

        $formatDate = function ($d) {
        return $d ? \Illuminate\Support\Carbon::parse($d)->format('M d, Y') : '—';
        };
        @endphp


        <div class="row g-3">
            {{-- Cutting --}}
            @can('view-cutting')
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title mt-0 mb-3">Cutting Analytics</h4>
                        <div class="widget-box-2">
                            <div class="widget-detail-2 text-end">
                                <span
                                    class="badge rounded-pill float-start mt-3 {{ $cuttingDirection == 'up' ? 'bg-success' : 'bg-danger' }}">
                                    {{ abs($cuttingChange) }}%
                                    <i class="mdi mdi-trending-{{ $cuttingDirection }}"></i>
                                </span>
                                <h2 class="fw-normal mb-1">{{ number_format($cuttingTotalLatest) }}</h2>
                                <p class="text-muted">
                                    Latest: {{ $formatDate($cuttingLatestDay) }}
                                    <br>Prev: {{ $formatDate($cuttingPrevDay) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endcan

            {{-- Embroidery --}}
            @can('view-embroideries')
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title mt-0 mb-3">Embroidery Analytics</h4>
                        <div class="widget-box-2">
                            <div class="widget-detail-2 text-end">
                                <span
                                    class="badge rounded-pill float-start mt-3 {{ $embDirection == 'up' ? 'bg-success' : 'bg-danger' }}">
                                    {{ abs($embChange) }}%
                                    <i class="mdi mdi-trending-{{ $embDirection }}"></i>
                                </span>
                                <h2 class="fw-normal mb-1">{{ number_format($embTotalLatest) }}</h2>
                                <p class="text-muted">
                                    Latest: {{ $formatDate($embLatestDay) }}
                                    <br>Prev: {{ $formatDate($embPrevDay) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endcan

            {{-- Print --}}
            @can('view-prints')
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title mt-0 mb-3">Print Analytics</h4>
                        <div class="widget-box-2">
                            <div class="widget-detail-2 text-end">
                                <span
                                    class="badge rounded-pill float-start mt-3 {{ $printDirection == 'up' ? 'bg-success' : 'bg-danger' }}">
                                    {{ abs($printChange) }}%
                                    <i class="mdi mdi-trending-{{ $printDirection }}"></i>
                                </span>
                                <h2 class="fw-normal mb-1">{{ number_format($printTotalLatest) }}</h2>
                                <p class="text-muted">
                                    Latest: {{ $formatDate($printLatestDay) }}
                                    <br>Prev: {{ $formatDate($printPrevDay) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endcan

            {{-- Wash --}}
            @can('view-washes')
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title mt-0 mb-3">Wash Analytics</h4>
                        <div class="widget-box-2">
                            <div class="widget-detail-2 text-end">
                                <span
                                    class="badge rounded-pill float-start mt-3 {{ $washDirection == 'up' ? 'bg-success' : 'bg-danger' }}">
                                    {{ abs($washChange) }}%
                                    <i class="mdi mdi-trending-{{ $washDirection }}"></i>
                                </span>
                                <h2 class="fw-normal mb-1">{{ number_format($washTotalLatest) }}</h2>
                                <p class="text-muted">
                                    Latest: {{ $formatDate($washLatestDay) }}
                                    <br>Prev: {{ $formatDate($washPrevDay) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endcan

            {{-- Production --}}
            @can('view-production-report')
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title mt-0 mb-3">Production Analytics</h4>
                        <div class="widget-box-2">
                            <div class="widget-detail-2 text-end">
                                <span
                                    class="badge rounded-pill float-start mt-3 {{ $prodDirection == 'up' ? 'bg-success' : 'bg-danger' }}">
                                    {{ abs($prodChange) }}%
                                    <i class="mdi mdi-trending-{{ $prodDirection }}"></i>
                                </span>
                                <h2 class="fw-normal mb-1">{{ number_format($prodTotalLatest) }}</h2>
                                <p class="text-muted">
                                    Latest: {{ $formatDate($prodLatestDay) }}
                                    <br>Prev: {{ $formatDate($prodPrevDay) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endcan

            {{-- Finishing --}}
            @can('view-finishing-report')
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title mt-0 mb-3">Finishing Analytics</h4>
                        <div class="widget-box-2">
                            <div class="widget-detail-2 text-end">
                                <span
                                    class="badge rounded-pill float-start mt-3 {{ $finDirection == 'up' ? 'bg-success' : 'bg-danger' }}">
                                    {{ abs($finChange) }}%
                                    <i class="mdi mdi-trending-{{ $finDirection }}"></i>
                                </span>
                                <h2 class="fw-normal mb-1">{{ number_format($finTotalLatest) }}</h2>
                                <p class="text-muted">
                                    Latest: {{ $formatDate($finLatestDay) }}<br>
                                    Prev: {{ $formatDate($finPrevDay) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endcan

        </div>



        {{-- <div class="row">
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <div class="dropdown float-end">
                            <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="mdi mdi-dots-vertical"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item">Action</a>
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item">Another action</a>
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item">Something else</a>
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item">Separated link</a>
                            </div>
                        </div>

                        <h4 class="header-title mt-0">Daily Sales</h4>

                        <div class="widget-chart text-center">
                            <div id="morris-donut-example" dir="ltr" style="height: 245px;" class="morris-chart">
                            </div>
                            <ul class="list-inline chart-detail-list mb-0">
                                <li class="list-inline-item">
                                    <h5 style="color: #ff8acc;"><i class="fa fa-circle me-1"></i>Series A</h5>
                                </li>
                                <li class="list-inline-item">
                                    <h5 style="color: #5b69bc;"><i class="fa fa-circle me-1"></i>Series B</h5>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div><!-- end col -->

            <div class="col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <div class="dropdown float-end">
                            <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="mdi mdi-dots-vertical"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item">Week</a>
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item">Month</a>
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item">Year</a>
                                <!-- item-->

                            </div>
                        </div>
                        <h4 class="header-title mt-0">Statistics</h4>
                        <div id="morris-bar-example" dir="ltr" style="height: 280px;" class="morris-chart">
                        </div>
                    </div>
                </div>
            </div><!-- end col -->

            <div class="col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <div class="dropdown float-end">
                            <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="mdi mdi-dots-vertical"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item">Action</a>
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item">Another action</a>
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item">Something else</a>
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item">Separated link</a>
                            </div>
                        </div>
                        <h4 class="header-title mt-0">Total Revenue</h4>
                        <div id="morris-line-example" dir="ltr" style="height: 280px;" class="morris-chart">
                        </div>
                    </div>
                </div>
            </div><!-- end col -->
        </div> --}}
        <!-- end row -->


        <div class="row">
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-body">

                        <h4 class="header-title mb-3">Latest user</h4>

                        <div class="inbox-widget">

                            @foreach($users as $user)
                            <div class="inbox-item">
                                <div>
                                    @if ($user->avatar)
                                    <div class="inbox-item-img">
                                        <img src="{{ asset('storage/' . $user->avatar) }}" class="rounded-circle"
                                            alt="{{ $user->name }}">
                                    </div>
                                    @else
                                    <div class="inbox-item-img">
                                        <span class="badge bg-secondary rounded-circle"
                                            style="width:40px; height:40px; display:inline-flex; align-items:center; justify-content:center;">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </span>
                                    </div>
                                    @endif

                                    <h5 class="inbox-item-author mt-0 mb-1">{{ $user->name }}</h5>
                                    <p class="inbox-item-text text-muted">{{ $user->email }}</p>
                                    <p class="inbox-item-date">{{ $user->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                            @endforeach

                        </div>
                    </div>
                </div>

            </div><!-- end col -->

            <div class="col-xl-8">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title mt-0 mb-3">Latest orders</h4>

                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Buyer Name</th>
                                        <th>Style No</th>
                                        <th>Order Quantity</th>
                                        <th>Garment Type</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($orders as $order)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $order->buyer_name ?? 'N/A' }}</td>
                                        <td>
                                            @can('view-orders')
                                            @if(Route::has('orders.show'))
                                            <a href="{{ route('orders.show', $order->id) }}"
                                                class="text-decoration-none">
                                                {{ $order->style_no ?? 'N/A' }}
                                            </a>
                                            @else
                                            {{ $order->style_no ?? 'N/A' }}
                                            @endif
                                            @endcan
                                        </td>
                                        <td>{{ number_format((int)($order->order_qty ?? 0)) }}</td>
                                        <td>
                                            {{ optional($order->garmentTypes)->pluck('name')->join(', ') ?: 'N/A' }}
                                        </td>
                                        <td>{{ ($order->date ?? $order->created_at)?->format('M d, Y') ?? '—' }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No recent orders found.</td>
                                    </tr>
                                    @endforelse

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div><!-- end col -->

        </div>
        <!-- end row -->

    </div>

</x-layouts.app>