<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Order;
use App\Models\User;
use Image;
use File;
use App\Models\Product;
use App\Models\Customer;
use Carbon\Carbon;
use Session;
use Toastr;
use Auth;
use DB;
class DashboardController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth')->except(['locked','unlocked']);
    }
    public function dashboard(){
        $total_order    = Order::count();
        $today_order    = Order::where('created_at', '>=', Carbon::today())->count();
        $total_product  = Product::count();
        $total_customer = Customer::count();

        // Widget config from session -- added 2026-05-02
        $ordersLimit  = session('dash_orders_limit', 5);
        $ordersStatus = session('dash_orders_status', 'all');
        $ordersQuery  = Order::latest()->with('customer','product','product.image');
        if ($ordersStatus !== 'all') $ordersQuery->where('order_status', $ordersStatus);
        $latest_order    = $ordersQuery->limit($ordersLimit)->get();
        $customersLimit  = session('dash_customers_limit', 5);
        $latest_customer = Customer::latest()->limit($customersLimit)->get();

        $today_delivery = Order::where(['order_status'=>'5'])->where('created_at', '>=', Carbon::today())->count();
        $total_delivery = Order::where(['order_status'=>'5'])->count();
        $last_week   = Order::where(['order_status'=>'5'])->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();
        $last_month  = Order::where(['order_status'=>'5'])->whereMonth('created_at', '=', Carbon::now()->subMonth()->month)->count();
        $monthly_sale = Order::select(DB::raw('DATE(created_at) as date','created_at'))->selectRaw("SUM(amount) as amount")->where(['order_status'=>'5'])->groupBy('date')->limit(30)->get();
        return view('backEnd.admin.dashboard', compact('total_order','today_order','total_product','total_customer','latest_order','latest_customer','today_delivery','total_delivery','last_week','last_month','monthly_sale','ordersLimit','ordersStatus','customersLimit'));
    }

    // Dashboard widget: orders config -- added 2026-05-02
    public function orderConfig(Request $request)
    {
        session([
            'dash_orders_limit'  => max(1, min(100, (int) $request->input('limit', 5))),
            'dash_orders_status' => $request->input('status', 'all'),
        ]);
        Toastr::success('Orders widget updated.', 'Success');
        return redirect()->route('dashboard');
    }

    public function orderReset()
    {
        session()->forget(['dash_orders_limit', 'dash_orders_status']);
        Toastr::success('Orders widget reset to default.', 'Success');
        return redirect()->route('dashboard');
    }

    public function orderExport($format)
    {
        $limit  = session('dash_orders_limit', 5);
        $status = session('dash_orders_status', 'all');
        $query  = Order::latest()->with('customer');
        if ($status !== 'all') $query->where('order_status', $status);
        $orders = $query->limit($limit)->get();

        if ($format === 'csv') {
            $headers = [
                'Content-Type'        => 'text/csv',
                'Content-Disposition' => 'attachment; filename="orders_report.csv"',
            ];
            return response()->stream(function() use ($orders) {
                $f = fopen('php://output', 'w');
                fputcsv($f, ['ID', 'Invoice', 'Amount', 'Customer', 'Status', 'Date']);
                foreach ($orders as $o) {
                    fputcsv($f, [
                        $o->id, $o->invoice_id, $o->amount,
                        $o->customer ? $o->customer->name : '',
                        $o->order_status,
                        $o->created_at->format('Y-m-d H:i'),
                    ]);
                }
                fclose($f);
            }, 200, $headers);
        }

        return view('backEnd.admin.dashboard_orders_pdf', compact('orders'));
    }

    // Dashboard widget: customers config -- added 2026-05-02
    public function customerConfig(Request $request)
    {
        session(['dash_customers_limit' => max(1, min(100, (int) $request->input('limit', 5)))]);
        Toastr::success('Customers widget updated.', 'Success');
        return redirect()->route('dashboard');
    }

    public function customerReset()
    {
        session()->forget('dash_customers_limit');
        Toastr::success('Customers widget reset to default.', 'Success');
        return redirect()->route('dashboard');
    }

    public function customerExport($format)
    {
        $limit     = session('dash_customers_limit', 5);
        $customers = Customer::latest()->limit($limit)->get();

        if ($format === 'csv') {
            $headers = [
                'Content-Type'        => 'text/csv',
                'Content-Disposition' => 'attachment; filename="customers_report.csv"',
            ];
            return response()->stream(function() use ($customers) {
                $f = fopen('php://output', 'w');
                fputcsv($f, ['ID', 'Name', 'Phone', 'Email', 'Status', 'Date']);
                foreach ($customers as $c) {
                    fputcsv($f, [
                        $c->id, $c->name, $c->phone ?? '',
                        $c->email ?? '', $c->status ?? 'active',
                        $c->created_at->format('Y-m-d'),
                    ]);
                }
                fclose($f);
            }, 200, $headers);
        }

        return view('backEnd.admin.dashboard_customers_pdf', compact('customers'));
    }
    public function changepassword(){
        return view('backEnd.admin.changepassword');
    }

    // Update profile -- added 2026-04-15
    public function profile()
    {
        $user = Auth::user();
        return view('backEnd.admin.profile', compact('user'));
    }

    public function profile_update(Request $request)
    {
        $this->validate($request, [
            'name'  => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $user  = User::find(Auth::id());
        $input = $request->only('name', 'email');

        if ($request->hasFile('image')) {
            // Delete old image if not default
            if ($user->image && !str_contains($user->image, 'default') && File::exists(public_path($user->image))) {
                File::delete(public_path($user->image));
            }

            $file     = $request->file('image');
            $filename = time() . '-' . preg_replace('/\s+/', '-', strtolower($file->getClientOriginalName()));
            $filename = preg_replace('/\.(jpg|jpeg|png|webp)$/i', '.webp', $filename);
            $path     = 'public/uploads/users/' . $filename;

            Image::make($file->getRealPath())->encode('webp', 90)->resize(200, 200)->save($path);
            $input['image'] = $path;
        }

        $user->update($input);

        Toastr::success('Profile updated successfully.', 'Success');
        return redirect()->route('admin.profile');
    }
     public function newpassword(Request $request)
    {
        $this->validate($request, [
            'old_password'=>'required',
            'new_password'=>'required',
            'confirm_password' => 'required_with:new_password|same:new_password|'
        ]);

        $user = User::find(Auth::id());
        $hashPass = $user->password;

        if (Hash::check($request->old_password, $hashPass)) {

            $user->fill([
                'password' => Hash::make($request->new_password)
            ])->save();

            Toastr::success('Success', 'Password changed successfully!');
            return redirect()->route('dashboard');
        }else{
            Toastr::error('Failed', 'Old password not match!');
            return back();
        }
    }
    public function locked(){
        // only if user is logged in
        
            Session::put('locked', true);
            return view('backEnd.auth.locked');
        

        return redirect()->route('login');
    }

    public function unlocked(Request $request)
    {
        if(!Auth::check())
            return redirect()->route('login');
        $password = $request->password;
        if(Hash::check($password,Auth::user()->password)){
            Session::forget('locked');
            Toastr::success('Success', 'You are logged in successfully!');
            return redirect()->route('dashboard');
        }
        Toastr::error('Failed', 'Your password not match!');
        return back();
    }
}
