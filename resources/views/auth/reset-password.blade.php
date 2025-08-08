<x-layouts.guest>
    <x-slot name="title">
        Login
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
                                <h4 class="text-uppercase mt-0">Reset Password</h4>
                            </div>

                            <form id="form" action="{{ route('password.update') }}" method="POST">
                                @csrf

                                <input type="hidden" name="token" value="{{ $token }}">
                                <input type="hidden" name="email" value="{{ $email }}">

                                <div class="mb-3 position-relative">
                                    <label for="password" class="form-label">Password</label>
                                    <input class="form-control pe-4" type="password" name="password" id="password"
                                        placeholder="Password">
                                    <button class="btn position-absolute end-0 border-0 bg-transparent" type="button"
                                        id="togglePassword" style="z-index: 10; top: 32px;">
                                        <i class="fa-regular fa-eye text-muted" id="togglePasswordIcon"></i>
                                    </button>
                                    <div class="error"></div>
                                </div>
                                <div class="mb-3 position-relative">
                                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                                    <input class="form-control pe-4" type="password" name="password_confirmation"
                                        id="password_confirmation" placeholder="Confirm password">
                                    <button class="btn position-absolute end-0 border-0 bg-transparent" type="button"
                                        id="togglePasswordConfirm" style="z-index: 10; top: 32px;">
                                        <i class="fa-regular fa-eye text-muted" id="togglePasswordConfirmIcon"></i>
                                    </button>
                                    <div class="error"></div>
                                </div>


                                <div class="mb-3 d-grid text-center">
                                    <button class="btn btn-primary" type="submit"> Reset </button>
                                </div>
                            </form>

                        </div> <!-- end card-body -->
                    </div>
                    <!-- end card -->
                </div> <!-- end col -->
            </div>
            <!-- end row -->
        </div>
        <!-- end container -->
    </div>


    @push('scripts')
        <script>
            $('#form').submit(function(event) {
                event.preventDefault();

                let $form = $(this);
                let $submitBtn = $form.find('button[type="submit"]');
                let formData = new FormData(this);

                $submitBtn.prop('disabled', true);

                $.ajax({
                    url: $form.attr('action'),
                    type: 'post',
                    data: formData,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $submitBtn.prop('disabled', false);

                        if (response['status'] == true) {
                            if (response.redirect) {
                                window.location.href = response.redirect;
                            } else {
                                location.reload();
                            }
                        } else {
                            let errors = response['errors'] || {};

                            // Clear previous errors
                            $('.error').removeClass('invalid-feedback').html('');
                            $("input").removeClass('is-invalid');

                            $.each(errors, function(key, value) {
                                let inputField = $(`#${key}`);
                                let errorField = inputField.closest('.mb-3').find('.error');
                                inputField.addClass('is-invalid');
                                errorField.addClass('invalid-feedback').html(value);
                            });

                            $("input").on('input', function() {
                                $(this).removeClass('is-invalid').siblings('.error').removeClass(
                                    'invalid-feedback').html('');
                            });
                        }
                    },
                    error: function(xhr) {
                        $submitBtn.prop('disabled', false);

                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            let errors = xhr.responseJSON.errors;

                            // Clear previous errors
                            $('.error').removeClass('invalid-feedback').html('');
                            $("input").removeClass('is-invalid');

                            $.each(errors, function(key, value) {
                                let inputField = $(`#${key}`);
                                let errorField = inputField.closest('.mb-3').find('.error');
                                inputField.addClass('is-invalid');
                                errorField.addClass('invalid-feedback').html(value);
                            });

                            $("input").on('input', function() {
                                $(this).removeClass('is-invalid').siblings('.error').removeClass(
                                    'invalid-feedback').html('');
                            });
                        } else {
                            console.log("Something went wrong. Please try again.");
                        }
                    }
                });
            });
        </script>
    @endpush
</x-layouts.guest>
