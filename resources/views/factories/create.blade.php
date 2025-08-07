<x-layouts.app>
    <x-slot name="title">Create Factory</x-slot>
    <x-slot name="pageTitle">Create Factory</x-slot>

    <div class="card">
        <div class="card-body">
            <form id="form" action="{{ route('factories.store') }}" method="POST" class="needs-validation"
                novalidate>
                @csrf

                <h4 class="mb-3 header-title">Basic Information</h4>
                <div class="mb-3">
                    <label class="form-label">Factory Name</label>
                    <input type="text" class="form-control" name="name" id="name"
                        placeholder="Enter your factory" required>
                    <p class="invalid-feedback"></p>
                </div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="factoryStatus" id="factoryStatus" class="form-select" required>
                        <option value="" disabled selected>Select status...</option>
                        <option value="1">Active</option>
                        <option value="0">Disable</option>
                    </select>
                    <p class="invalid-feedback"></p>
                </div>
                <button class="btn btn-primary me-2" type="submit">Create <i
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
                    $('button[type="submit"]').prop("disabled", true);

                    axios.post(form.attr("action"), formData, {
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr("content"),
                                'Content-Type': 'multipart/form-data'
                            }
                        })
                        .then(function(response) {
                            $('button[type="submit"]').prop("disabled", false);

                            if (response.data.status) {
                                 window.location.href = "{{ route('factories.index') }}";
                            } else {
                                let errors = response.data.errors || {};

                                // Clear previous errors
                                $(".invalid-feedback").html("");
                                $("input, select").removeClass("is-invalid");

                                $.each(errors, function(key, value) {
                                    value = Array.isArray(value) ? value[0] : value;
                                    let inputField = $(`[name='${key}']`);
                                    let errorField = inputField
                                        .closest(".mb-3, .form-group")
                                        .find(".invalid-feedback")
                                        .first();
                                    inputField.addClass("is-invalid");
                                    errorField.html(value);
                                });

                                // Remove error classes/messages on input
                                $("input, select").on("input change", function() {
                                    $(this)
                                        .removeClass("is-invalid")
                                        .closest(".mb-3, .form-group")
                                        .find(".invalid-feedback")
                                        .html("");
                                });
                            }
                        })
                        .catch(function(error) {
                            $('button[type="submit"]').prop("disabled", false);
                            if (error.response && error.response.status === 422) {
                                let errors = error.response.data.errors || {};

                                $(".invalid-feedback").html("");
                                $("input, select").removeClass("is-invalid");

                                $.each(errors, function(key, value) {
                                    value = Array.isArray(value) ? value[0] : value;
                                    let inputField = $(`[name='${key}']`);
                                    let errorField = inputField
                                        .closest(".mb-3, .form-group")
                                        .find(".invalid-feedback")
                                        .first();
                                    inputField.addClass("is-invalid");
                                    errorField.html(value);
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
