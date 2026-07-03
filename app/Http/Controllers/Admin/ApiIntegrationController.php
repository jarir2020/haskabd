<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentGateway;
use App\Models\SmsGateway;
use App\Models\Courierapi;
use Illuminate\Support\Facades\Http;
use Toastr;
use File;
use Str;
use Image;
use DB;

class ApiIntegrationController extends Controller
{
    
     
    public function pay_manage ()
    {
        $bkash = PaymentGateway::where('type','=','bkash')->first();
        $shurjopay = PaymentGateway::where('type','=','shurjopay')->first();
        return view('backEnd.apiintegration.pay_manage',compact('bkash','shurjopay'));
    }
    
    public function pay_update(Request $request)
    {
      
        $update_data = PaymentGateway::find($request->id);
        $input = $request->all();
        $input['status'] = $request->status?1:0;
        $update_data->update($input);
        
        Toastr::success('Success','Data update successfully');
        return redirect()->back();
    }
    
    public function sms_manage ()
    {  
        $sms = SmsGateway::first();
        return view('backEnd.apiintegration.sms_manage',compact('sms'));
    }
    
    public function sms_update(Request $request)
    {
      
        $update_data = SmsGateway::find($request->id);
        $input = $request->all();
        $input['status'] = $request->status?1:0;
        $input['order'] = $request->order?1:0;
        $input['forget_pass'] = $request->forget_pass?1:0;
        $input['password_g'] = $request->password_g?1:0;
        $update_data->update($input);
        
        Toastr::success('Success','Data update successfully');
        return redirect()->back();
    }
    
    public function courier_manage ()
    {
        $steadfast = Courierapi::where('type','=','steadfast')->first();
        $pathao = Courierapi::where('type','=','pathao')->first();
        return view('backEnd.apiintegration.courier_manage',compact('steadfast','pathao'));
    }
    
    public function courier_update (Request $request)
    {

        $update_data = Courierapi::find($request->id);
        $input = $request->all();
        $input['status'] = $request->status?1:0;
        $update_data->update($input);

        Toastr::success('Success','Data update successfully');
        return redirect()->back();
    }

    // Fetch Pathao access token using stored credentials -- added 2026-04-15
    public function pathaoRefreshToken(Request $request)
    {
        $pathao = Courierapi::where('type', 'pathao')->first();

        if (!$pathao) {
            return response()->json(['error' => 'Pathao record not found.'], 404);
        }

        $clientId     = $pathao->api_key;
        $clientSecret = $pathao->secret_key;
        $username     = $pathao->username;
        $password     = $pathao->password;
        $baseUrl      = rtrim($pathao->url, '/');

        if (!$clientId || !$clientSecret || !$username || !$password) {
            return response()->json(['error' => 'Fill in Client ID, Client Secret, Username (email) and Password first.'], 422);
        }

        try {
            $response = Http::withHeaders([
                'Accept'       => 'application/json',
                'Content-Type' => 'application/json',
            ])->post($baseUrl . '/api/v1/issue-token', [
                'client_id'     => $clientId,
                'client_secret' => $clientSecret,
                'username'      => $username,
                'password'      => $password,
                'grant_type'    => 'password',
            ]);

            $data = $response->json();

            if ($response->successful() && isset($data['access_token'])) {
                $pathao->token = $data['access_token'];
                $pathao->save();

                return response()->json([
                    'success'    => true,
                    'message'    => 'Token refreshed successfully.',
                    'expires_in' => $data['expires_in'] ?? null,
                ]);
            }

            return response()->json([
                'error' => $data['message'] ?? $data['error'] ?? 'Pathao returned an error.',
            ], 422);

        } catch (\Exception $e) {
            \Log::error('Pathao token refresh failed: ' . $e->getMessage());
            return response()->json(['error' => 'Could not connect to Pathao. Check the URL.'], 500);
        }
    }
}