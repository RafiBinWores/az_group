<x-layouts.app>
    <x-slot name="title">Create Finishing Report</x-slot>
    <x-slot name="pageTitle">Create Finishing Report</x-slot>

    @push('styles')
        <link href="{{ asset('assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    @endpush

    <div class="card">
        <div class="card-body">
            <form action="{{ route('finishing.store') }}" id="form" method="POST" class="needs-validation"
                novalidate>
                @csrf

                <div class="row">
                    <div class="mb-3 col-12">
                        <label for="order_id" class="form-label">Style No</label>
                        <select class="form-control" name="order_id" id="order_id" data-toggle="select2"
                            data-width="100%" required>
                            <option>Select</option>
                            @foreach ($orders as $order)
                                <option value="{{ $order->id }}">{{ $order->style_no }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3 col-12">
                        <label class="form-label">Date</label>
                        <input type="date" name="date" id="date" class="form-control mt-1" required>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="mb-3 col-12 col-md-6 col-lg-4">
                        <label for="thread_cutting" class="form-label">Thread Cutting</label>
                        <input type="number" name="thread_cutting" id="thread_cutting" placeholder="Thread Cutting" required
                            min="1" class="form-control">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3 col-12 col-md-6 col-lg-4">
                        <label for="qc_check" class="form-label">QC Check</label>
                        <input type="number" name="qc_check" id="qc_check" placeholder="QC Check" min="1"
                            class="form-control">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3 col-12 col-md-6 col-lg-4">
                        <label for="button_rivet_attach" class="form-label">Button & Rivet Attach</label>
                        <input type="number" name="button_rivet_attach" id="button_rivet_attach"
                            placeholder="Button & Rivet Attach" min="1" class="form-control">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3 col-12 col-md-6 col-lg-4">
                        <label for="iron" class="form-label">Iron</label>
                        <input type="number" name="iron" id="iron" placeholder="Iron" min="1"
                            class="form-control">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3 col-12 col-md-6 col-lg-4">
                        <label for="hangtag" class="form-label">Hangtag</label>
                        <input type="number" name="hangtag" id="hangtag" placeholder="Hangtag" min="1"
                            class="form-control">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3 col-12 col-md-6 col-lg-4">
                        <label for="poly" class="form-label">Poly</label>
                        <input type="number" name="poly" id="poly" placeholder="Poly" min="1"
                            class="form-control">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3 col-12 col-md-6 col-lg-4">
                        <label for="carton" class="form-label">Carton</label>
                        <input type="number" name="carton" id="carton" placeholder="Carton" min="1"
                            class="form-control">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3 col-12 col-md-6 col-lg-4">
                        <label for="today_finishing" class="form-label">Today Finishing</label>
                        <input type="number" name="today_finishing" id="today_finishing"
                            placeholder="Today Finishing" min="1" class="form-control">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3 col-12 col-md-6 col-lg-4">
                        <label for="total_finishing" class="form-label">Total Finishing</label>
                        <input type="number" name="total_finishing" id="total_finishing"
                            placeholder="Total Finishing" min="1" class="form-control">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3 col-12 col-md-6 col-lg-4">
                        <label for="plan_to_complete" class="form-label">Plan To Complete</label>
                        <input type="number" name="plan_to_complete" id="plan_to_complete"
                            placeholder="Plan To Complete" min="1" class="form-control">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3 col-12 col-md-6 col-lg-4">
                        <label for="dpi_inline" class="form-label">DPI Inline</label>
                        <input type="number" name="dpi_inline" id="dpi_inline" placeholder="DPI Inline"
                            min="1" class="form-control">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3 col-12 col-md-6 col-lg-4">
                        <label for="fri_final" class="form-label">FRI Final</label>
                        <input type="number" name="fri_final" id="fri_final" placeholder="FRI Final"
                            min="1" class="form-control">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">

                <button class="btn btn-primary me-2" type="submit">Create <i
                        class="mdi mdi-file-document-outline"></i></button>
                <a href="{{ route('finishing.index') }}" class="btn btn-secondary">Cancel <i
                        class="mdi mdi-close"></i></a>
            </form>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>
        <script src="{{ asset('assets/js/pages/form-advanced.init.js') }}"></script>
        <script>
            // Form submission with Axios
            $(function() {
                // clear on user interaction once
                $(document).on('input change', 'input, select', function() {
                    $(this).removeClass("is-invalid")
                        .closest(".mb-3, .form-group, .color-row")
                        .find(".invalid-feedback")
                        .html('');
                    if ($(this).is('select')) {
                        $(this).next('.select2').find('.select2-selection').removeClass('is-invalid');
                    }
                });

                $("form").on("submit", function(event) {
                    event.preventDefault();
                    const form = $(this);
                    const formData = new FormData(this);
                    $('button[type="submit"]').prop("disabled", true);

                    axios.post(form.attr("action"), formData, {
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr("content"),
                                'Content-Type': 'multipart/form-data',
                                'Accept': 'application/json'
                            }
                        })
                        .then(function(response) {
                            $('button[type="submit"]').prop("disabled", false);

                            if (response.data.status) {
                                window.location.href = "{{ route('finishing.index') }}";
                            } else {
                                showErrors(response.data.errors || {});
                            }
                        })
                        .catch(function(error) {
                            $('button[type="submit"]').prop("disabled", false);
                            if (error.response && error.response.status === 422) {
                                showErrors(error.response.data.errors || {});
                            } else {
                                alert("Something went wrong. Please try again.");
                            }
                        });
                });

                function showErrors(errors) {
                    $(".invalid-feedback").html('');
                    $("input, select").removeClass("is-invalid");
                    $(".select2-selection").removeClass("is-invalid");

                    $.each(errors, function(key, value) {
                        value = Array.isArray(value) ? value[0] : value;

                        if (key === 'order_id') {
                            const selectField = $('#order_id');
                            selectField.addClass('is-invalid');
                            selectField.closest('.mb-3').find('.invalid-feedback').html(value);
                            selectField.next('.select2').find('.select2-selection').addClass('is-invalid');
                            return;
                        }

                        // If you need dot syntax support, keep dotToBracket; else use key directly
                        const inputName = key; // or: dotToBracket(key)
                        const inputField = $(`[name='${inputName}']`);
                        const errorField = inputField.closest(".mb-3, .form-group, .color-row")
                            .find(".invalid-feedback").first();
                        inputField.addClass("is-invalid");
                        errorField.html(value);
                    });
                }
            });
        </script>
    @endpush
</x-layouts.app>
