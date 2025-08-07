<x-layouts.app>
    {{-- Page title --}}
    <x-slot name="title">Create cutting | AZ Group</x-slot>
    {{-- Page header --}}
    <x-slot name="pageTitle">Create cutting</x-slot>


    {{-- Page Content --}}
    <div class="card">
        <div class="card-body">
            <form id="cutting-form" action="{{ route('cuttings.store') }}" method="POST">
                @csrf

                <!-- Style No -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Style No</label>
                    <select name="order_id" id="style-select" class="form-select mt-1" autocomplete="off">
                        <option value="">Select a style...</option>
                        @foreach ($orders as $order)
                            <option value="{{ $order->id }}" data-colors='@json($order->color_qty)'
                                data-garments='@json($order->garmentTypes->map->only('id', 'name'))'>
                                {{ $order->style_no }}
                            </option>
                        @endforeach
                    </select>
                    <div class="error text-danger small mt-1"></div>
                </div>

                <!-- Garment Type -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Garment Type</label>
                    <select name="garment_type" id="garment_type" class="form-select mt-1">
                        <option value="">Select...</option>
                    </select>
                    <div class="error text-danger small mt-1"></div>
                </div>

                <!-- Date -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Date</label>
                    <input type="date" name="date" id="date" class="form-control mt-1">
                    <div class="error text-danger small mt-1"></div>
                </div>

                <!-- Cutting Fields (dynamic) -->
                <div id="cutting-fields" class="mb-3">
                    <label class="form-label fw-semibold">Cutting</label>
                    <div class="error text-danger small mt-1"></div>
                </div>

                <!-- Buttons -->
                <button class="btn btn-primary me-2" type="submit">Create <i
                        class="mdi mdi-file-document-outline"></i></button>
                <a href="{{ route('cuttings.index') }}" class="btn btn-secondary">Cancel <i class="mdi mdi-close"></i></a>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            // Dynamically update garment types and color fields
            document.getElementById('style-select').addEventListener('change', function() {
                // Garment Types
                let garmentSelect = document.getElementById('garment_type');
                garmentSelect.innerHTML = '<option value="">Select...</option>';
                let selected = this.options[this.selectedIndex];
                let garments = selected.getAttribute('data-garments');
                if (garments) {
                    try {
                        garments = JSON.parse(garments);
                    } catch (e) {
                        garments = [];
                    }
                    garments.forEach(type => {
                        garmentSelect.innerHTML += `<option value="${type.name}">${type.name}</option>`;
                    });
                }

                // Cutting fields
                const fieldsDiv = document.getElementById('cutting-fields');
                fieldsDiv.innerHTML = `<label class="form-label fw-semibold">Cutting</label>
                    <div class="error text-danger small mt-1"></div>`;
                let colors = selected.getAttribute('data-colors');
                if (colors) {
                    try {
                        colors = JSON.parse(colors);
                    } catch (e) {
                        colors = [];
                    }
                    colors.forEach((row, idx) => {
                        const div = document.createElement('div');
                        div.className = 'row g-2 align-items-center mb-2';
                        div.innerHTML = `
                            <div class="col-5">
                                <input type="text" readonly value="${row.color}" class="form-control bg-light" name="cutting[${idx}][color]">
                            </div>
                            <div class="col-3">
                                <input type="number" readonly min="0" placeholder="Order Qty" value="${row.qty}" class="form-control bg-light" name="cutting[${idx}][order_qty]">
                            </div>
                            <div class="col-4">
                                <input type="number" min="0" placeholder="Cutting Qty" class="form-control" name="cutting[${idx}][cutting_qty]">
                            </div>
                        `;
                        fieldsDiv.insertBefore(div, fieldsDiv.querySelector('.error'));
                    });
                }
            });

            // Form submit via AJAX (handles errors for both flat and nested fields)
            $(function() {
                $("#cutting-form").on("submit", function(event) {
                    event.preventDefault();
                    let form = $(this);
                    let formData = new FormData(this);
                    $('button[type="submit"]').prop("disabled", true);

                    $.ajax({
                        url: form.attr("action"),
                        type: "POST",
                        data: formData,
                        dataType: "json",
                        processData: false,
                        contentType: false,
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        },
                        success: function(response) {
                            $('button[type="submit"]').prop("disabled", false);
                            if (response.status) {
                                window.location.href = "{{ route('cuttings.index') }}";
                            } else {
                                displayFieldErrors(response.errors || {});
                            }
                        },
                        error: function(xhr) {
                            $('button[type="submit"]').prop("disabled", false);
                            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                                displayFieldErrors(xhr.responseJSON.errors);
                            } else {
                                showToast("error", "Something went wrong. Please try again.");
                            }
                        },
                    });
                });

                // Error rendering function
                function displayFieldErrors(errors) {
                    $(".error").html("");
                    $("input, select").removeClass("is-invalid");

                    $.each(errors, function(key, value) {
                        // Convert array field names (dot notation) to correct selector
                        let name = key.replace(/\./g, "][");
                        let fieldSelector = `[name='${name}']`;
                        let inputField = $(fieldSelector);

                        // If not found, try flat fields
                        if (!inputField.length) inputField = $(`[name='${key}']`);

                        // Try finding the error div (looks for .error in parent mb-3, or after input for arrays)
                        let errorField = inputField.closest(".mb-3").find(".error").first();
                        if (!errorField.length && inputField.next('.error').length) {
                            errorField = inputField.next('.error');
                        }

                        inputField.addClass("is-invalid");
                        errorField.html(Array.isArray(value) ? value[0] : value);
                    });

                    $("input, select").on("input change", function() {
                        $(this)
                            .removeClass("is-invalid")
                            .closest(".mb-3")
                            .find(".error")
                            .html("");
                    });
                }
            });
        </script>
    @endpush
</x-layouts.app>
