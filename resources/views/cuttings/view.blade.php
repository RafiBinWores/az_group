<x-layouts.app>
    <x-slot name="title">Cuttings</x-slot>
    <x-slot name="pageTitle">Cuttings</x-slot>

    @push('styles')
        <link href="{{ asset('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet"
            type="text/css" />
        <link href="{{ asset('assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}"
            rel="stylesheet" type="text/css" />
    @endpush

    <div class="card">
        <div class="card-body">
            <div class="row justify-content-between">
                <div class="col-md-4">
                    <div class="mt-3 mt-md-0">
                        <a href="{{ route('cuttings.create') }}" class="btn btn-success waves-effect waves-light"><i
                                class="mdi mdi-plus-circle me-1"></i> Add</a>
                    </div>
                </div><!-- end col-->
                <div class="col-md-8">
                    <form class="d-flex flex-wrap align-items-center justify-content-sm-end">
                        <label for="filter" class="me-2">Sort By</label>
                        <div class="me-sm-2">
                            <select class="form-select my-1 my-md-0" id="filter" name="range">
                                <option selected="">All</option>
                                <option value="today">Today</option>
                                <option value="this_week">This week</option>
                                <option value="this_month">This month</option>
                                <option value="this_year">This year</option>
                                <option value="last_week">Last week</option>
                                <option value="last_month">Last month</option>
                                <option value="last_year">Last year</option>
                            </select>
                        </div>
                    </form>
                </div>
            </div> <!-- end row -->
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="mt-0 header-title mb-3">Order List</h4>

                    <table id="table" class="table dt-responsive table-responsive align-middle">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Style No</th>
                                <th>Buyer</th>
                                <th>Garment Type</th>
                                <th>Order Qty</th>
                                <th>Cutting Qty</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

        </div>
    </div> <!-- end row -->

    @push('scripts')
        <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
        <script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('assets/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js') }}"></script>

        <!-- Datatables init -->
        <script src="{{ asset('assets/js/pages/datatables.init.js') }}"></script>
        <script>
            var table;

            $(function() {
                table = $('#table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('cuttings.index') }}",
                        data: function(d) {
                            d.range = $('#filter').val();
                        }
                    },
                    order: [
                        [0, 'desc']
                    ],
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'order.style_no',
                            name: 'order.style_no'
                        },
                        {
                            data: 'order.buyer_name',
                            name: 'order.buyer_name'
                        },
                        {
                            data: 'garment_type',
                            name: 'garment_type',
                        },
                        {
                            data: 'total_order_qty',
                            name: 'total_order_qty'
                        },
                        {
                            data: 'total_cutting_qty',
                            name: 'total_cutting_qty'
                        },
                        {
                            data: 'date',
                            name: 'date'
                        },
                        {
                            data: 'actions',
                            name: 'actions',
                            orderable: false,
                            searchable: false
                        },
                    ]
                });
                $('#filter').change(function() {
                    table.ajax.reload();
                });
            });

            // Handle Delete Click
            $(document).on('click', '.delete-btn', function() {
                let url = $(this).data('url');

                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        axios.post(url, {
                                _method: "DELETE",
                                _token: $('meta[name="csrf-token"]').attr('content')
                            })
                            .then(function(response) {
                                Swal.fire("Deleted!", response.data.message || "Deleted successfully!",
                                    "success");
                                table.ajax.reload(null, false);
                            })
                            .catch(function(error) {
                                Swal.fire("Error!", error.response?.data?.message ||
                                    "Something went wrong.", "error");
                            });
                    }
                });
            });
        </script>
    @endpush
</x-layouts.app>
