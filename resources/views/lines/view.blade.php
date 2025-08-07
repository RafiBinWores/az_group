<x-layouts.app>
    <x-slot name="title">Line</x-slot>
    <x-slot name="pageTitle">Line</x-slot>

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
                        <a href="{{ route('lines.create') }}"
                            class="btn btn-success waves-effect waves-light"><i class="mdi mdi-plus-circle me-1"></i>
                            Add</a>
                    </div>
                </div><!-- end col-->
                {{-- <div class="col-md-8">
                    <form class="d-flex flex-wrap align-items-center justify-content-sm-end">
                        <label for="status-select" class="me-2">Sort By</label>
                        <div class="me-sm-2">
                            <select class="form-select my-1 my-md-0" id="status-select">
                                <option selected="">All</option>
                                <option value="1">Name</option>
                                <option value="2">Post</option>
                                <option value="3">Followers</option>
                                <option value="4">Followings</option>
                            </select>
                        </div>
                    </form>
                </div> --}}
            </div> <!-- end row -->
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="mt-0 header-title mb-3">Garment List</h4>

                    <table id="table" class="table dt-responsive table-responsive align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Line Name</th>
                                <th>Status</th>
                                <th>Created At</th>
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
                    ajax: '{{ route('lines.index') }}',
                    order: [
                        [0, 'desc']
                    ],
                    columns: [{
                            data: 'id',
                            name: 'id'
                        },
                        {
                            data: 'name',
                            name: 'name',
                        },
                        {
                            data: 'status',
                            name: 'status',
                        },
                        {
                            data: 'created_at',
                            name: 'created_at',
                            width: '100px',
                        },
                        {
                            data: 'actions',
                            name: 'actions',
                            orderable: false,
                            searchable: false,
                        }
                    ]
                });
            });

            $(document).on('change', '.toggle-status', function() {
                let id = $(this).data('id');
                let checked = $(this).is(':checked') ? 1 : 0;

                axios.post('/lines/update-status', {
                        id: id,
                        status: checked
                    }, {
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    })
                    .then(function(response) {
                        $('#table').DataTable().ajax.reload(null, false);
                         Swal.fire({
                                toast: true,
                                position: 'top-right',
                                icon: response.data.status ? 'success' : 'warning',
                                title: response.data.message,
                                showConfirmButton: false,
                                timer: 2500,
                                timerProgressBar: true,
                                customClass: {
                                    popup: 'colored-toast'
                                }
                            });

                    })
                    .catch(function() {
                        showToast('error', 'Failed to update status');
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
