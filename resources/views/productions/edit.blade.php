<x-layouts.app>
    {{-- Page title --}}
    <x-slot name="title">Edit Production Report</x-slot>
    {{-- Page header --}}
    <x-slot name="pageTitle">Edit Production Report</x-slot>

    <div class="card">
        <div class="card-body">
            <form id="form" action="{{ route('productions.update', $production->id) }}" method="POST" class="needs-validation" novalidate>
                @csrf
                @method('PUT')

                {{-- Style No --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Style No</label>
                    <select name="order_id" id="style-select" class="form-select mt-1" autocomplete="off" required>
                        <option value="">Select a style...</option>
                        @foreach ($orders as $order)
                            <option
                                value="{{ $order->id }}"
                                data-colors='@json($order->color_qty)'
                                data-garments='@json($order->garmentTypes->map->only('id','name'))'
                                {{ (int)$production->order_id === (int)$order->id ? 'selected' : '' }}
                            >
                                {{ $order->style_no }}
                            </option>
                        @endforeach
                    </select>
                    <div class="error text-danger small mt-1"></div>
                </div>

                {{-- Garment Type --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Garment Type</label>
                    <select name="garment_type" id="garment_type" class="form-select mt-1" required>
                        <option value="">Select...</option>
                        {{-- options will be injected by JS to keep behavior consistent --}}
                    </select>
                    <div class="error text-danger small mt-1"></div>
                </div>

                {{-- Date --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Date</label>
                    <input type="date" name="date" id="date" class="form-control mt-1" value="{{ $production->date }}" required>
                    <div class="error text-danger small mt-1"></div>
                </div>

                {{-- Dynamic Fields --}}
                <div id="add-fields" class="mb-3">
                    <label class="form-label fw-semibold">Production Report</label>
                    <div class="error text-danger small mt-1"></div>
                </div>

                {{-- Buttons --}}
                <button class="btn btn-primary me-2" type="submit">
                    Update <i class="mdi mdi-content-save"></i>
                </button>
                <a href="{{ route('productions.index') }}" class="btn btn-secondary">
                    Cancel <i class="mdi mdi-close"></i>
                </a>
            </form>
        </div>
    </div>

    @push('scripts')
    
    <script>
        // Server data -> JS
        const latestCuttings = @json($latestCuttings);   // { [order_id]: { cutting: [ {color, cutting_qty}, ... ] } }
        const factories      = @json($factories);        // array of {id, name} (or {name})
        const lines          = @json($lines);            // array of {id, name} (or {name})
        const productionData = @json($production->production_data ?? []); // existing rows (array)
        const initialOrderId = {{ (int)$production->order_id }};
        const initialGarment = @json($production->garment_type ?? '');

        // Helpers
        function asArray(val) {
            if (!val) return [];
            if (typeof val === 'string') {
                try { return JSON.parse(val); } catch(e){ return []; }
            }
            return Array.isArray(val) ? val : [];
        }
        function renderOptions(list, valueKey, labelKey, selectedValue = null) {
            return list.map(item => {
                const value = item[valueKey];
                const label = item[labelKey];
                const selected = (selectedValue != null && String(value) === String(selectedValue)) ? 'selected' : '';
                return `<option value="${value}" ${selected}>${label}</option>`;
            }).join('');
        }
        function getOrderOptionEl(orderId) {
            const sel = document.getElementById('style-select');
            return [...sel.options].find(o => String(o.value) === String(orderId));
        }

        // Build rows (Edit mode: prefer productionData; fallback to order colors)
        function buildRows(orderId) {
            const fieldsDiv = document.getElementById('add-fields');
            fieldsDiv.innerHTML = `<label class="form-label fw-semibold">Production Report</label>
                                   <div class="error text-danger small mt-1"></div>`;

            const selected = getOrderOptionEl(orderId);
            if (!selected) return;

            // fallback source if no productionData rows exist
            let colors = asArray(selected.getAttribute('data-colors'));
            let rows = Array.isArray(productionData) && productionData.length ? productionData : colors.map(c => ({
                color: c.color,
                order_qty: c.qty ?? null,
                cutting_qty: null,
                factory: '',
                line: '',
                input: null,
                total_input: null,
                output: null,
                total_output: null,
            }));

            const cuttingData = latestCuttings[String(orderId)] ? latestCuttings[String(orderId)].cutting : [];

            rows.forEach((row, idx) => {
                // find latest cutting qty if not provided in saved data
                let latestCut = null;
                if (cuttingData && row.color) {
                    const found = cuttingData.find(c => String(c.color).toLowerCase() === String(row.color).toLowerCase());
                    latestCut = found ? (found.cutting_qty ?? null) : null;
                }
                // prefer saved cutting_qty; else fallback to latest; else 'N/A'
                const displayCuttingQty = row.cutting_qty != null && row.cutting_qty !== '' ? row.cutting_qty : (latestCut ?? 'N/A');

                const div = document.createElement('div');
                div.className = 'row g-2 align-items-center border-primary px-2 pb-2 border mb-2 rounded';
                div.innerHTML = `
                    <div class="col-6 col-md-3">
                        <span class="fw-semibold">Color</span>
                        <input type="text" readonly class="form-control bg-soft-secondary"
                            name="production_data[${idx}][color]" value="${row.color ?? ''}">
                    </div>

                    <div class="col-6 col-md-3 d-none">
                        <span class="fw-semibold">Order Qty</span>
                        <input type="number" readonly class="form-control bg-soft-secondary"
                            name="production_data[${idx}][order_qty]" value="${row.order_qty ?? ''}">
                    </div>

                    <div class="col-6 col-md-3">
                        <span class="fw-semibold">Cutting Qty</span>
                        <input type="number" readonly class="form-control bg-soft-secondary"
                            name="production_data[${idx}][cutting_qty]" value="${displayCuttingQty}">
                    </div>

                    <div class="col-6 col-md-3">
                        <span class="fw-semibold">Factory</span>
                        <select name="production_data[${idx}][factory]" class="form-select">
                            <option value="">Select...</option>
                            ${renderOptions(factories, factories[0]?.id !== undefined ? 'name' : 'name', 'name', row.factory ?? '')}
                        </select>
                        <div class="error text-danger small mt-1"></div>
                    </div>

                    <div class="col-6 col-md-3">
                        <span class="fw-semibold">Line</span>
                        <select name="production_data[${idx}][line]" class="form-select">
                            <option value="">Select...</option>
                            ${renderOptions(lines, lines[0]?.id !== undefined ? 'name' : 'name', 'name', row.line ?? '')}
                        </select>
                        <div class="error text-danger small mt-1"></div>
                    </div>

                    <div class="col-6 col-md-3">
                        <span class="fw-semibold">Input</span>
                        <input type="number" class="form-control" placeholder="Input"
                            name="production_data[${idx}][input]" value="${row.input ?? ''}">
                        <div class="error text-danger small mt-1"></div>
                    </div>

                    <div class="col-6 col-md-3">
                        <span class="fw-semibold">Total Input</span>
                        <input type="number" class="form-control" placeholder="Total input"
                            name="production_data[${idx}][total_input]" value="${row.total_input ?? ''}">
                        <div class="error text-danger small mt-1"></div>
                    </div>

                    <div class="col-6 col-md-3">
                        <span class="fw-semibold">Output</span>
                        <input type="number" class="form-control" placeholder="Output"
                            name="production_data[${idx}][output]" value="${row.output ?? ''}">
                        <div class="error text-danger small mt-1"></div>
                    </div>

                    <div class="col-6 col-md-3">
                        <span class="fw-semibold">Total Output</span>
                        <input type="number" class="form-control" placeholder="Total output"
                            name="production_data[${idx}][total_output]" value="${row.total_output ?? ''}">
                        <div class="error text-danger small mt-1"></div>
                    </div>
                `;
                fieldsDiv.insertBefore(div, fieldsDiv.querySelector('.error'));
            });
        }

        // Garment types builder (based on selected style)
        function buildGarmentOptions(orderId, selectedGarment) {
            const sel = getOrderOptionEl(orderId);
            const garmentSelect = document.getElementById('garment_type');
            garmentSelect.innerHTML = '<option value="">Select...</option>';
            if (!sel) return;
            let garments = sel.getAttribute('data-garments');
            garments = asArray(garments);
            garments.forEach(type => {
                const val = type.name;
                const isSel = String(val) === String(selectedGarment) ? 'selected' : '';
                garmentSelect.innerHTML += `<option value="${val}" ${isSel}>${val}</option>`;
            });
        }

        // Init (prefill edit state)
        document.addEventListener('DOMContentLoaded', function() {
            // Set initial garment options
            buildGarmentOptions(initialOrderId, initialGarment);
            // Build rows with existing production_data (or fallback to order colors)
            buildRows(initialOrderId);
        });

        // React to style changes (rebuild garment + rows)
        document.getElementById('style-select').addEventListener('change', function() {
            const orderId = this.value;
            buildGarmentOptions(orderId, ''); // reset garment selection on change
            buildRows(orderId);
        });

        // --- AJAX submit (PUT) ---
        $(function () {
            $("#form").on("submit", function (event) {
                event.preventDefault();
                const form = $(this);
                const formData = new FormData(this);
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
                    success: function (response) {
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
                            Swal.fire({
                                toast: true,
                                position: 'top-right',
                                icon: 'error',
                                title: 'Something went wrong. Please try again.',
                                showConfirmButton: false,
                                timer: 2500,
                                timerProgressBar: true,
                                customClass: { popup: 'colored-toast' }
                            });
                        }
                    },
                });

                function displayFieldErrors(errors) {
                    $(".error").html("");
                    $("input, select").removeClass("is-invalid");

                    $.each(errors, function (key, value) {
                        // convert dot notation to bracket for array fields
                        let name = key.replace(/\./g, "][");
                        let selector = `[name='${name}']`;
                        let input = $(selector);

                        if (!input.length) input = $(`[name='${key}']`);

                        let errorField = input.closest(".mb-3").find(".error").first();
                        if (!errorField.length && input.next('.error').length) {
                            errorField = input.next('.error');
                        }

                        input.addClass("is-invalid");
                        errorField.html(Array.isArray(value) ? value[0] : value);
                    });

                    $("input, select").on("input change", function () {
                        $(this).removeClass("is-invalid")
                            .closest(".mb-3").find(".error").html("");
                    });
                }
            });
        });
    </script>
    @endpush
</x-layouts.app>
