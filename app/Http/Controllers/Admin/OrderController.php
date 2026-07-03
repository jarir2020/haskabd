<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\District;
use App\Models\OrderStatus;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Shipping;
use App\Models\ShippingCharge;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Models\Courierapi;
use App\Models\SmsGateway;
use App\Models\GeneralSetting;
use Illuminate\Support\Facades\Session;
use Cart;
use Brian2694\Toastr\Facades\Toastr;
use Mail;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index($slug,Request $request){
        // DataTables handles search/pagination client-side -- updated 2026-05-02
        if($slug == 'all'){
            $order_status = (object) ['name' => 'All', 'orders_count' => Order::count()];
            $show_data    = Order::latest()->with('shipping','status','customer')->get();
        }else{
            $order_status = OrderStatus::where('slug',$slug)->withCount('orders')->first();
            $show_data    = Order::where(['order_status'=>$order_status->id])->latest()->with('shipping','status','customer')->get();
        }
        $all_ids = $show_data->pluck('id')->toArray();
        $users = User::select('id','name')->get();
        $steadfast = Courierapi::where(['status'=>1, 'type'=>'steadfast'])->first();
        $pathao_info = Courierapi::where(['status'=>1, 'type'=>'pathao'])->select('id', 'type', 'url', 'token', 'status')->first();
        // pathao courier
        if($pathao_info) {
            $response = Http::get($pathao_info->url . '/api/v1/countries/1/city-list');
            $pathaocities = $response->json();
            $response2 = Http::withHeaders([
                'Authorization' => 'Bearer ' . $pathao_info->token,
                'Content-Type' => 'application/json',
                ])->get($pathao_info->url . '/api/v1/stores');
            $pathaostore = $response2->json();
        } else {
            $pathaocities = [];
            $pathaostore = [];
        }
        return view('backEnd.order.index',compact('show_data','order_status','users','steadfast','pathaostore','pathaocities','all_ids'));
    } 
    
    public function pathaocity(Request $request)
    {
        $pathao_info = Courierapi::where(['status'=>1, 'type'=>'pathao'])->select('id', 'type', 'url', 'token', 'status')->first();
        if($pathao_info) {
            $response = Http::get($pathao_info->url . '/api/v1/cities/'.$request->city_id.'/zone-list');
            $pathaozones = $response->json();
            return response()->json($pathaozones);
        } else {
            return response()->json([]);
        }
    } 
    public function pathaozone(Request $request)
    {
        $pathao_info = Courierapi::where(['status'=>1, 'type'=>'pathao'])->select('id', 'type', 'url', 'token', 'status')->first();
        if($pathao_info) {
            $response = Http::get($pathao_info->url . '/api/v1/zones/'.$request->zone_id.'/area-list');
            $pathaoareas = $response->json();
            return response()->json($pathaoareas);
        } else {
             return response()->json([]);
        }
    }
    
    public function order_pathao(Request $request)
    {
        $orders_id = $request->order_ids;

        foreach ($orders_id as $order_id) {
            $order = Order::with('shipping')->find($order_id);
            $order_count = OrderDetails::select('order_id')->where('order_id', $order->id)->count();
            // return $request->all();
            // pathao
            $pathao_info = Courierapi::where(['status' => 1, 'type' => 'pathao'])->select('id', 'type', 'url', 'token', 'status')->first();
            if ($pathao_info) {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $pathao_info->token,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])->post($pathao_info->url . '/api/v1/orders', [
                    'store_id' => $request->pathaostore,
                    'merchant_order_id' => $order->invoice_id,
                    'sender_name' => 'Test',
                    'sender_phone' => $order->shipping ? $order->shipping->phone : '',
                    'recipient_name' => $order->shipping ? $order->shipping->name : '',
                    'recipient_phone' => $order->shipping ? $order->shipping->phone : '',
                    'recipient_address' => $order->shipping ? $order->shipping->address : '',
                    'recipient_city' => $request->pathaocity,
                    'recipient_zone' => $request->pathaozone,
                    'recipient_area' => $request->pathaoarea,
                    'delivery_type' => 48,
                    'item_type' => 2,
                    'special_instruction' => 'Special note- product must be check after delivery',
                    'item_quantity' => 1,
                    'item_weight' => 0.5,
                    'amount_to_collect' => round($order->amount),
                    'item_description' => 'Special note- product must be check after delivery',
                ]);
            }
            if ($response->status() == '200') {
                Toastr::success($response['data']['consignment_id'], 'Courier Tracking ID');
                return response()->json(['status' => 'success', 'message' => $response['data']['consignment_id'], 'Courier Tracking ID']);
            } else {
                Toastr::error($response['message'], 'Courier Order Faild');
                return response()->json(['status' => 'failed', 'message' => $response['message'], 'Courier Order Faild']);
            }
            return redirect()->back();


        }
    }
    
    public function invoice($invoice_id){
        $order = Order::where(['invoice_id'=>$invoice_id])->with('orderdetails','payment','shipping','customer')->firstOrFail();
       
        return view('backEnd.order.invoice',compact('order'));
    }
    
    public function process($invoice_id){
        $data = Order::where(['invoice_id'=>$invoice_id])->select('id','invoice_id','order_status')->with('orderdetails')->first();
        $shippingcharge = ShippingCharge::where('status',1)->get();
        return view('backEnd.order.process',compact('data','shippingcharge'));
    }
    
    public function order_process(Request $request)
    {
        $link = OrderStatus::find($request->status)->slug;
        // Eager load orderdetails with product to avoid N+1 -- fixed 2026-04-15
        $order = Order::with('orderdetails.product')->find($request->id);
        $previousStatus = $order->order_status;


        $courier = $order->order_status;
        $order->order_status = $request->status;
        $order->admin_note = $request->admin_note;
        $order->save();

        $shipping_update = Shipping::where('order_id', $order->id)->first();
        $shippingfee = ShippingCharge::find($request->area);
        if ($shippingfee->name != $request->area) {
            if ($order->shipping_charge > $shippingfee->amount) {
                $total = $order->amount + ($shippingfee->amount - $order->shipping_charge);
                $order->shipping_charge = $shippingfee->amount;
                $order->amount = $total;
                $order->save();
            } else {
                $total = $order->amount + ($shippingfee->amount - $order->shipping_charge);
                $order->shipping_charge = $shippingfee->amount;
                $order->amount = $total;
                $order->save();
            }
        }

        $shipping_update->name = $request->name;
        $shipping_update->phone = $request->phone;
        $shipping_update->address = $request->address;
        $shipping_update->area = $shippingfee->name;
        $shipping_update->save();

        // যখন Complete করা হচ্ছে → sold count বাড়বে
        if ($request->status == 6 && $previousStatus != 6) {
            foreach ($order->orderdetails as $detail) {
                $product = $detail->product; // already eager loaded -- fixed N+1 2026-04-15
                if ($product) {
                    $increment = $detail->qty ?? 1;
                    $product->sold = ($product->sold ?? 0) + $increment;
                    $product->save();
                }
            }
        }

        // যখন Complete থেকে অন্য কোনো status করা হচ্ছে → sold count কমবে
        if ($previousStatus == 6 && $request->status != 6) {
            foreach ($order->orderdetails as $detail) {
                $product = $detail->product; // already eager loaded -- fixed N+1 2026-04-15
                if ($product) {
                    $decrement = $detail->qty ?? 1;
                    $product->sold = max(0, ($product->sold ?? 0) - $decrement);
                    $product->save();
                }
            }
        }



        if ($request->status == 5 && $courier != 5) {
            $courier_info = Courierapi::where(['status' => 1, 'type' => 'steadfast'])->first();
            if ($courier_info) {
                $consignmentData = [
                    'invoice' => $order->invoice_id,
                    'recipient_name' => $order->shipping ? $order->shipping->name : 'InboxHat',
                    'recipient_phone' => $order->shipping ? $order->shipping->phone : '01750578495',
                    'recipient_address' => $order->shipping ? $order->shipping->address : '01750578495',
                    'cod_amount' => $order->amount
                ];
                $client = new Client();
                $response = $client->post($courier_info->url, [
                    'json' => $consignmentData,
                    'headers' => [
                        'Api-Key' => $courier_info->api_key,
                        'Secret-Key' => $courier_info->secret_key,
                        'Accept' => 'application/json',
                    ],
                ]);
                
                $responseData = json_decode($response->getBody(), true);
            } else {
                return "ok";
            }
            Toastr::success('Success', 'Order status change successfully');
            return redirect('admin/order/' . $link);
        }
		        Toastr::success('Success', 'Order status change successfully');
        return redirect('admin/order/' . $link);
    }
    
    public function destroy(Request $request){
        $order = Order::where('id',$request->id)->delete();
        $order_details = OrderDetails::where('order_id',$request->id)->delete();
        $shipping = Shipping::where('order_id',$request->id)->delete();
        $payment = Payment::where('order_id',$request->id)->delete();
        Toastr::success('Success','Order delete success successfully');
        return redirect()->back();
    }
    
    public function order_assign(Request $request){
        $products = Order::whereIn('id', $request->input('order_ids'))->update(['user_id' => $request->user_id]);
        return response()->json(['status'=>'success','message'=>'Order user id assign']);
    }
    
    public function order_status(Request $request){
        
        $sms_gateway = SmsGateway::where('status', 1)->first();
        $site_setting = GeneralSetting::where('status', 1)->first();
        // Update order statuses
        $orders = Order::whereIn('id', $request->input('order_ids'))->with('customer', 'orderdetails.product')->get(); // eager load -- fixed N+1 2026-04-15
        
        foreach ($orders as $order) {
            $previousStatus = $order->order_status;
            $order->order_status= $request->order_status;
            $order->update();
            $orderStatus = OrderStatus::find($request->order_status);

            //Send SMS to the customer
            if ($sms_gateway) {
                $customer_info = $order->customer; // eager loaded -- fixed N+1 2026-04-15
                if ($customer_info) {
                    $url = $sms_gateway->url;
                    $data = [
                        "api_key" => $sms_gateway->api_key,
                        "number" => $customer_info->phone,
                        "type" => 'text',
                        "senderid" => $sms_gateway->serderid,
                        "message" => "Dear {$customer_info->name},\r\n"
                                  . "Your order (Order ID: {$order->invoice_id}) status has been updated to: "
                                  . "{$orderStatus->name}.\r\n"
                                  . "Thank you for using {$site_setting->name}!",
                    ];
    
                    // cURL request to send SMS
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    $response = curl_exec($ch);
                    curl_close($ch);
                }
            }

        if ($request->order_status == 6 && $previousStatus != 6) {
            foreach ($order->orderdetails as $detail) {
                $new_product = $detail->product; // eager loaded -- fixed N+1 2026-04-15
                if ($new_product) {
                    $increment = $detail->qty ?? 1; // fallback to 1 if qty is null
                    $new_product->sold = $new_product->sold ? $new_product->sold + $increment : $increment;
                    $new_product->save();
                }
            }
        }

        // যখন Complete থেকে অন্য কোনো status করা হচ্ছে → sold count কমবে
        if ($previousStatus == 6 && $request->order_status != 6) {
            foreach ($order->orderdetails as $detail) {
                $new_product = $detail->product; // eager loaded -- fixed N+1 2026-04-15
                if ($new_product) {
                    $decrement = $detail->qty ?? 1; // fallback to 1 if qty is null
                    // sold মান 0 এর নিচে না যায় সেটা চেক করা হচ্ছে
                    $new_product->sold = max(0, $new_product->sold - $decrement);
                    $new_product->save();
                }
            }
        }
     }
        if($request->order_status == 5){
            foreach($orders as $order){
                foreach($order->orderdetails as $order_details){ // eager loaded -- fixed N+1 2026-04-15
                    $product = $order_details->product; // eager loaded
                    if($product){
                        $product->stock -= $order_details->qty;
                        $product->save();
                    }
                }
            }
        }



        return response()->json(['status'=>'success','message'=>'Order status change successfully']);
    }
    
    public function bulk_destroy(Request $request){
        $orders_id = $request->order_ids;
        foreach($orders_id as $order_id){
            $order = Order::where('id',$order_id)->delete();
            $order_details = OrderDetails::where('order_id',$order_id)->delete();
            $shipping = Shipping::where('order_id',$order_id)->delete();
            $payment = Payment::where('order_id',$order_id)->delete();
        }
        return response()->json(['status'=>'success','message'=>'Order delete successfully']);
    }
    public function order_print(Request $request){
        $orders = Order::whereIn('id', $request->input('order_ids'))->with('orderdetails','payment','shipping','customer')->get();
        $view = view('backEnd.order.print', ['orders' => $orders])->render();
        return response()->json(['status' => 'success', 'view' => $view]);
    }
    // public function bulk_courier($slug, Request $request)
    // {
    //     $courier_info = Courierapi::where(['status' => 1, 'type' => $slug])->first();
        
    //     if ($courier_info) {
    //         $orders_ids = $request->order_ids;
            
    //         foreach ($orders_ids as $order_id) {
    //             $order = Order::find($order_id);
                
    //             $courier = $order->order_status;
    //             if ($request->status == 5 && $courier != 5) {
    //                 $consignmentData = [
    //                     'invoice' => $order->invoice_id,
    //                     'recipient_name' => $order->shipping ? $order->shipping->name : 'InboxHat',
    //                     'recipient_phone' => $order->shipping ? $order->shipping->phone : '01750578495',
    //                     'recipient_address' => $order->shipping ? $order->shipping->address : '01750578495',
    //                     'cod_amount' => $order->amount
    //                 ];
    //                 $client = new Client();
    //                 $response = $client->post('$courier_info->url', [
    //                     'json' => $consignmentData,
    //                     'headers' => [
    //                         'Api-Key' => '$courier_info->api_key',
    //                         'Secret-Key' => '$courier_info->secret_key',
    //                         'Accept' => 'application/json',
    //                     ],
    //                 ]);

    //                 $responseData = json_decode($response->getBody(), true);
    //                 if ($responseData['status'] == 200) {
    //                     $message = 'Your order place to courier successfully';
    //                     $status = 'success';
    //                     $order->order_status = 4;
    //                     $order->save();
    //                 } else {
    //                     $message = 'Your order place to courier failed';
    //                     $status = 'failed';
    //                 }
    //                 return response()->json(['status' => $status, 'message' => $message]);
    //             }
                
    //         }
    //     } else {
    //         return "stop";
    //     }
    // }
    // Bulk courier push (Steadfast etc.) -- updated 2026-04-15
    public function bulk_courier($slug, Request $request)
    {
        $courier_info = Courierapi::where(['status' => 1, 'type' => $slug])->first();

        if (!$courier_info) {
            return response()->json([
                'status'  => 'error',
                'message' => ucfirst($slug) . ' courier is not enabled. Go to API Integration → Courier API and enable it.',
            ]);
        }

        $orders_ids    = $request->order_ids ?? [];
        $successOrders = [];
        $failedOrders  = [];

        // Load all orders at once to avoid N+1
        $orders = Order::with('shipping')->whereIn('id', $orders_ids)->get()->keyBy('id');

        foreach ($orders_ids as $order_id) {
            $order = $orders->get($order_id);

            if (!$order) {
                $failedOrders[] = ['order_id' => $order_id, 'invoice' => '#'.$order_id, 'message' => 'Order not found.'];
                continue;
            }

            if ($request->status == 5 && $order->order_status == 5) {
                $failedOrders[] = ['order_id' => $order_id, 'invoice' => $order->invoice_id, 'message' => 'Already sent to courier.'];
                continue;
            }

            $consignmentData = [
                'invoice'           => $order->invoice_id,
                'recipient_name'    => $order->shipping->name    ?? 'N/A',
                'recipient_phone'   => $order->shipping->phone   ?? 'N/A',
                'recipient_address' => $order->shipping->address ?? 'N/A',
                'cod_amount'        => $order->amount,
            ];

            $client = new Client();
            try {
                // http_errors:false prevents Guzzle from throwing on 4xx/5xx
                $response = $client->post($courier_info->url, [
                    'http_errors' => false,
                    'json'        => $consignmentData,
                    'headers'     => [
                        'Api-Key'    => $courier_info->api_key,
                        'Secret-Key' => $courier_info->secret_key,
                        'Accept'     => 'application/json',
                    ],
                ]);

                $httpStatus   = $response->getStatusCode();
                $responseData = json_decode($response->getBody(), true) ?? [];

                if ($httpStatus === 401) {
                    // Stop processing further — all orders will fail with same error
                    $failedOrders[] = [
                        'order_id' => $order_id,
                        'invoice'  => $order->invoice_id,
                        'message'  => 'Invalid API credentials (401). Update your API Key & Secret Key in Courier Settings.',
                    ];
                    break;
                }

                if (($responseData['status'] ?? 0) == 200) {
                    $order->order_status = 5;
                    $order->save();
                    $successOrders[] = [
                        'order_id'        => $order_id,
                        'invoice'         => $order->invoice_id,
                        'consignment_id'  => $responseData['consignment']['consignment_id'] ?? '—',
                        'message'         => $responseData['message'] ?? 'Placed successfully',
                    ];
                } else {
                    $failedOrders[] = [
                        'order_id' => $order_id,
                        'invoice'  => $order->invoice_id,
                        'message'  => $responseData['message'] ?? 'Courier rejected this order (HTTP '.$httpStatus.').',
                    ];
                }

            } catch (\Exception $e) {
                \Log::error('bulk_courier ['.$slug.'] order #'.$order_id.': '.$e->getMessage());
                $failedOrders[] = [
                    'order_id' => $order_id,
                    'invoice'  => $order->invoice_id,
                    'message'  => 'Connection error. Check the courier API URL in settings.',
                ];
            }
        }

        return response()->json([
            'status'  => 'success',
            'success' => $successOrders,
            'failed'  => $failedOrders,
        ]);
    }
    public function stock_report(Request $request){
        $products = Product::select('id', 'name','new_price','stock')
            ->where('status', 1);
        if ($request->keyword) {
            $products = $products->where('name', 'LIKE', '%' . $request->keyword . "%");
        }
        if ($request->category_id) {
            $products = $products->where('category_id', $request->category_id);
        }
        if ($request->start_date && $request->end_date) {
            $products =$products->whereBetween('updated_at', [$request->start_date,$request->end_date]);
        }
        $total_purchase = $products->sum(\DB::raw('purchase_price * stock'));
        $total_stock = $products->sum('stock');
        $total_price = $products->sum(\DB::raw('new_price * stock'));
        $products = $products->paginate(100);
        $categories = Category::where('status',1)->get();
        return view('backEnd.reports.stock',compact('products','categories','total_purchase','total_stock','total_price'));
    }

