<!doctype html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" href="{{asset($generalsetting->favicon)}}" alt="{{$generalsetting->name}}" />
    <title>Verify OTP | {{$generalsetting->name}}</title>
    <link rel="stylesheet" href="{{asset('public/cdn/fonts/poppins/font.css')}}">
    <link rel="stylesheet" href="{{asset('public/backEnd/')}}/assets_login/css/vendors.css">
    <link rel="stylesheet" href="{{asset('public/backEnd/')}}/assets_login/css/aiz-core.css">
    <style>
        body { font-size: 12px; }
        .otp-input { letter-spacing: 8px; font-size: 22px; text-align: center; font-weight: bold; }
        #countdown { font-weight: bold; color: #e74c3c; }
    </style>
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
                                    <h1 class="h3 text-primary mb-0">Enter OTP</h1>
                                    <p>A 6-digit OTP was sent to<br><strong>{{ $email }}</strong></p>
                                    <p class="mb-0">Expires in: <span id="countdown">01:00</span></p>
                                </div>

                                @if(session('error'))
                                    <div class="alert alert-danger">{{ session('error') }}</div>
                                @endif
                                @if(session('success'))
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                @endif

                                <form method="POST" action="{{ route('password.otp.verify') }}">
                                    @csrf
                                    <input type="hidden" name="email" value="{{ $email }}">
                                    <div class="form-group">
                                        <input id="otp" type="text" inputmode="numeric" maxlength="6"
                                            class="form-control otp-input @error('otp') is-invalid @enderror"
                                            name="otp" value="{{ old('otp') }}"
                                            required autofocus placeholder="______" autocomplete="off">
                                        @error('otp')
                                            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-lg btn-block">
                                        Verify OTP
                                    </button>
                                </form>

                                <div class="text-center mt-3">
                                    <p class="mb-1 text-muted">Didn't receive it?</p>
                                    <form method="POST" action="{{ route('password.send.otp') }}" id="resend-form">
                                        @csrf
                                        <input type="hidden" name="email" value="{{ $email }}">
                                        <button type="submit" class="btn btn-link p-0 fs-14" id="resend-btn" disabled>
                                            Resend OTP <span id="resend-timer"></span>
                                        </button>
                                    </form>
                                </div>

                                <div class="text-center mt-2">
                                    <a href="{{ route('password.request') }}" class="text-reset fs-14">Use different email</a>
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
<script>
// OTP expiry countdown (1 min)
(function () {
    var otpSecs = 60;
    var otpEl = document.getElementById('countdown');
    var otpInterval = setInterval(function () {
        otpSecs--;
        if (otpSecs <= 0) {
            clearInterval(otpInterval);
            otpEl.textContent = 'Expired';
            otpEl.style.color = '#e74c3c';
            return;
        }
        var m = Math.floor(otpSecs / 60);
        var s = otpSecs % 60;
        otpEl.textContent = (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
    }, 1000);
})();

// Resend cooldown (5 min)
(function () {
    var resendSecs = 300;
    var resendBtn = document.getElementById('resend-btn');
    var resendTimer = document.getElementById('resend-timer');
    var resendInterval = setInterval(function () {
        resendSecs--;
        if (resendSecs <= 0) {
            clearInterval(resendInterval);
            resendBtn.disabled = false;
            resendTimer.textContent = '';
            return;
        }
        var m = Math.floor(resendSecs / 60);
        var s = resendSecs % 60;
        resendTimer.textContent = '(' + (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s + ')';
    }, 1000);
})();
</script>
</body>
</html>
