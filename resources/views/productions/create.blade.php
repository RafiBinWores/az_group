<x-layouts.app>
    {{-- Page title --}}
    <x-slot name="title">Create Production Report</x-slot>
    {{-- Page header --}}
    <x-slot name="pageTitle">Create Production Report</x-slot>


    {{-- Page Content --}}
    <div class="card">
        <div class="card-body">
            <form id="form" action="{{ route('productions.store') }}" method="POST" class="needs-validation"
                novalidate>
                @csrf

                <!-- Style No -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Style No</label>
                    <select name="order_id" id="style-select" class="form-select mt-1" autocomplete="off" required>
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
                    <select name="garment_type" id="garment_type" class="form-select mt-1" required>
                        <option value="">Select...</option>
                    </select>
                    <div class="error text-danger small mt-1"></div>
                </div>

                <!-- Date -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Date</label>
                    <input type="date" name="date" id="date" class="form-control mt-1" required>
                    <div class="error text-danger small mt-1"></div>
                </div>

                <!-- Cutting Fields (dynamic) -->
                <div id="add-fields" class="mb-3">
                    <label class="form-label fw-semibold">Production Report</label>
                    <div class="error text-danger small mt-1"></div>
                </div>

                <!-- Buttons -->
                <button class="btn btn-primary me-2" type="submit">Create <i
                        class="mdi mdi-file-document-outline"></i></button>
                <a href="{{ route('prints.index') }}" class="btn btn-secondary">Cancel <i class="mdi mdi-close"></i></a>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            // Pass the latest cutting data from PHP to JS
            const latestCuttings = @json($latestCuttings);
            const factories = @json($factories);
            const lines = @json($lines);

            // Style select change handler
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

                // Production fields
                const fieldsDiv = document.getElementById('add-fields');
                fieldsDiv.innerHTML = `<label class="form-label fw-semibold">Production Report</label>
                                         <div class="error text-danger small mt-1"></div>`;

                let colors = selected.getAttribute('data-colors');
                let selectedOrderId = this.value;
                // Use string key for JS object safety
                let cuttingData = latestCuttings[selectedOrderId + ""] ? latestCuttings[selectedOrderId + ""].cutting :
                    [];

                function renderOptions(list, valueKey, labelKey) {
                    return list.map(item => `<option value="${item[valueKey]}">${item[labelKey]}</option>`).join('');
                }


                if (colors) {
                    try {
                        colors = JSON.parse(colors);
                    } catch (e) {
                        colors = [];
                    }
                    let sn = 1;
                    colors.forEach((row, idx) => {
                        // Find latest cutting qty for this color
                        let cuttingRow = cuttingData.find(c => c.color === row.color);
                        let lastCuttingQty = cuttingRow ? cuttingRow.cutting_qty : '';

                        const div = document.createElement('div');
                        div.className =
                            'row g-2 align-items-center border-primary px-2 pb-2 border mb-2 rounded';
                        div.innerHTML = `
                                        <div class="col-6 col-md-3 col-lg-3">
                                            <span class="fw-semibold">Color</span>
                                            <input type="text" readonly value="${row.color}" class="form-control bg-soft-secondary" name="production_data[${idx}][color]">
                                        </div>
                                        <div class="col-6 col-md-3 col-lg-3 d-none">
                                            <span class="fw-semibold">Order Qty</span>
                                            <input type="number" readonly min="0" placeholder="Order Qty" value="${row.qty}" class="form-control bg-soft-secondary" name="production_data[${idx}][order_qty]">
                                        </div>
                                        <div class="col-6 col-md-3 col-lg-3">
                                            <span class="fw-semibold">Cutting Qty</span>
                                            <input type="number" readonly min="0" placeholder="Cutting Qty" value="${lastCuttingQty || 'N/A'}" class="form-control bg-soft-secondary" name="production_data[${idx}][cutting_qty]" required>
                                        </div>
                                        <div class="col-6 col-md-3 col-lg-3">
                                            <span class="fw-semibold">Factory</span>
                                             <select name="production_data[${idx}][factory]" id="factory" class="form-select">
                                                <option value="" disabled selected>Select...</option>
                                                ${renderOptions(factories, 'name', 'name')}
                                            </select>
                                           
                                        </div>
                                        <div class="col-6 col-md-3 col-lg-3">
                                            <span class="fw-semibold">Line</span>
                                            <select name="production_data[${idx}][line]" id="line" class="form-select">
                                                <option value="">Select...</option>
                                                ${renderOptions(lines, 'name', 'name')}
                                            </select>
                                        </div>
                                        <div class="col-6 col-md-3 col-lg-3">
                                            <span class="fw-semibold">Input</span>
                                            <input type="number" min="0" placeholder="Input" class="form-control" name="production_data[${idx}][input]">
                                        </div>
                                        <div class="col-6 col-md-3 col-lg-3">
                                            <span class="fw-semibold">Total Input</span>
                                            <input type="number" min="0" placeholder="Total Input" class="form-control" name="production_data[${idx}][total_input]">
                                        </div>
                                        <div class="col-6 col-md-3 col-lg-3">
                                            <span class="fw-semibold">Output</span>
                                            <input type="number" min="0" placeholder="Output" class="form-control" name="production_data[${idx}][output]">
                                        </div>
                                        <div class="col-6 col-md-3 col-lg-3">
                                            <span class="fw-semibold">Total Output</span>
                                            <input type="number" min="0" placeholder="Total Output" class="form-control" name="production_data[${idx}][total_output]">
                                        </div>
                                    `;
                        fieldsDiv.insertBefore(div, fieldsDiv.querySelector('.error'));
                    });
                }
            });

            // Form submit via AJAX (handles errors for both flat and nested fields)
            $(function() {
                $("#form").on("submit", function(event) {
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
                                window.location.href = "{{ route('productions.index') }}";
                            } else {
                                if (response.message) {
                                    Swal.fire({
                                        toast: true,
                                        position: 'top-right',
                                        icon: response.status ? 'success' : 'warning',
                                        title: response.message,
                                        showConfirmButton: false,
                                        timer: 2500,
                                        timerProgressBar: true,
                                        customClass: {
                                            popup: 'colored-toast'
                                        }
                                    });
                                }

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