public function order_report(Request $request)
{
    $users = User::where('status', 1)->get();

    // Base query (without pagination)
    $query = OrderDetails::with('shipping','order')
        ->whereHas('order', function ($q) {
            $q->where('order_status', 6);
        });

    // Filters
    if ($request->keyword) {
        $query->where('name', 'LIKE', '%' . $request->keyword . "%");
    }

    if ($request->user_id) {
        $query->whereHas('order', function ($q) use ($request) {
            $q->where('user_id', $request->user_id);
        });
    }

    if ($request->start_date && $request->end_date) {
        $query->whereBetween('updated_at', [$request->start_date, $request->end_date]);
    }

    // ❗ GRAND TOTALS (full result, not paginated)
    $grand_purchase = (clone $query)->sum(\DB::raw('purchase_price * qty'));
    $grand_sales    = (clone $query)->sum(\DB::raw('sale_price * qty'));
    $grand_qty      = (clone $query)->sum('qty');
    // discount is per-order — sum once per unique order_id
    $grand_order_ids = (clone $query)->pluck('order_id')->unique();
    $grand_discount  = \App\Models\Order::whereIn('id', $grand_order_ids)->sum('discount');

    // ❗ PAGINATED RESULT (current page only)
    $orders = $query->paginate(100)->appends($request->query());

    // ❗ PAGE TOTALS (only current page)
    $page_purchase = $orders->sum(function ($item) {
        return $item->purchase_price * $item->qty;
    });

    $page_sales = $orders->sum(function ($item) {
        return $item->sale_price * $item->qty;
    });

    $page_qty      = $orders->sum('qty');
    $page_order_ids = $orders->pluck('order_id')->unique();
    $page_discount  = \App\Models\Order::whereIn('id', $page_order_ids)->sum('discount');

    return view('backEnd.reports.order', compact(
        'orders', 'users',
        'page_purchase', 'page_sales', 'page_qty', 'page_discount',
        'grand_purchase', 'grand_sales', 'grand_qty', 'grand_discount'
    ));
}


    public function order_create(){
        $cartinfo  = Cart::instance('pos_shopping')->destroy();
        $products = Product::select('id','name','new_price','product_code')->where(['status'=>1])->get();
        $cartinfo  = Cart::instance('pos_shopping')->content();
        $shippingcharge = ShippingCharge::where('status',1)->get();
        return view('backEnd.order.create',compact('products','cartinfo','shippingcharge'));
    }
    
    public function order_store(Request $request){
        $this->validate($request,[
            'name'=>'required',
            'phone'=>'required',
            'address'=>'required',
            'area'=>'required',
        ]);

        if(Cart::instance('pos_shopping')->count() <= 0) {
            Toastr::error('Your shopping empty', 'Failed!');
            return redirect()->back();
        }

        $subtotal = Cart::instance('pos_shopping')->subtotal();
        $subtotal = str_replace(',','',$subtotal);
        $subtotal = str_replace('.00', '',$subtotal);
        $discount = Session::get('pos_discount')+Session::get('product_discount');
        $shippingfee  = ShippingCharge::find($request->area);
        
        $exits_customer = Customer::where('phone',$request->phone)->select('phone','id')->first();
        if($exits_customer){
            $customer_id = $exits_customer->id;
        }else{
            $password = rand(111111,999999);
            $store              = new Customer();
            $store->name        = $request->name;
            $store->slug        = $request->name;
            $store->phone       = $request->phone;
            $store->password    = bcrypt($password);
            $store->verify      = 1;
            $store->status      = 'active';
            $store->save();
            $customer_id = $store->id;
        }
 
         // order data save
        $order                   = new Order();
        $order->invoice_id       = rand(11111,99999);
        $order->amount           = ($subtotal + $shippingfee->amount) - $discount;
        $order->discount         = $discount ? $discount : 0;
        $order->shipping_charge  = $shippingfee->amount;
        $order->customer_id      =  $customer_id;
        $order->order_status     = 1;
        $order->note             = $request->note;
        $order->save();

        // shipping data save
        $shipping              =   new Shipping();
        $shipping->order_id    =   $order->id;
        $shipping->customer_id =   $customer_id;
        $shipping->name        =   $request->name;
        $shipping->phone       =   $request->phone;
        $shipping->address     =   $request->address;
        $shipping->area        =   $shippingfee->name;
        $shipping->save();

        // payment data save
        $payment                 = new Payment();
        $payment->order_id       = $order->id;
        $payment->customer_id    = $customer_id;
        $payment->payment_method = 'Cash On Delivery';
        $payment->amount         = $order->amount;
        $payment->payment_status = 'pending';
        $payment->save();

       // order details data save
        foreach(Cart::instance('pos_shopping')->content() as $cart){
            $order_details                   =   new OrderDetails();
            $order_details->order_id         =   $order->id;
            $order_details->product_id       =   $cart->id;
            $order_details->product_name     =   $cart->name;
            $order_details->purchase_price   =   $cart->options->purchase_price;
            $order_details->product_discount =   $cart->options->product_discount;
            $order_details->sale_price       =   $cart->price;
            $order_details->qty              =   $cart->qty;
            $order_details->save();
        }
        Cart::instance('pos_shopping')->destroy();
        Session::forget('pos_shipping');
        Session::forget('pos_discount');
        Session::forget('product_discount');
        Toastr::success('Thanks, Your order place successfully', 'Success!');
        return redirect('admin/order/pending');
    }
    public function cart_add(Request $request){
        $product = Product::select('id','name','stock','new_price','old_price','purchase_price','slug')->where(['id' => $request->id])->first();
        $qty = 1;
        $cartinfo = Cart::instance('pos_shopping')->add([
            'id' => $product->id,
            'name' => $product->name,
            'qty' => $qty,
            'price' => $product->new_price,
            'options' => [
                'slug' => $product->slug,
                'image' => $product->image->image,
                'old_price' => $product->old_price,
                'purchase_price' => $product->purchase_price,
                'product_discount' => 0,
            ],
        ]);
        return response()->json(compact('cartinfo'));
    }
    
    public function cart_content(){
        $cartinfo = Cart::instance('pos_shopping')->content()->sortBy('id');
        return view('backEnd.order.cart_content',compact('cartinfo'));
    }

    public function cart_details(){
        $cartinfo = Cart::instance('pos_shopping')->content()->sortBy('id');
        $discount = 0;
        foreach($cartinfo as $cart){
            $discount += $cart->options->product_discount*$cart->qty;
        }
        Session::put('product_discount',$discount);
        return view('backEnd.order.cart_details',compact('cartinfo'));
    }
    public function cart_increment(Request $request){
        $qty = $request->qty + 1;
        $cart = Cart::instance('pos_shopping')->content()->where('rowId', $request->id)->first();
        $cartinfo = Cart::instance('pos_shopping')->update($request->id, [
            'qty' => $qty,
            'options' => [
                'slug' => $cart->options->slug,
                'image' => $cart->options->image,
                'old_price' => $cart->options->old_price,
                'purchase_price' => $cart->options->purchase_price,
                'product_discount' => $cart->options->product_discount,
                'product_size' => $cart->options->product_size,
                'product_color' => $cart->options->product_color,
            ],
        ]);
        return response()->json($cartinfo);
    }
    public function cart_decrement(Request $request){
        $qty = $request->qty - 1;
        $cart = Cart::instance('pos_shopping')->content()->where('rowId', $request->id)->first();
        $cartinfo = Cart::instance('pos_shopping')->update($request->id, [
            'qty' => $qty,
            'options' => [
                'slug' => $cart->options->slug,
                'image' => $cart->options->image,
                'old_price' => $cart->options->old_price,
                'purchase_price' => $cart->options->purchase_price,
                'product_discount' => $cart->options->product_discount,
                'product_size' => $cart->options->product_size,
                'product_color' => $cart->options->product_color,
            ],
        ]);
        
        return response()->json($cartinfo);
    }
    public function cart_remove(Request $request){
        $remove = Cart::instance('pos_shopping')->remove($request->id);
        $cartinfo = Cart::instance('pos_shopping')->content();
        return response()->json($cartinfo);
    }
    public function product_discount(Request $request){
        $discount = $request->discount;
        $cart = Cart::instance('pos_shopping')->content()->where('rowId', $request->id)->first();
        $cartinfo = Cart::instance('pos_shopping')->update($request->id, [
            'options' => [
                'slug' => $cart->options->slug,
                'image' => $cart->options->image,
                'old_price' => $cart->options->old_price,
                'purchase_price' => $cart->options->purchase_price,
                'product_discount' => $request->discount,
                'product_size' => $cart->options->product_size,
                'product_color' => $cart->options->product_color,
            ],
        ]);
        return response()->json($cartinfo);
    }
    public function cart_update(Request $request)
    {
        // Get the row ID of the cart item
        $rowId = $request->id;
        // Fetch the current cart item using the row ID
        $cartItem = Cart::instance('pos_shopping')->content()->where('rowId', $request->id)->first();
        if ($cartItem) {
            // Update the options for the cart item
            Cart::instance('pos_shopping')->update($rowId, [
                'options' => [
                    'product_size' => $request->product_size ?: $cartItem->options->product_size, // Use new size or keep existing
                    'product_color' => $request->product_color ?: $cartItem->options->product_color, // Use new color or keep existing
                    'slug' => $cartItem->options->slug,
                    'image' => $cartItem->options->image,
                    'old_price' => $cartItem->options->old_price,
                    'purchase_price' => $cartItem->options->purchase_price,
                    'product_discount' => $cartItem->options->product_discount,
                ],
            ]);
        }
    
        return response()->json($cartItem);
    }
    public function cart_shipping(Request $request){
         $shipping = ShippingCharge::where(['status'=>1,'id'=>$request->id])->first()->amount;
        Session::put('pos_shipping', $shipping);
        return response()->json($shipping);
    }

    public function cart_clear(Request $request){
        $cartinfo = Cart::instance('pos_shopping')->destroy();
        Session::forget('pos_shipping');
        Session::forget('pos_discount');
        Session::forget('product_discount');
        return redirect()->back();
    }

    
    public function order_edit($invoice_id){
        $products = Product::select('id','name','new_price','product_code')->where(['status'=>1])->get();
        $shippingcharge = ShippingCharge::where('status',1)->get();
        $order = Order::where('invoice_id',$invoice_id)->first();
        $cartinfo  = Cart::instance('pos_shopping')->destroy();
        $shippinginfo  = Shipping::where('order_id',$order->id)->first();
        Session::put('product_discount',$order->discount);
        Session::put('pos_shipping',$order->shipping_charge);
        $orderdetails = OrderDetails::where('order_id',$order->id)->get();
        foreach($orderdetails as $ordetails){
        $cartinfo = Cart::instance('pos_shopping')->add([
            'id' => $ordetails->product_id,
            'name' => $ordetails->product_name,
            'qty' => $ordetails->qty,
            'price' => $ordetails->sale_price,
            'options' => [
                'image' => $ordetails->image->image,
                'purchase_price' => $ordetails->purchase_price,
                'product_discount' => $ordetails->product_discount,
                'details_id' => $ordetails->id,
                'product_size'=>$ordetails->product_size,
                'product_color'=>$ordetails->product_color,
            ],
        ]);
        }
        $cartinfo  = Cart::instance('pos_shopping')->content()->sortBy('id');
        return view('backEnd.order.edit',compact('products','cartinfo','shippingcharge','shippinginfo','order'));
    }
    
    public function order_update(Request $request){
        $this->validate($request,[
            'name'=>'required',
            'phone'=>'required',
            'address'=>'required',
            'area'=>'required',
        ]);

        if(Cart::instance('pos_shopping')->count() <= 0) {
            Toastr::error('Your shopping empty', 'Failed!');
            return redirect()->back();
        }

        $subtotal = Cart::instance('pos_shopping')->subtotal();
        $subtotal = str_replace(',','',$subtotal);
        $subtotal = str_replace('.00', '',$subtotal);
        $discount = Session::get('pos_discount')+Session::get('product_discount');
        $shippingfee  = ShippingCharge::find($request->area);

        $exits_customer = Customer::where('phone',$request->phone)->select('phone','id')->first();
        if($exits_customer){
            $customer_id = $exits_customer->id;
        }else{
            $password = rand(111111,999999);
            $store              = new Customer();
            $store->name        = $request->name;
            $store->slug        = $request->name;
            $store->phone       = $request->phone;
            $store->password    = bcrypt($password);
            $store->verify      = 1;
            $store->status      = 'active';
            $store->save();
            $customer_id = $store->id;
        }
 
         // order data save
        $order                   =  Order::where('id',$request->order_id)->first();
        $order->amount           = ($subtotal + $shippingfee->amount) - $discount;
        $order->discount         = $discount ? $discount : 0;
        $order->shipping_charge  = $shippingfee->amount;
        $order->customer_id      =  $customer_id;
        $order->order_status     = 1;
        $order->note             = $request->note;
        $order->save();


        // shipping data save
        $shipping              =   Shipping::where('order_id',$request->order_id)->first();
        $shipping->order_id    =   $order->id;
        $shipping->customer_id =   $customer_id;
        $shipping->name        =   $request->name;
        $shipping->phone       =   $request->phone;
        $shipping->address     =   $request->address;
        $shipping->area        =   $shippingfee->name;
        $shipping->save();

        // payment data save
        $payment                 =  Payment::where('order_id',$request->order_id)->first();
        $payment->order_id       = $order->id;
        $payment->customer_id    = $customer_id;
        $payment->payment_method = 'Cash On Delivery';
        $payment->amount         = $order->amount;
        $payment->payment_status = 'pending';
        $payment->save();

        // order details data save
        $cart_details_ids = [];
        foreach (Cart::instance('pos_shopping')->content() as $cart) {
            if ($cart->options->details_id) {
                $cart_details_ids[] = $cart->options->details_id;
            }
        }
        OrderDetails::where('order_id', $order->id)->whereNotIn('id', $cart_details_ids)->delete();

        foreach(Cart::instance('pos_shopping')->content() as $cart){
            $exits = OrderDetails::where('id',$cart->options->details_id)->first();
            if($exits){
                $order_details                   =   OrderDetails::find($exits->id);
                $order_details->product_discount =   $cart->options->product_discount;
                $order_details->product_color =   $cart->options->product_color;
                $order_details->product_size =   $cart->options->product_size;
                $order_details->sale_price       =   $cart->price;
                $order_details->qty              =   $cart->qty;
                $order_details->save();
            }else{
                $order_details                   =   new OrderDetails();
                $order_details->order_id         =   $order->id;
                $order_details->product_id       =   $cart->id;
                $order_details->product_name     =   $cart->name;
                $order_details->purchase_price   =   $cart->options->purchase_price;
                $order_details->product_discount =   $cart->options->product_discount;
                $order_details->product_color    =   $cart->options->product_color;
                $order_details->product_size     =   $cart->options->product_size;
                $order_details->sale_price       =   $cart->price;
                $order_details->qty              =   $cart->qty;
                $order_details->save();
            }
            
        }
        Cart::instance('pos_shopping')->destroy();
        Session::forget('pos_shipping');
        Session::forget('pos_discount');
        Session::forget('product_discount');
        Toastr::success('Thanks, Your order place successfully', 'Success!');
        return redirect('admin/order/pending');
    }

    // FraudBD check -- added 2026-04-15
    public function fraudCheck(Request $request)
    {
        $phone  = preg_replace('/\D/', '', $request->phone); // strip non-digits
        $apiKey = env('FRAUDBD_API_KEY');

        if (!$apiKey) {
            return response()->json(['error' => 'FraudBD API key not configured. Add FRAUDBD_API_KEY to .env'], 422);
        }

        if (!$phone) {
            return response()->json(['error' => 'Phone number is required.'], 422);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
            ])->post('https://fraudbd.com/api/check-courier-info', [
                'phone_number' => $phone,
            ]);

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'error' => $response->json('message') ?? 'FraudBD returned an error.',
            ], $response->status());

        } catch (\Exception $e) {
            \Log::error('FraudBD check failed: ' . $e->getMessage());
            return response()->json(['error' => 'Could not connect to FraudBD. Try again.'], 500);
        }
    }

}
