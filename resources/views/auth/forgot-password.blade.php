<x-layouts.guest>
    <x-slot name="title">
        Forgot Password
    </x-slot>

    <div class="account-pages my-5">
        <div class="container">

            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-4">
                    <div class="text-center mb-4">
                        <a href="{{ route('dashboard.index') }}" class="">
                            <img src="{{ asset('assets/images/logo.png') }}" alt="" height="100"
                                class="mx-auto">
                        </a>
                    </div>
                    <div class="card">
                        <div class="card-body p-4">

                            <div class="text-center mb-4">
                                <h4 class="text-uppercase mt-0 mb-3">Reset Password</h4>
                                <p class="text-muted mb-0 font-13">Enter your email address and we'll send you an email
                                    with instructions to reset your password. </p>
                            </div>

                            {{-- Alert --}}
                            {{-- Alert --}}
                            <div id="alert-box" class="alert alert-danger alert-dismissible fade d-none"
                                role="alert">
                                <span id="alert-message"></span>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>


                            <form action="{{ route('password.email') }}" id="form" method="POST"
                                class="needs-validation" novalidate>
                                @csrf

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email address</label>
                                    <input class="form-control" type="email" id="email" name="email" required
                                        placeholder="Enter your email">
                                    <div class="error"></div>
                                </div>

                                <button
                                    class="btn btn-primary d-flex align-items-center justify-content-center w-100 gap-2"
                                    id="reset-btn" type="submit">
                                    <span id="btn-spinner" class="spinner-border spinner-border-sm d-none"
                                        role="status" aria-hidden="true"></span>
                                    <span id="btn-text">Reset Password</span>

                                </button>
                            </form>

                        </div> <!-- end card-body -->
                    </div>
                    <div class="row mt-3">
                        <div class="col-12 text-center">
                            <p class="text-muted">Back to <a href="{{ route('login') }}" class="text-dark ms-1"><b>Log
                                        in</b></a></p>
                        </div> <!-- end col -->
                    </div>
                </div> <!-- end col -->
            </div>
            <!-- end row -->
        </div>
        <!-- end container -->
    </div>


    @push('scripts')
        <script>
            $(function() {
                // Remove validation feedback on input change, once per page load
                $("input").on('input', function() {
                    $(this)
                        .removeClass('is-invalid')
                        .siblings('.error')
                        .removeClass('invalid-feedback')
                        .html('');
                });

                $('#form').submit(function(event) {
                    event.preventDefault();

                    // UI Elements
                    const $form = $(this);
                    const $submitBtn = $('#reset-btn');
                    const $btnText = $('#btn-text');
                    const $btnSpinner = $('#btn-spinner');
                    const $alertBox = $('#alert-box');
                    const $alertMsg = $('#alert-message');

                    // Utility to reset button and spinner
                    function resetButton() {
                        $btnSpinner.addClass('d-none');
                        $btnText.text('Reset Password');
                        $submitBtn.prop('disabled', false);
                    }

                    // Utility to show alert
                    function showAlert(msg) {
                        $alertMsg.html(msg);
                        $alertBox.removeClass('d-none').addClass('show');
                    }

                    // Reset alert and errors before sending
                    $alertBox.addClass('d-none');
                    $alertMsg.html('');
                    $('.error').removeClass('invalid-feedback').html('');
                    $("input").removeClass('is-invalid');

                    // Show spinner and disable button
                    $btnSpinner.removeClass('d-none');
                    $btnText.text('Sending...');
                    $submitBtn.prop('disabled', true);

                    $.ajax({
                        url: $form.attr('action'),
                        type: 'POST',
                        data: new FormData(this),
                        dataType: 'json',
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            resetButton();

                            if (response.status === true) {
                                if (response.redirect) {
                                    window.location.href = response.redirect;
                                } else {
                                    location.reload();
                                }
                            } else {
                                // Show server general error in alert
                                if (response.message) showAlert(response.message);

                                // Show validation errors beside fields
                                if (response.errors) {
                                    $.each(response.errors, function(key, value) {
                                        const $input = $(`#${key}`);
                                        $input.addClass('is-invalid');
                                        $input.closest('.mb-3').find('.error')
                                            .addClass('invalid-feedback')
                                            .html(value);
                                    });
                                }
                            }
                        },
                        error: function(xhr) {
                            resetButton();

                            // Validation error
                            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                                $.each(xhr.responseJSON.errors, function(key, value) {
                                    const $input = $(`#${key}`);
                                    $input.addClass('is-invalid');
                                    $input.closest('.mb-3').find('.error')
                                        .addClass('invalid-feedback')
                                        .html(value);
                                });
                                return;
                            }

                            // Show all other errors in alert
                            let msg = 'Something went wrong. Please try again.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                msg = xhr.responseJSON.message;
                            } else if (xhr.status === 404) {
                                msg = "Email not found.";
                            }
                            showAlert(msg);
                        }
                    });
                });
            });
        </script>
    @endpush
</x-layouts.guest>
