@extends('layout/app')

@section('content')

    <div class="row">
        <div class="col-12">

            <div class="text-center">
                <a href="index.html" class="logo">
                    <img src="assets/images/logo-light.png" alt="" height="22" class="logo-light mx-auto">
                <img src="assets/images/logo-dark.png" alt="" height="22" class="logo-dark mx-auto">
                </a>

                <h3 class="mt-4">Stay tunned, we're launching very soon</h3>
                <p class="text-muted">We're making the system more awesome.</p>

            </div>
        </div>
    </div>
    <div class="row mt-5 justify-content-center">
        <div class="col-md-8 text-center">
            <div data-countdown="2021/01/17" class="counter-number"></div>
        </div> <!-- end col-->
    </div>
@endsection