<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetOtp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

// Added 2026-04-17
class AdminPasswordResetController extends Controller
{
    // Step 1 – show email form
    public function showForgotForm()
    {
        return view('backEnd.auth.forgot_password');
    }

    // Step 2 – send OTP
    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $email = $request->email;
        $existing = PasswordResetOtp::find($email);

        // Enforce 5-minute cooldown between requests
        if ($existing && !$existing->canRequestNew()) {
            $wait = now()->diffInSeconds($existing->next_request_at);
            return back()->withInput()->with('error', "Please wait {$wait} seconds before requesting a new OTP.");
        }

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        PasswordResetOtp::updateOrCreate(
            ['email' => $email],
            [
                'otp'             => Hash::make($otp),
                'verified'        => false,
                'expires_at'      => now()->addMinute(),
                'next_request_at' => now()->addMinutes(5),
            ]
        );

        Mail::send('emails.admin_otp', ['otp' => $otp, 'email' => $email], function ($m) use ($email) {
            $m->to($email)->subject('Your Password Reset OTP');
        });

        return redirect()->route('password.otp.verify.form')
            ->with('email', $email)
            ->with('success', 'OTP sent to your email. It expires in 1 minute.');
    }

    // Step 3 – show OTP form
    public function showVerifyForm(Request $request)
    {
        $email = session('email') ?? $request->email;
        return view('backEnd.auth.otp_verify', compact('email'));
    }

    // Step 4 – verify OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|digits:6',
        ]);

        $record = PasswordResetOtp::find($request->email);

        if (!$record) {
            return back()->withInput()->with('error', 'No OTP found. Please request a new one.');
        }

        if ($record->isExpired()) {
            return back()->withInput()->with('error', 'OTP has expired. Please request a new one.');
        }

        if (!Hash::check($request->otp, $record->otp)) {
            return back()->withInput()->with('error', 'Invalid OTP. Please try again.');
        }

        // Mark verified and store in session
        $record->update(['verified' => true]);
        session(['otp_verified_email' => $request->email]);

        return redirect()->route('password.new.form');
    }

    // Step 5 – show new password form
    public function showNewPasswordForm()
    {
        if (!session('otp_verified_email')) {
            return redirect()->route('password.request')->with('error', 'Please verify your OTP first.');
        }

        return view('backEnd.auth.reset_password');
    }

    // Step 6 – update password
    public function updatePassword(Request $request)
    {
        $email = session('otp_verified_email');

        if (!$email) {
            return redirect()->route('password.request')->with('error', 'Session expired. Please start again.');
        }

        $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        // Final check: record must still be verified
        $record = PasswordResetOtp::find($email);
        if (!$record || !$record->verified) {
            return redirect()->route('password.request')->with('error', 'OTP not verified. Please start again.');
        }

        User::where('email', $email)->update([
            'password' => Hash::make($request->password),
        ]);

        // Clean up
        $record->delete();
        session()->forget('otp_verified_email');

        return redirect()->route('login')->with('success', 'Password updated successfully. Please login.');
    }
}
