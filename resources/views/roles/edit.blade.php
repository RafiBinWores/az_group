<x-layouts.app>
    <x-slot name="title">Role Edit</x-slot>
    <x-slot name="pageTitle">Role Edit</x-slot>

    <form action="{{ route('roles.update', $role->id) }}" method="POST" id="form" class="needs-validation" novalidate>
        @csrf
        @method('PUT')
        <div class="card">
            <div class="card-body">
                <h4 class="mb-3 header-title">Basic Information</h4>
                <div class="mb-3">
                    <label for="name" class="form-label">Role Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ $role->name }}"
                        placeholder="Enter your role name" required>
                    <div class="invalid-feedback name-error"></div>
                </div>
                <div class="mb-3">
                    <div class="d-flex align-items-center mb-2">
                        <span class="me-2 fw-semibold">Assign Permissions</span>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="selectAllPermissions">
                            <label class="form-check-label user-select-none" for="selectAllPermissions">Select
                                All</label>
                        </div>
                    </div>
                    <div class="row g-2 border p-2 pt-1" id="permissionsList">
                        @foreach ($permissions as $permission)
                            <div class="col-12 col-md-6 col-lg-3">
                                <div class="form-check">
                                    <input class="form-check-input permission-checkbox" type="checkbox"
                                        value="{{ $permission->name }}" name="permissions[]"
                                        id="permission_{{ $permission->id }}"
                                        {{ $role->permissions->pluck('name')->contains($permission->name) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="permission_{{ $permission->id }}">
                                        {{ $permission->name }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="invalid-feedback permissions-error d-block mt-1"></div>
                </div>
                <button class="btn btn-primary me-2" type="submit">Update <i
                        class="mdi mdi-file-document-outline"></i></button>
                <a href="{{ route('roles.index') }}" class="btn btn-secondary">Cancel <i class="mdi mdi-close"></i></a>
            </div>
        </div>
    </form>

    @push('scripts')
        <script>
            // --- Permissions Select All Logic ---
            function syncSelectAllCheckbox() {
                $('#selectAllPermissions').prop(
                    'checked',
                    $('.permission-checkbox').length && $('.permission-checkbox:checked').length === $(
                        '.permission-checkbox').length
                );
            }
            // On change select all
            $('#selectAllPermissions').on('change', function() {
                $('.permission-checkbox').prop('checked', this.checked);
                clearPermissionsError();
            });
            // On single permission checkbox change
            $('#permissionsList').on('change', '.permission-checkbox', function() {
                syncSelectAllCheckbox();
                clearPermissionsError();
            });
            // On page load
            $(syncSelectAllCheckbox);

            function clearPermissionsError() {
                $('.permissions-error').removeClass('invalid-feedback text-danger').html('');
                $('.permission-checkbox').removeClass('is-invalid');
            }

            function clearNameError() {
                $('.name-error').removeClass('invalid-feedback text-danger').html('');
                $('[name="name"]').removeClass('is-invalid');
            }

            // --- Exios Form Submission ---
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
                    } else {
                        if (response.data.message) {
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
                        }

                        let errors = response.data.errors || {};
                        // Name field error
                        if (errors.name) {
                            $('[name="name"]').addClass('is-invalid');
                            $('.name-error').addClass('invalid-feedback text-danger').html(errors.name[0]);
                        }
                        // Permissions group error
                        if (errors.permissions) {
                            $('.permissions-error').addClass('invalid-feedback text-danger').html(errors
                                .permissions[0]);
                            $('.permission-checkbox').addClass('is-invalid');
                        }
                    }
                }).catch(function(error) {
                    $('button[type="submit"]').prop('disabled', false);
                    
                    // Laravel FormRequest validation errors (422)
                    if (error.response && error.response.status === 422 && error.response.data.errors) {
                        clearNameError();
                        clearPermissionsError();
                        let errors = error.response.data.errors;
                        if (errors.name) {
                            $('[name="name"]').addClass('is-invalid');
                            $('.name-error').addClass('invalid-feedback text-danger').html(errors.name[0]);
                        }
                        if (errors.permissions) {
                            $('.permissions-error').addClass('invalid-feedback text-danger').html(errors
                                .permissions[0]);
                            $('.permission-checkbox').addClass('is-invalid');
                        }
                    } else {
                        // Other errors (network, server, etc)
                        let msg = "Something went wrong. Please try again.";
                        alert(msg);
                    }
                });
            });


            // Remove error styles/messages on user input for name
            $('[name="name"]').on('input', clearNameError);
        </script>
    @endpush
</x-layouts.app>
