<x-layouts.app>
    {{-- Page title --}}
    <x-slot name="title">Edit Embroidery Report</x-slot>
    <x-slot name="pageTitle">Edit Embroidery Report</x-slot>

    <div class="card">
        <div class="card-body">
            <form id="form" action="{{ route('embroideries.update', $embroidery->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Style No -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Style No</label>
                    <select name="order_id" id="style-select" class="form-select mt-1" autocomplete="off">
                        <option value="">Select a style...</option>
                        @foreach ($orders as $order)
                            <option value="{{ $order->id }}" 
                                data-colors='@json($order->color_qty)' 
                                data-garments='@json($order->garmentTypes->map->only('id', 'name'))'
                                @if($order->id == old('order_id', $embroidery->order_id)) selected @endif>
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
                        {{-- Options populated by JS --}}
                    </select>
                    <div class="error text-danger small mt-1"></div>
                </div>

                <!-- Date -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Date</label>
                    <input type="date" name="date" id="date" class="form-control mt-1" value="{{ old('date', $embroidery->date ? date('Y-m-d', strtotime($embroidery->date)) : '') }}">
                    <div class="error text-danger small mt-1"></div>
                </div>

                <!-- Embroidery Fields (dynamic) -->
                <div id="add-fields" class="mb-3">
                    <label class="form-label fw-semibold">Embroidery Report</label>
                    <div class="error text-danger small mt-1"></div>
                    {{-- Populated by JS --}}
                </div>

                <!-- Buttons -->
                <button class="btn btn-primary me-2" type="submit">Update <i class="mdi mdi-file-document-outline"></i></button>
                <a href="{{ route('embroideries.index') }}" class="btn btn-secondary">Cancel <i class="mdi mdi-close"></i></a>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        // --- Setup server data ---
        const latestCuttings = @json($latestCuttings);
        const embroideryData = @json($embroidery->embroidery_data);
        const selectedOrderId = '{{ old('order_id', $embroidery->order_id) }}';
        const selectedGarmentType = '{{ old('garment_type', $embroidery->garment_type) }}';

        // --- Helper: Find saved embroidery data for color ---
        function getSavedEmbroideryData(color) {
            return (embroideryData || []).find(e => e.color === color) || {};
        }

        // --- Populate garment types and color fields on load/change ---
        function populateFields() {
            let styleSelect = document.getElementById('style-select');
            let garmentSelect = document.getElementById('garment_type');
            let selected = styleSelect.options[styleSelect.selectedIndex];

            // Garment Types
            garmentSelect.innerHTML = '<option value="">Select...</option>';
            let garments = selected.getAttribute('data-garments');
            if (garments) {
                try { garments = JSON.parse(garments); } catch { garments = []; }
                garments.forEach(type => {
                    garmentSelect.innerHTML += `<option value="${type.name}" ${(type.name === selectedGarmentType) ? 'selected' : ''}>${type.name}</option>`;
                });
            }

            // Embroidery Fields
            const fieldsDiv = document.getElementById('add-fields');
            fieldsDiv.innerHTML = `<label class="form-label fw-semibold">Embroidery Report</label>
                                   <div class="error text-danger small mt-1"></div>`;

            let colors = selected.getAttribute('data-colors');
            let cuttingData = [];
            if (selectedOrderId) {
                cuttingData = latestCuttings[selectedOrderId] ? latestCuttings[selectedOrderId].cutting : [];
            }
            if (colors) {
                try { colors = JSON.parse(colors); } catch { colors = []; }
                colors.forEach((row, idx) => {
                    // Latest cutting qty for this color
                    let cuttingRow = cuttingData.find(c => c.color === row.color);
                    let lastCuttingQty = cuttingRow ? cuttingRow.cutting_qty : '';
                    // Saved embroidery data for this color
                    let saved = getSavedEmbroideryData(row.color);

                    const div = document.createElement('div');
                    div.className = 'row g-2 align-items-center px-2 pb-2 border mb-2 rounded';
                    div.innerHTML = `
                        <div class="col-6 col-md-4 col-lg-2">
                            <span class="fw-semibold">Color</span>
                            <input type="text" readonly value="${row.color}" class="form-control bg-soft-secondary" name="embroidery_data[${idx}][color]">
                        </div>
                        <div class="col-6 col-md-4 col-lg-2">
                            <span class="fw-semibold">Order Qty</span>
                            <input type="number" readonly min="0" value="${row.qty}" class="form-control bg-soft-secondary" name="embroidery_data[${idx}][order_qty]">
                        </div>
                        <div class="col-6 col-md-4 col-lg-2">
                            <span class="fw-semibold">Cutting Qty</span>
                            <input type="number" readonly min="0" value="${lastCuttingQty || 'N/A'}" class="form-control bg-soft-secondary" name="embroidery_data[${idx}][cutting_qty]">
                        </div>
                        <div class="col-6 col-md-4 col-lg-2">
                            <span class="fw-semibold">Factory</span>
                            <input type="text" placeholder="Factory" class="form-control" name="embroidery_data[${idx}][factory]" value="${saved.factory ?? ''}">
                        </div>
                        <div class="col-6 col-md-4 col-lg-2">
                            <span class="fw-semibold">Send</span>
                            <input type="number" min="0" placeholder="Send" class="form-control" name="embroidery_data[${idx}][send]" value="${saved.send ?? ''}">
                        </div>
                        <div class="col-6 col-md-4 col-lg-2">
                            <span class="fw-semibold">Received</span>
                            <input type="number" min="0" placeholder="Received" class="form-control" name="embroidery_data[${idx}][received]" value="${saved.received ?? ''}">
                        </div>
                    `;
                    fieldsDiv.insertBefore(div, fieldsDiv.querySelector('.error'));
                });
            }
        }

        // --- Style change handler ---
        document.getElementById('style-select').addEventListener('change', function() {
            populateFields();
        });

        // --- On page load, set selected style/garment and populate fields ---
        document.addEventListener('DOMContentLoaded', function() {
            // Set style select (if editing)
            if (selectedOrderId) {
                let styleSelect = document.getElementById('style-select');
                for (let i = 0; i < styleSelect.options.length; i++) {
                    if (styleSelect.options[i].value == selectedOrderId) {
                        styleSelect.selectedIndex = i;
                        break;
                    }
                }
            }
            populateFields();
        });

        // --- Form submit via AJAX (same as before, can be kept as is) ---
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
                                window.location.href = "{{ route('embroideries.index') }}";
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
