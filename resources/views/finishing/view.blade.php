<x-layouts.app>
    <x-slot name="title">Finishing</x-slot>
    <x-slot name="pageTitle">Finishing</x-slot>

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
                    @can('edit-finishing-report')
                        <div class="mt-3 mt-md-0">
                        <a href="{{ route('finishing.create') }}" class="btn btn-success waves-effect waves-light"><i
                                class="mdi mdi-plus-circle me-1"></i> Add</a>
                    </div>
                    @endcan
                </div><!-- end col-->
                <div class="col-md-8">
                    <form class="d-flex flex-wrap align-items-center justify-content-sm-end">
                        <label for="range" class="me-2">Sort By</label>
                        <div class="me-sm-2">
                            <select class="form-select my-1 my-md-0" id="range" name="range">
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
                    <h4 class="mt-0 header-title mb-3">Finishing List</h4>

                    <table id="table" class="table dt-responsive table-responsive align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Buyer Name</th>
                                <th>Style No</th>
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
            const table = $('#table').DataTable({
                ajax: {
                    url: '{{ route('finishing.index') }}',
                    data: function(d) {
                        d.range = $('#range').val(); // pass filter
                    }
                },
                columns: [{
                        data: null,
                        render: (d, t, r, meta) => meta.row + 1,
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'style_no'
                    },
                    {
                        data: 'buyer_name'
                    },
                    {
                        data: 'thread_cutting'
                    },
                    {
                        data: 'qc_check'
                    },
                    {
                        data: 'button_rivet_attach'
                    },
                    {
                        data: 'iron'
                    },
                    {
                        data: 'hangtag'
                    },
                    {
                        data: 'poly'
                    },
                    {
                        data: 'carton'
                    },
                    {
                        data: 'today_finishing'
                    },
                    {
                        data: 'total_finishing'
                    },
                    {
                        data: 'plan_to_complete'
                    },
                    {
                        data: 'dpi_inline'
                    },
                    {
                        data: 'fri_final'
                    },
                    {
                        data: 'date'
                    },
                    {
                        data: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            $('#range').on('change', () => table.ajax.reload());
        </script>
        <script>
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
