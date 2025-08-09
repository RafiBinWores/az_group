<x-layouts.app>
    {{-- Page title --}}
    <x-slot name="title">Edit Wash Report</x-slot>
    {{-- Page header --}}
    <x-slot name="pageTitle">Edit Wash Report</x-slot>

    <div class="card">
        <div class="card-body">
            <form id="form" action="{{ route('washes.update', $wash->id) }}" method="POST" class="needs-validation" novalidate>
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
                                data-garments='@json($order->garmentTypes->map->only("id","name"))'
                                {{ (string)$wash->order_id === (string)$order->id ? 'selected' : '' }}
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
                    <select name="garment_type" id="garment_type" class="form-select mt-1" data-selected="{{ $wash->garment_type ?? '' }}">
                        <option value="">Select...</option>
                    </select>
                    <div class="error text-danger small mt-1"></div>
                </div>

                <!-- Date -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Date</label>
                    <input
                        type="date"
                        name="date"
                        id="date"
                        class="form-control mt-1"
                         value="{{ old('date', $wash->date ? date('Y-m-d', strtotime($wash->date)) : '') }}"
                    >
                    <div class="error text-danger small mt-1"></div>
                </div>

                <!-- Wash Fields (dynamic) -->
                <div id="add-fields" class="mb-3">
                    <label class="form-label fw-semibold">Wash Report</label>
                    <div class="error text-danger small mt-1"></div>
                </div>

                <!-- Buttons -->
                <button class="btn btn-primary me-2" type="submit">Update <i class="mdi mdi-content-save"></i></button>
                <a href="{{ route('washes.index') }}" class="btn btn-secondary">Cancel <i class="mdi mdi-close"></i></a>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        // Server data
        const latestProdTotals = @json((object)($latestProdTotals ?? []));
        const existing = {
            orderId: @json($wash->order_id),
            garmentType: @json($wash->garment_type),
            rows: @json($wash->wash_data ?? []), // [{color, order_qty, output_qty, factory, send, received}]
        };

        // Build rows UI
        function buildFields(selected, colors, prefillByColor) {
            const fieldsDiv = document.getElementById('add-fields');
            fieldsDiv.innerHTML = `
                <label class="form-label fw-semibold">Wash Report</label>
                <div class="error text-danger small mt-1"></div>
            `;

            colors.forEach((row, idx) => {
                const colorName = row.color;
                const pre = prefillByColor[colorName] || {};
                // latest total output as fallback if output_qty not stored
                const lastTotalOutput =
                    (latestProdTotals[selected] && latestProdTotals[selected][colorName] !== undefined)
                        ? latestProdTotals[selected][colorName]
                        : '';

                const outputQty = (pre.output_qty !== undefined && pre.output_qty !== null && pre.output_qty !== '')
                    ? pre.output_qty
                    : (lastTotalOutput !== '' ? lastTotalOutput : 'N/A');

                const div = document.createElement('div');
                div.className = 'row g-2 align-items-center px-2 pb-2 border mb-2 rounded';
                div.innerHTML = `
                    <div class="col-6 col-md-4 col-lg-2">
                        <span class="fw-semibold">Color</span>
                        <input type="text" readonly value="${colorName}" class="form-control bg-soft-secondary" name="wash_data[${idx}][color]">
                    </div>
                    <div class="col-6 col-md-4 col-lg-2">
                        <span class="fw-semibold">Order Qty</span>
                        <input type="number" readonly min="0" value="${pre.order_qty ?? row.qty ?? ''}" class="form-control bg-soft-secondary" name="wash_data[${idx}][order_qty]">
                    </div>
                    <div class="col-6 col-md-4 col-lg-2">
                        <span class="fw-semibold">Output Qty</span>
                        <input type="text" readonly value="${outputQty}" class="form-control bg-soft-secondary" name="wash_data[${idx}][output_qty]">
                    </div>
                    <div class="col-6 col-md-4 col-lg-2">
                        <span class="fw-semibold">Factory</span>
                        <input type="text" placeholder="Factory" value="${pre.factory ?? ''}" class="form-control" name="wash_data[${idx}][factory]">
                    </div>
                    <div class="col-6 col-md-4 col-lg-2">
                        <span class="fw-semibold">Send</span>
                        <input type="number" min="0" placeholder="Send" value="${pre.send ?? ''}" class="form-control" name="wash_data[${idx}][send]">
                    </div>
                    <div class="col-6 col-md-4 col-lg-2">
                        <span class="fw-semibold">Received</span>
                        <input type="number" min="0" placeholder="Received" value="${pre.received ?? ''}" class="form-control" name="wash_data[${idx}][received]">
                    </div>
                `;
                fieldsDiv.insertBefore(div, fieldsDiv.querySelector('.error'));
            });
        }

        // When style changes, populate garment types and rows
        document.getElementById('style-select').addEventListener('change', function () {
            const garmentSelect = document.getElementById('garment_type');
            garmentSelect.innerHTML = '<option value="">Select...</option>';
            const selected = this.options[this.selectedIndex];
            const selectedOrderId = this.value;

            // Garments
            let garments = selected.getAttribute('data-garments');
            try { garments = garments ? JSON.parse(garments) : []; } catch { garments = []; }
            garments.forEach(type => {
                garmentSelect.innerHTML += `<option value="${type.name}">${type.name}</option>`;
            });

            // Select the saved garment type (if editing)
            const savedGarment = garmentSelect.getAttribute('data-selected');
            if (savedGarment) {
                const opt = Array.from(garmentSelect.options).find(o => o.value === savedGarment);
                if (opt) opt.selected = true;
            }

            // Colors & prefill map by color
            let colors = selected.getAttribute('data-colors');
            try { colors = colors ? JSON.parse(colors) : []; } catch { colors = []; }

            const prefillByColor = {};
            (existing.rows || []).forEach(r => { if (r.color) prefillByColor[r.color] = r; });

            buildFields(selectedOrderId, colors, prefillByColor);
        });

        // Initialize on load for edit
        document.addEventListener('DOMContentLoaded', function () {
            const styleSelect = document.getElementById('style-select');

            if (existing.orderId) {
                // ensure the right option is selected (blade already marks it), then trigger change
                styleSelect.dispatchEvent(new Event('change'));
            }
        });

        // Submit via AJAX (same as your create, corrected redirect)
        $(function () {
            $("#form").on("submit", function (event) {
                event.preventDefault();
                const form = $(this);
                const formData = new FormData(this);
                $('button[type="submit"]').prop("disabled", true);

                $.ajax({
                    url: form.attr("action"),
                    type: "POST", // keep POST; @method('PUT') handles spoofing
                    data: formData,
                    dataType: "json",
                    processData: false,
                    contentType: false,
                    headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
                    success: function (response) {
                        $('button[type="submit"]').prop("disabled", false);
                        if (response.status) {
                            window.location.href = "{{ route('washes.index') }}";
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
                                    customClass: { popup: 'colored-toast' }
                                });
                            }
                            displayFieldErrors(response.errors || {});
                        }
                    },
                    error: function (xhr) {
                        $('button[type="submit"]').prop("disabled", false);
                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            displayFieldErrors(xhr.responseJSON.errors);
                        } else {
                            showToast("error", "Something went wrong. Please try again.");
                        }
                    },
                });
            });

            function displayFieldErrors(errors) {
                $(".error").html("");
                $("input, select").removeClass("is-invalid");

                $.each(errors, function (key, value) {
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

                $("input, select").on("input change", function () {
                    $(this).removeClass("is-invalid").closest(".mb-3").find(".error").html("");
                });
            }
        });
    </script>
    @endpush
</x-layouts.app>
