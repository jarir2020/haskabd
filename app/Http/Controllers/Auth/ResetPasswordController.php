<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    // Redirect to admin dashboard after password reset -- added 2026-04-16
    protected $redirectTo = '/admin/dashboard';

    // Use admin-styled reset password view -- added 2026-04-16
    public function showResetForm(Request $request)
    {
        $token = $request->route()->parameter('token');

        return view('backEnd.auth.reset_password')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }
}
