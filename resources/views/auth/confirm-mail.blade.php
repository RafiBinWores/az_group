<x-layouts.guest>
    <x-slot name="title">
        Confirm Email
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
                    <div class="card text-center">

                            <div class="card-body p-4">
                                
                                <div class="mb-4">
                                    <h4 class="text-uppercase mt-0">Confirm Email</h4>
                                </div>
                                <img src="{{ asset('assets/images/mail_confirm.png') }}" alt="img" width="86" class="mx-auto d-block">

                                <p class="text-muted font-14 mt-2"> A email has been send to your <b>Email</b>.
                                    Please check for an email from company and click on the included link to
                                    reset your password. </p>

                                <a href="{{ route('login') }}" class="btn d-block btn-primary waves-effect waves-light mt-3">Back to login</a>

                            </div> <!-- end card-body -->
                        </div>
                </div> <!-- end col -->
            </div>
            <!-- end row -->
        </div>
        <!-- end container -->
    </div>
</x-layouts.guest>
