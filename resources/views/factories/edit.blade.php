<x-layouts.app>
    <x-slot name="title">Edit Factory Type</x-slot>
    <x-slot name="pageTitle">Edit Factory Type</x-slot>

    <div class="card">
        <div class="card-body">
            <form id="form" action="{{ route('factories.update', $factory->id) }}" method="POST"
                class="needs-validation" novalidate>
                @csrf
                @method('PUT')

                <h4 class="mb-3 header-title">Basic Information</h4>
                <div class="mb-3">
                    <label class="form-label">Factory Name</label>
                    <input type="text" class="form-control" name="name" id="name"
                        value="{{ $factory->name }}" placeholder="Enter your factory name" required>
                    <p class="invalid-feedback"></p>
                </div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="factoryStatus" id="factoryStatus" class="form-select" required>
                        <option value="" disabled selected>Select status...</option>
                        <option value="1" {{ $factory->status === 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ $factory->status === 0 ? 'selected' : '' }}>Disable</option>
                        <p class="invalid-feedback"></p>
                    </select>
                    <p class="invalid-feedback"></p>
                </div>
                <button class="btn btn-primary me-2" type="submit">Update <i
                        class="mdi mdi-file-document-outline"></i></button>
                <a href="{{ route('factories.index') }}" class="btn btn-secondary">Cancel <i
                        class="mdi mdi-close"></i></a>
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
                            form.find("input, select, textarea").removeClass(
                                "is-invalid is-valid");
                            form.find(".invalid-feedback").html("");

                            if (response.data.status) {
                                window.location.href = "{{ route('factories.index') }}";
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
                                form.find("input, select, textarea").on("input change",
                                    function() {
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

                                form.find("input, select, textarea").on("input change",
                                    function() {
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
