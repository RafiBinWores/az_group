<x-layouts.app>
    {{-- Page title --}}
    <x-slot name="title">Edit Cutting</x-slot>
    <x-slot name="pageTitle">Edit Cutting</x-slot>

    <div class="card">
        <div class="card-body">
            <form id="cutting-form" action="{{ route('cuttings.update', $cutting->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Style No -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Style No</label>
                    <select name="order_id" id="style-select" class="form-select mt-1" autocomplete="off">
                        <option value="">Select a style...</option>
                        @foreach ($orders as $order)
                            <option 
                                value="{{ $order->id }}"
                                data-colors='@json($order->color_qty)'
                                data-garments='@json($order->garmentTypes->map->only('id', 'name'))'
                                @if ($order->id == old('order_id', $cutting->order_id)) selected @endif
                            >
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
                        {{-- Options will be populated by JS --}}
                    </select>
                    <div class="error text-danger small mt-1"></div>
                </div>

                <!-- Date -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Date</label>
                    <input type="date" name="date" id="date" class="form-control mt-1"
                        value="{{ old('date', $cutting->date ? date('Y-m-d', strtotime($cutting->date)) : '') }}">
                    <div class="error text-danger small mt-1"></div>
                </div>

                <!-- Cutting Fields (dynamic) -->
                <div id="cutting-fields" class="mb-3">
                    <label class="form-label fw-semibold">Cutting</label>
                    <div class="error text-danger small mt-1"></div>
                    {{-- Fields are rendered via JS --}}
                </div>

                <!-- Buttons -->
                <button class="btn btn-primary me-2" type="submit">
                    Update <i class="mdi mdi-file-document-outline"></i>
                </button>
                <a href="{{ route('cuttings.index') }}" class="btn btn-secondary">
                    Cancel <i class="mdi mdi-close"></i>
                </a>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        // Pass server-side data to JS
        const cuttingData = @json($cutting->cutting ?? []);
        const selectedOrderId = '{{ old('order_id', $cutting->order_id) }}';
        const selectedGarmentType = '{{ old('garment_type', $cutting->garment_type) }}';

        // Populate form for edit
        function populateGarmentsAndColors() {
            let styleSelect = document.getElementById('style-select');
            let selected = styleSelect.options[styleSelect.selectedIndex];
            let garmentSelect = document.getElementById('garment_type');

            // Garment Types
            garmentSelect.innerHTML = '<option value="">Select...</option>';
            let garments = selected.getAttribute('data-garments');
            if (garments) {
                try { garments = JSON.parse(garments); } catch (e) { garments = []; }
                garments.forEach(type => {
                    garmentSelect.innerHTML += `<option value="${type.name}" ${(type.name === selectedGarmentType) ? 'selected' : ''}>${type.name}</option>`;
                });
            }

            // Cutting Fields
            const fieldsDiv = document.getElementById('cutting-fields');
            fieldsDiv.innerHTML = `<label class="form-label fw-semibold">Cutting</label><div class="error text-danger small mt-1"></div>`;
            let colors = selected.getAttribute('data-colors');
            if (colors) {
                try { colors = JSON.parse(colors); } catch (e) { colors = []; }
                colors.forEach((row, idx) => {
                    // Find existing cutting data for this color (if any)
                    let cuttingRow = cuttingData.find(c => c.color === row.color) || {};
                    let orderQty = row.qty ?? '';
                    let cuttingQty = cuttingRow.cutting_qty ?? '';

                    const div = document.createElement('div');
                    div.className = 'row g-2 align-items-center mb-2';
                    div.innerHTML = `
                        <div class="col-5">
                            <input type="text" readonly value="${row.color}" class="form-control bg-light" name="cutting[${idx}][color]">
                        </div>
                        <div class="col-3">
                            <input type="number" readonly min="0" placeholder="Order Qty" value="${orderQty}" class="form-control bg-light" name="cutting[${idx}][order_qty]">
                        </div>
                        <div class="col-4">
                            <input type="number" min="0" placeholder="Cutting Qty" class="form-control" name="cutting[${idx}][cutting_qty]" value="${cuttingQty}">
                        </div>
                    `;
                    fieldsDiv.insertBefore(div, fieldsDiv.querySelector('.error'));
                });
            }
        }

        // Style change event
        document.getElementById('style-select').addEventListener('change', function() {
            populateGarmentsAndColors();
        });

        // Auto-select on page load (for edit)
        document.addEventListener('DOMContentLoaded', function() {
            // Set selected style (if not default)
            if (selectedOrderId) {
                let styleSelect = document.getElementById('style-select');
                for (let i = 0; i < styleSelect.options.length; i++) {
                    if (styleSelect.options[i].value == selectedOrderId) {
                        styleSelect.selectedIndex = i;
                        break;
                    }
                }
                populateGarmentsAndColors();
            }
        });

        // Form submit via AJAX (update)
        $(function() {
            $("#cutting-form").on("submit", function(event) {
                event.preventDefault();
                let form = $(this);
                let formData = new FormData(this);
                $('button[type="submit"]').prop("disabled", true);

                $.ajax({
                    url: form.attr("action"),
                    type: "POST", // Laravel needs POST with _method=PUT
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

            // Error rendering
            function displayFieldErrors(errors) {
                $(".error").html("");
                $("input, select").removeClass("is-invalid");

                $.each(errors, function(key, value) {
                    let name = key.replace(/\./g, "][");
                    let fieldSelector = `[name='${name}']`;
                    let inputField = $(fieldSelector);
                    if (!inputField.length) inputField = $(`[name='${key}']`);
                    let errorField = inputField.closest(".mb-3").find(".error").first();
                    if (!errorField.length && inputField.next('.error').length) {
                        errorField = inputField.next('.error');
                    }
                    inputField.addClass("is-invalid");
                    errorField.html(Array.isArray(value) ? value[0] : value);
                });

                $("input, select").on("input change", function() {
                    $(this).removeClass("is-invalid").closest(".mb-3").find(".error").html("");
                });
            }
        });
    </script>
    @endpush
</x-layouts.app>
