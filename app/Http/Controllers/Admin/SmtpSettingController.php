<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Toastr;

// SMTP Settings Controller -- added 2026-04-15
class SmtpSettingController extends Controller
{
    public function index()
    {
        $smtp = [
            'MAIL_MAILER'       => env('MAIL_MAILER', 'smtp'),
            'MAIL_HOST'         => env('MAIL_HOST', ''),
            'MAIL_PORT'         => env('MAIL_PORT', '587'),
            'MAIL_USERNAME'     => env('MAIL_USERNAME', ''),
            'MAIL_PASSWORD'     => env('MAIL_PASSWORD', ''),
            'MAIL_ENCRYPTION'   => env('MAIL_ENCRYPTION', 'tls'),
            'MAIL_FROM_ADDRESS' => env('MAIL_FROM_ADDRESS', ''),
            'MAIL_FROM_NAME'    => env('MAIL_FROM_NAME', ''),
        ];

        return view('backEnd.settings.smtp', compact('smtp'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'MAIL_HOST'         => 'required',
            'MAIL_PORT'         => 'required|numeric',
            'MAIL_USERNAME'     => 'required|email',
            'MAIL_FROM_ADDRESS' => 'required|email',
            'MAIL_FROM_NAME'    => 'required',
        ]);

        $envPath = base_path('.env');
        $envContent = file_get_contents($envPath);

        $keys = [
            'MAIL_MAILER',
            'MAIL_HOST',
            'MAIL_PORT',
            'MAIL_USERNAME',
            'MAIL_PASSWORD',
            'MAIL_ENCRYPTION',
            'MAIL_FROM_ADDRESS',
            'MAIL_FROM_NAME',
        ];

        foreach ($keys as $key) {
            $value = $request->input($key, '');

            // Wrap in quotes if value contains spaces or special characters
            $needsQuotes = preg_match('/[\s+@#&%^*(){}|<>!]/', $value);
            $formatted   = $needsQuotes ? "'{$value}'" : $value;

            // Replace existing key=value (with or without quotes)
            $envContent = preg_replace(
                "/^{$key}=.*/m",
                "{$key}={$formatted}",
                $envContent
            );
        }

        file_put_contents($envPath, $envContent);

        // Clear config cache so new values take effect
        \Artisan::call('optimize:clear');

        Toastr::success('SMTP settings updated successfully.', 'Success');
        return redirect()->route('admin.smtp.index');
    }
}
