<x-layouts.app>
    <x-slot name="title">Role Create</x-slot>
    <x-slot name="pageTitle">Role Create</x-slot>

    <div class="card">
        <div class="card-body">
            <form id="form" action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data"
                class="needs-validation" novalidate>
                @csrf

                <h4 class="mb-3 header-title">Basic Information</h4>
                <!-- Avatar -->
                <div class="mb-3">
                    <label class="form-label fw-semibold mb-2">Profile Picture</label>
                    <div class="d-flex align-items-center gap-4 p-3 rounded-3 border border-secondary">
                        <!-- Image preview (circle) -->
                        <div id="photo-preview"
                            class="position-relative rounded-circle border-secondary d-flex align-items-center justify-content-center"
                            style="width:80px; height:80px; overflow:hidden; border: 1px dashed;">
                            <img id="avatarPreviewImg" src="https://via.placeholder.com/80x80?text=+" alt="Preview"
                                class="img-fluid w-100 h-100 object-fit-cover"
                                style="display: none; object-fit: cover;" />
                            <svg id="avatarDefaultSvg"
                                class="text-secondary position-absolute top-50 start-50 translate-middle"
                                style="width:48px;height:48px;" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <circle cx="12" cy="8" r="4" stroke-width="1" />
                                <path d="M4 20c0-4 4-7 8-7s8 3 8 7" stroke-width="1" />
                            </svg>
                            <input id="avatar-input" type="file" name="avatar" accept="image/*"
                                class="position-absolute top-0 start-0 w-100 h-100 opacity-0" style="cursor:pointer;" />
                        </div>

                        <!-- Upload/Delete Buttons -->
                        <div class="d-flex gap-2">
                            <button type="button" id="upload-btn" class="btn btn-success d-flex align-items-center"
                                onclick="document.getElementById('avatar-input').click();">
                                <i class="fa fa-upload me-2"></i> Upload
                            </button>
                            <button type="button" id="delete-photo-btn"
                                class="btn btn-outline-secondary text-secondary">
                                Delete
                            </button>
                        </div>
                    </div>
                    <small class="form-text text-muted">
                        Allowed formats: JPG, PNG, GIF, WEBP. Maximum file size: 2 MB.
                    </small>
                    <div class="invalid-feedback"></div>
                </div>
                <!-- Name -->
                <div class="mb-3">
                    <label class="form-label">User Name</label>
                    <input type="text" class="form-control" name="name" id="name"
                        placeholder="Enter user name" required>
                    <p class="invalid-feedback"></p>
                </div>
                <!-- Email -->
                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <input type="email" class="form-control" name="email" id="email"
                        placeholder="Enter user email" required>
                    <p class="invalid-feedback"></p>
                </div>
                <!-- Role -->
                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select" id="role" required>
                        <option value="">Select a role</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->name }}">
                                {{ $role->name }}</option>
                        @endforeach
                    </select>
                    <p class="invalid-feedback"></p>

                </div>
                <div class="mb-3 position-relative">
                    <label for="password" class="form-label">Password</label>
                    <input class="form-control pe-4" type="password" name="password" id="password"
                        placeholder="Enter your password" required>
                    <button class="btn position-absolute end-0 border-0 bg-transparent" type="button"
                        id="togglePassword" style="z-index: 10; top: 32px;">
                        <i class="fa-regular fa-eye text-muted" id="togglePasswordIcon"></i>
                    </button>
                    <p class="invalid-feedback"></p>
                </div>

                <button class="btn btn-primary me-2" type="submit">Create <i
                        class="mdi mdi-file-document-outline"></i></button>
                <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel <i class="mdi mdi-close"></i></a>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            $(function() {
                $("form").on("submit", function(event) {
                    event.preventDefault();
                    let form = $(this);
                    let formData = new FormData(this);
                    form.find('button[type="submit"]').prop("disabled", true);

                    axios.post(form.attr("action"), formData, {
                            headers: {
                                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                                "Content-Type": "multipart/form-data"
                            }
                        })
                        .then(function(response) {
                            form.find('button[type="submit"]').prop("disabled", false);

                            // Remove validation classes from all inputs/selects
                            form.find("input, select, textarea").removeClass("is-invalid is-valid");
                            form.find(".invalid-feedback").html("");

                            if (response.data.status) {
                                window.location.href = "{{ route('users.index') }}";
                            } else {
                                let errors = response.data.errors || {};
                                $.each(errors, function(key, value) {
                                    value = Array.isArray(value) ? value[0] : value;
                                    let inputField = form.find(`[name='${key}']`);
                                    let errorField = inputField
                                        .closest(".mb-3")
                                        .find(".invalid-feedback")
                                        .first();

                                    inputField.addClass("is-invalid");
                                    errorField.html(value);
                                });

                                // Remove error classes/messages on change
                                form.find("input, select, textarea").on("input change", function() {
                                    $(this)
                                        .removeClass("is-invalid")
                                        .closest(".mb-3")
                                        .find(".invalid-feedback")
                                        .html("");
                                });
                            }
                        })
                        .catch(function(error) {
                            form.find('button[type="submit"]').prop("disabled", false);

                            // Handle Laravel validation errors (422)
                            if (error.response && error.response.status === 422) {
                                let errors = error.response.data.errors || {};
                                $.each(errors, function(key, value) {
                                    value = Array.isArray(value) ? value[0] : value;
                                    let inputField = form.find(`[name='${key}']`);
                                    let errorField = inputField
                                        .closest(".mb-3")
                                        .find(".invalid-feedback")
                                        .first();

                                    inputField.addClass("is-invalid");
                                    errorField.html(value);
                                });

                                form.find("input, select, textarea").on("input change", function() {
                                    $(this)
                                        .removeClass("is-invalid")
                                        .closest(".mb-3")
                                        .find(".invalid-feedback")
                                        .html("");
                                });
                            } else {
                                showToast("error", "Something went wrong. Please try again.");
                            }
                        });
                });
            });
        </script>
    @endpush
</x-layouts.app>
