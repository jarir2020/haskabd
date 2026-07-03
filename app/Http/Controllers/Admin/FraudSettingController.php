<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Toastr;

// FraudBD Settings Controller -- added 2026-04-15
class FraudSettingController extends Controller
{
    public function index()
    {
        $apiKey = env('FRAUDBD_API_KEY', '');
        return view('backEnd.settings.fraud', compact('apiKey'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'api_key' => 'required|string',
        ]);

        $envPath    = base_path('.env');
        $envContent = file_get_contents($envPath);
        $value      = trim($request->api_key);

        if (str_contains($envContent, 'FRAUDBD_API_KEY=')) {
            $envContent = preg_replace('/^FRAUDBD_API_KEY=.*/m', "FRAUDBD_API_KEY={$value}", $envContent);
        } else {
            $envContent .= "\nFRAUDBD_API_KEY={$value}\n";
        }

        file_put_contents($envPath, $envContent);
        \Artisan::call('optimize:clear');

        Toastr::success('FraudBD API key saved.', 'Success');
        return redirect()->route('admin.fraud.setting');
    }
}
