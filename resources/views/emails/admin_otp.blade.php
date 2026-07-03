<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Password Reset OTP</title>
</head>
<body style="margin:0;padding:0;background:#f4f4f4;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;">
<div style="background:#f4f4f4;width:100%;padding:40px 0;">
    <div style="background:#fff;width:480px;margin:0 auto;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.08);">

        <!-- Header -->
        <div style="background:#4a6cf7;padding:30px;text-align:center;">
            <h2 style="color:#fff;margin:0;font-size:22px;">Password Reset OTP</h2>
        </div>

        <!-- Body -->
        <div style="padding:36px 40px;">
            <p style="color:#555;font-size:14px;margin:0 0 20px;">
                You requested a password reset for <strong>{{ $email }}</strong>.<br>
                Use the OTP below to proceed. It is valid for <strong>1 minute</strong>.
            </p>

            <!-- OTP Box -->
            <div style="text-align:center;margin:28px 0;">
                <span style="display:inline-block;background:#f0f4ff;border:2px dashed #4a6cf7;border-radius:8px;padding:18px 40px;font-size:36px;font-weight:bold;letter-spacing:12px;color:#4a6cf7;">
                    {{ $otp }}
                </span>
            </div>

            <p style="color:#888;font-size:12px;margin:20px 0 0;text-align:center;">
                If you did not request this, please ignore this email.<br>
                For security, do not share this OTP with anyone.
            </p>
        </div>

        <!-- Footer -->
        <div style="background:#f9f9f9;padding:16px;text-align:center;border-top:1px solid #eee;">
            <p style="color:#aaa;font-size:11px;margin:0;">This OTP expires in 1 minute. A new OTP can be requested after 5 minutes.</p>
        </div>
    </div>
</div>
</body>
</html>
