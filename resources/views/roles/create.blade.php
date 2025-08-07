<x-layouts.app>
    <x-slot name="title">Role Create</x-slot>
    <x-slot name="pageTitle">Role Create</x-slot>

    <form action="{{ route('roles.store') }}" method="POST" id="form" class="needs-validation" novalidate>
        @csrf
        <div class="card">
            <div class="card-body">
                <div class="mb-3">
                    <label for="name" class="form-label">Role Name</label>
                    <input type="text" class="form-control" id="name" name="name"
                        placeholder="Enter your role name">
                    <div class="invalid-feedback name-error"></div>
                </div>
                <div class="mb-3">
                    <div class="d-flex align-items-center mb-2">
                        <span class="me-2 fw-semibold">Assign Permissions</span>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="selectAllPermissions">
                            <label class="form-check-label user-select-none" for="selectAllPermissions">
                                Select All
                            </label>
                        </div>
                    </div>
                    <div class="row g-2 border p-2" id="permissionsList">
                        @foreach ($permissions as $permission)
                            <div class="col-12 col-md-6 col-lg-3">
                                <div class="form-check">
                                    <input class="form-check-input permission-checkbox" type="checkbox"
                                        value="{{ $permission->name }}" name="permissions[]"
                                        id="permission_{{ $permission->id }}">
                                    <label class="form-check-label" for="permission_{{ $permission->id }}">
                                        {{ $permission->name }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="invalid-feedback permissions-error d-block"></div>
                </div>

                <button class="btn btn-primary me-2" type="submit">Create <i
                        class="mdi mdi-file-document-outline"></i></button>
                <a href="{{ route('roles.index') }}" class="btn btn-secondary">Cancel <i class="mdi mdi-close"></i></a>
            </div>
        </div>
    </form>

    @push('scripts')
        <script>
            // Permission Select All and Individual Checkbox Logic
            function syncSelectAllCheckbox() {
                $('#selectAllPermissions').prop(
                    'checked',
                    $('.permission-checkbox').length > 0 &&
                    $('.permission-checkbox:checked').length === $('.permission-checkbox').length
                );
            }

            function clearPermissionsError() {
                $('.permissions-error').removeClass('invalid-feedback text-danger').html('');
                $('.permission-checkbox').removeClass('is-invalid');
            }

            function clearNameError() {
                $('.name-error').removeClass('invalid-feedback text-danger').html('');
                $('[name="name"]').removeClass('is-invalid');
            }

            $(document).ready(function() {
                syncSelectAllCheckbox();

                $('#selectAllPermissions').on('change', function() {
                    $('.permission-checkbox').prop('checked', this.checked);
                    clearPermissionsError();
                });

                $('#permissionsList').on('change', '.permission-checkbox', function() {
                    syncSelectAllCheckbox();
                    clearPermissionsError();
                });

                $('#form').submit(function(event) {
                    event.preventDefault();
                    let form = this;
                    let formData = new FormData(form);
                    $('button[type="submit"]').prop('disabled', true);

                    axios.post(form.action, formData, {
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'Content-Type': 'multipart/form-data'
                        }
                    }).then(function(response) {
                        $('button[type="submit"]').prop('disabled', false);

                        clearNameError();
                        clearPermissionsError();

                        if (response.data.status) {
                            window.location.href = "{{ route('roles.index') }}";
                        } else if (response.data.errors) {
                            
                            // Handle custom backend error format (optional, for BC)
                            let errors = response.data.errors || {};
                            if (errors.name) {
                                $('[name="name"]').addClass('is-invalid');
                                $('.name-error').addClass('invalid-feedback text-danger').html(errors
                                    .name[0]);
                            }
                            if (errors.permissions) {
                                $('.permissions-error').addClass('invalid-feedback text-danger').html(
                                    errors.permissions[0]);
                                $('.permission-checkbox').addClass('is-invalid');
                            }
                        }
                    }).catch(function(error) {
                        $('button[type="submit"]').prop('disabled', false);

                        // Handle Laravel FormRequest validation errors (422)
                        if (error.response && error.response.status === 422 && error.response.data
                            .errors) {
                            let errors = error.response.data.errors;
                            clearNameError();
                            clearPermissionsError();

                            // Name error
                            if (errors.name) {
                                $('[name="name"]').addClass('is-invalid');
                                $('.name-error').addClass('invalid-feedback text-danger').html(errors
                                    .name[0]);
                            }
                            // Permissions error
                            if (errors.permissions) {
                                $('.permissions-error').addClass('invalid-feedback text-danger').html(
                                    errors.permissions[0]);
                                $('.permission-checkbox').addClass('is-invalid');
                            }
                        } else {
                            // Other errors (network, server, etc)
                            let msg = "Something went wrong. Please try again.";
                            alert(msg);
                        }
                    });
                });

                // Remove error on name field input
                $('[name="name"]').on('input', clearNameError);
            });
        </script>
    @endpush
</x-layouts.app>
