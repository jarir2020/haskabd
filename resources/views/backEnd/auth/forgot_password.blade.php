<!doctype html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" href="{{asset($generalsetting->favicon)}}" alt="{{$generalsetting->name}}" />
    <title>Forgot Password | {{$generalsetting->name}}</title>
    <link rel="stylesheet" href="{{asset('public/cdn/fonts/poppins/font.css')}}">
    <link rel="stylesheet" href="{{asset('public/backEnd/')}}/assets_login/css/vendors.css">
    <link rel="stylesheet" href="{{asset('public/backEnd/')}}/assets_login/css/aiz-core.css">
    <style>body { font-size: 12px; }</style>
</head>
<body class="">
<div class="aiz-main-wrapper d-flex">
    <div class="flex-grow-1">
        <div class="h-100 bg-cover bg-center py-5 d-flex align-items-center" style="background-image: url({{asset('public/backEnd/')}}/assets_login/img/background.jpg)">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 col-xl-4 mx-auto">
                        <div class="card text-left">
                            <div class="card-body">
                                <div class="mb-4 text-center">
                                    <img src="{{asset($generalsetting->dark_logo)}}" class="mw-100 mb-4" height="40">
                                    <h1 class="h3 text-primary mb-0">Forgot Password</h1>
                                    <p>Enter your email to receive a 6-digit OTP.</p>
                                </div>

                                @if(session('error'))
                                    <div class="alert alert-danger">{{ session('error') }}</div>
                                @endif
                                @if(session('success'))
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                @endif

                                <form method="POST" action="{{ route('password.send.otp') }}">
                                    @csrf
                                    <div class="form-group">
                                        <input id="email" type="email"
                                            class="form-control @error('email') is-invalid @enderror"
                                            name="email" value="{{ old('email') }}"
                                            required autofocus placeholder="Email Address">
                                        @error('email')
                                            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-lg btn-block">
                                        Send OTP
                                    </button>
                                </form>

                                <div class="text-center mt-3">
                                    <a href="{{ route('login') }}" class="text-reset fs-14">Back to Login</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{{asset('public/backEnd/')}}/assets_login/js/vendors.js"></script>
<script src="{{asset('public/backEnd/')}}/assets_login/js/aiz-core.js"></script>
</body>
</html>
