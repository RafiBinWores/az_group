<x-layouts.app>
    <x-slot name="title">Create Order</x-slot>
    <x-slot name="pageTitle">Create Order</x-slot>

    @push('styles')
        <link href="{{ asset('assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    @endpush

    <div class="card">
        <div class="card-body">
            <form action="{{ route('orders.store') }}" id="form" method="POST" class="needs-validation" novalidate>
                @csrf

                <!-- Buyer Name -->
                <div class="mb-3">
                    <label for="buyer_name" class="form-label">Buyer Name</label>
                    <input type="text" name="buyer_name" id="buyer_name" placeholder="Buyer Name" required
                        class="form-control">
                    <div class="invalid-feedback"></div>
                </div>

                <!-- Style No -->
                <div class="mb-3">
                    <label for="style_no" class="form-label">Style No</label>
                    <input type="text" name="style_no" id="style_no" placeholder="e.g. 1-KA-5123" required
                        class="form-control">
                    <div class="invalid-feedback"></div>
                </div>

                <!-- Garment Types -->
                <div class="mb-3">
                    <label for="garment_types" class="form-label">Garment Types</label>
                    <select id="garment_types" name="garment_types[]" class="form-control select2-multiple" required
                        data-toggle="select2" data-width="100%" multiple="multiple" data-placeholder="Choose ...">
                        <option value="">Select a type...</option>
                        @foreach ($types as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback"></div>
                </div>

                <!-- Total Quantity -->
                <div class="mb-3">
                    <label for="order_quantity" class="form-label">Total Quantity</label>
                    <input type="number" name="order_quantity" id="order_quantity" placeholder="Total Quantity"
                        min="1" required class="form-control">
                    <div class="invalid-feedback"></div>
                </div>

                <div id="add-fields" class="gap-2 mb-3">
                    <label class="form-label mb-2">Color Wise Quantity</label>
                    <div class="d-flex gap-2 align-items-center color-row mb-2">
                        <input type="text" name="color_qty[0][color]" placeholder="Color" class="form-control"
                            required />
                        <input type="number" min="0" name="color_qty[0][qty]" placeholder="Quantity"
                            class="form-control" required />
                        <button type="button" class="btn btn-primary add-row">
                            +
                        </button>
                        <button type="button" class="remove-row btn btn-danger px-2 py-1 ml-2 d-none">
                            &times;
                        </button>
                    </div>
                    <div class="invalid-feedback"></div>
                </div>

                <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">

                <button class="btn btn-primary me-2" type="submit">Create <i
                        class="mdi mdi-file-document-outline"></i></button>
                <a href="{{ route('orders.index') }}" class="btn btn-secondary">Cancel <i class="mdi mdi-close"></i></a>
            </form>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>
        <script>
            // --- DOT TO BRACKET KEY CONVERTER ---
            function dotToBracket(name) {
                // garment_types.0 => garment_types[0]
                return name.replace(/\.(\d+)/g, '[$1]');
            }


            // --- DYNAMIC COLOR ROWS ---
            function updateRemoveButtons() {
                let rows = $('#add-fields .color-row');
                rows.each(function(idx, row) {
                    let removeBtn = $(row).find('.remove-row');
                    if (rows.length === 1) {
                        removeBtn.addClass('d-none');
                    } else {
                        removeBtn.removeClass('d-none');
                    }
                    // Update name attributes for color/qty
                    $(row).find('input[type="text"]').attr('name', `color_qty[${idx}][color]`);
                    $(row).find('input[type="number"]').attr('name', `color_qty[${idx}][qty]`);
                });
            }

            // Add Row
            $(document).on('click', '.add-row', function() {
                let fields = $('#add-fields');
                let rows = fields.find('.color-row');
                let newRow = rows.first().clone();
                // Clear values in the new row
                newRow.find('input').val('');
                newRow.find('input').removeClass('is-invalid');
                newRow.find('.invalid-feedback').html('');
                fields.append(newRow);
                updateRemoveButtons();
            });

            // Remove Row
            $(document).on('click', '.remove-row', function() {
                let rows = $('#add-fields .color-row');
                if (rows.length > 1) {
                    $(this).closest('.color-row').remove();
                    updateRemoveButtons();
                }
            });

            // On load, set correct remove button state
            $(document).ready(function() {
                updateRemoveButtons();
                $('.select2-multiple').select2({
                    width: '100%'
                });
            });

            // Form submission with Axios
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
                                window.location.href = "{{ route('orders.index') }}";
                            } else {
                                let errors = response.data.errors || {};

                                // Clear previous errors
                                $(".invalid-feedback").html('');
                                $("input, select").removeClass("is-invalid");

                                $.each(errors, function(key, value) {
                                    value = Array.isArray(value) ? value[0] : value;

                                    // Special handling for garment_types array field
                                    if (key === 'garment_types' || key.startsWith(
                                        'garment_types.')) {
                                        let selectField = $('#garment_types');
                                        selectField.addClass('is-invalid');
                                        selectField.closest('.mb-3').find('.invalid-feedback').html(
                                            value);
                                        return; // skip to next error
                                    }

                                    // Default handling for all other fields
                                    let inputName = dotToBracket(key);
                                    let inputField = $(`[name='${inputName}']`);
                                    let errorField = inputField
                                        .closest(".mb-3, .form-group, .color-row")
                                        .find(".invalid-feedback")
                                        .first();
                                    inputField.addClass("is-invalid");
                                    errorField.html(value);
                                });

                                // Remove error classes/messages on input
                                $("input, select").on("input change", function() {
                                    $(this)
                                        .removeClass("is-invalid")
                                        .closest(".mb-3, .form-group, .color-row")
                                        .find(".invalid-feedback")
                                        .html('');
                                });
                            }
                        })
                        .catch(function(error) {
                            $('button[type="submit"]').prop("disabled", false);
                            if (error.response && error.response.status === 422) {
                                let errors = error.response.data.errors || {};

                                $(".invalid-feedback").html('');
                                $("input, select").removeClass("is-invalid");

                                $.each(errors, function(key, value) {
                                    value = Array.isArray(value) ? value[0] : value;

                                    // Special handling for garment_types array field
                                    if (key === 'garment_types' || key.startsWith(
                                        'garment_types.')) {
                                        let selectField = $('#garment_types');
                                        selectField.addClass('is-invalid');
                                        selectField.closest('.mb-3').find('.invalid-feedback').html(
                                            value);
                                        return; // skip to next error
                                    }

                                    let inputName = dotToBracket(key);
                                    let inputField = $(`[name='${inputName}']`);
                                    let errorField = inputField
                                        .closest(".mb-3, .form-group, .color-row")
                                        .find(".invalid-feedback")
                                        .first();
                                    inputField.addClass("is-invalid");
                                    errorField.html(value);
                                });
                            } else {
                                alert("Something went wrong. Please try again.");
                            }
                        });
                });
            });
        </script>
    @endpush
</x-layouts.app>
