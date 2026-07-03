<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Productimage;
use App\Models\Productprice;
use App\Models\Productcolor;
use App\Models\Productsize;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Childcategory;
use App\Models\Brand;
use App\Models\Color;
use App\Models\Size;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function getSubcategory(Request $request)
    {
        $subcategory = DB::table("subcategories")
        ->where("category_id", $request->category_id)
        ->pluck('subcategoryName', 'id');
        return response()->json($subcategory);
    }
    public function getChildcategory(Request $request)
    {
        $childcategory = DB::table("childcategories")
        ->where("subcategory_id", $request->subcategory_id)
        ->pluck('childcategoryName', 'id');
        return response()->json($childcategory);
    }
    
    
    function __construct()
    {
         $this->middleware('permission:product-list|product-create|product-edit|product-delete', ['only' => ['index','show']]);
         $this->middleware('permission:product-create', ['only' => ['create','store']]);
         $this->middleware('permission:product-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:product-delete', ['only' => ['destroy']]);
    }
    
    
    public function index(Request $request)
    {
        // DataTables handles search/pagination client-side -- updated 2026-05-02
        $data = Product::orderBy('id','DESC')->with('image','category')->get();
        return view('backEnd.product.index',compact('data'));
    }
    public function create()
    {
        $categories = Category::where('parent_id','=','0')->where('status',1)->select('id','name','status')->with('childrenCategories')->get();
        $brands = Brand::where('status','1')->select('id','name','status')->get();
        $colors = Color::where('status','1')->get();
        $sizes = Size::where('status','1')->get();
        return view('backEnd.product.create',compact('categories','brands','colors','sizes'));
    }


public function store(Request $request)
{
    $this->validate($request, [
        'name' => 'required',
        'category_id' => 'required',
        'new_price' => 'required',
        'purchase_price' => 'required',
        'description' => 'required',
    ]);

    DB::beginTransaction();

    try {
        // ✅ AUTO PRODUCT CODE
        $last_id = Product::orderBy('id', 'desc')->select('id')->first();
        $last_id = $last_id ? $last_id->id + 1 : 1;

        // ✅ PRODUCT DATA
        $input = $request->except(['image','files','proSize','proColor','variantPrice','variantStock']);

        $input['slug'] = strtolower(preg_replace('/[\/\s]+/', '-', $request->name.'-'.$last_id));
        $input['pro_video'] = $this->getYouTubeVideoId($request->pro_video);
        $input['status'] = $request->status ? 1 : 0;
        $input['topsale'] = $request->topsale ? 1 : 0;
        $input['feature_product'] = $request->feature_product ? 1 : 0;
        $input['product_code'] = 'P' . str_pad($last_id, 4, '0', STR_PAD_LEFT);

        // ✅ SAVE PRODUCT
        $product = Product::create($input);

        // ✅ SAVE VARIANTS (SIZE + COLOR + PRICE + STOCK)
        // ✅ SAVE SIZE VARIANTS (INDEPENDENT)
        // ✅ SAVE SIZE VARIANTS (INDEPENDENT)
        if ($request->has('proSize') && is_array($request->proSize)) {
            foreach ($request->proSize as $index => $sizeId) {
                
                // Skip if sizeId is null, empty string, or 0
                if (empty($sizeId)) {
                    continue;
                }

                $price = $request->variantPrice[$index] ?? 0;
                $stock = $request->variantStock[$index] ?? 0;

                DB::table('productsizes')->insert([
                    'product_id' => $product->id,
                    'size_id'    => $sizeId,
                    'price'      => $price,
                    'stock'      => $stock,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }


        // ✅ SAVE COLOR VARIANTS (INDEPENDENT)
        // ✅ SAVE COLOR VARIANTS (INDEPENDENT)
        if ($request->has('proColor') && is_array($request->proColor)) {
            foreach ($request->proColor as $index => $colorId) {
                
                // Skip if colorId is null, empty string, or 0
                if (empty($colorId)) {
                    continue;
                }

                $price = $request->variantPrice[$index] ?? 0;
                $stock = $request->variantStock[$index] ?? 0;

                DB::table('productcolors')->insert([
                    'product_id' => $product->id,
                    'color_id'   => $colorId,
                    'price'      => $price,
                    'stock'      => $stock,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }


        // ✅ IMAGE UPLOAD
        $images = $request->file('image');
        if ($images) {
            foreach ($images as $image) {
                $name = time().'-'.$image->getClientOriginalName();
                $name = strtolower(preg_replace('/\s+/', '-', $name));
                $uploadPath = 'public/uploads/product/';
                $image->move($uploadPath,$name);
                $imageUrl = $uploadPath.$name;

                $pimage = new Productimage();
                $pimage->product_id = $product->id;
                $pimage->image      = $imageUrl;
                $pimage->save();
            }
        }

        DB::commit();

        Toastr::success('Success','Product Inserted Successfully');
        return redirect()->route('products.index');

    } catch (\Exception $e) {
        DB::rollback();
        Toastr::error('Error', $e->getMessage());
        return back();
    }
}

    
    public function edit($id)
    {
        $edit_data = Product::with('images')->find($id);
        $categories = Category::where('parent_id','=','0')->where('status',1)->select('id','name','status')->get();
        $categoryId = Product::find($id)->category_id;
        $subcategoryId = Product::find($id)->subcategory_id;
        $subcategory = Subcategory::where('category_id', '=', $categoryId)->select('id','subcategoryName','status')->get();
        $childcategory = Childcategory::where('subcategory_id', '=', $subcategoryId)->select('id', 'childcategoryName', 'status')->get();
        $brands = Brand::where('status','1')->select('id','name','status')->get();
        $totalsizes = Size::where('status',1)->get();
        $totalcolors = Color::where('status',1)->get();
        $variants = DB::table('productsizes')
        ->leftJoin('productcolors', function($join){
            $join->on('productsizes.product_id', '=', 'productcolors.product_id');
        })
        ->where('productsizes.product_id', $id)
        ->select(
            'productsizes.size_id',
            'productcolors.color_id',
            'productsizes.price',
            'productsizes.stock'
        )
        ->get();
    return view('backEnd.product.edit', compact(
        'edit_data',
        'categories',
        'subcategory',
        'childcategory',
        'brands',
        'totalsizes',
        'totalcolors',
        'variants'
    ));

    }
    public function price_edit()
    {
        $products = DB::table('products')->select('id','name','status','old_price','new_price','stock')->where('status',1)->get();;
        return view('backEnd.product.price_edit',compact('products'));
    }
    public function price_update(Request $request)
    {
        // Fetch all products in one query, then update individually -- fixed N+1 2026-04-15
        $ids      = $request->ids;
        $products = Product::select('id','old_price','new_price','stock')
                        ->whereIn('id', $ids)
                        ->get()
                        ->keyBy('id');

        foreach ($ids as $key => $id) {
            $product = $products->get($id);
            if ($product) {
                $product->update([
                    'old_price' => $request->old_price[$key],
                    'new_price' => $request->new_price[$key],
                    'stock'     => $request->stock[$key],
                ]);
            }
        }

        Toastr::success('Success','Price update successfully');
        return redirect()->back();
    }
    
    public function update(Request $request)
    {
       $this->validate($request, [
            'name' => 'required',
            'category_id' => 'required',
            'new_price' => 'required',
            'purchase_price' => 'required',
            'stock' => 'required',
            'category_id' => 'required',
            'description' => 'required',
        ]);
        
        DB::beginTransaction();

        try {

        $update_data = Product::findOrFail($request->id);

        // ✅ PRODUCT DATA
        $input = $request->except([
            'image','files',
            'proSize','proColor',
            'variantPrice','variantStock'
        ]);

        $input['slug'] = strtolower(preg_replace('/[\/\s]+/', '-', $request->name.'-'.$update_data->id));
        $input['status'] = $request->status ? 1 : 0;
        $input['topsale'] = $request->topsale ? 1 : 0;
        $input['feature_product'] = $request->feature_product ? 1 : 0;
        $input['pro_video'] = $this->getYouTubeVideoId($request->pro_video);

        $update_data->update($input);

        // ✅ DELETE OLD VARIANTS
// ✅ DELETE OLD VARIANTS (ALWAYS SAFE)
DB::table('productsizes')->where('product_id', $update_data->id)->delete();
DB::table('productcolors')->where('product_id', $update_data->id)->delete();


// ✅ SAVE UPDATED SIZE VARIANTS (INDEPENDENT)
if ($request->filled('proSize')) {
    foreach ($request->proSize as $index => $sizeId) {
        
        if (!$sizeId) {
            continue;
        }

        $price = $request->variantPrice[$index] ?? 0;
        $stock = $request->variantStock[$index] ?? 0;

        DB::table('productsizes')->insert([
            'product_id' => $update_data->id,
            'size_id'    => $sizeId,
            'price'      => $price,
            'stock'      => $stock,
            'created_at'=> now(),
            'updated_at'=> now(),
        ]);
    }
}


// ✅ SAVE UPDATED COLOR VARIANTS (INDEPENDENT)
if ($request->filled('proColor')) {
    foreach ($request->proColor as $index => $colorId) {

        if (!$colorId) {
            continue;
        }

        $price = $request->variantPrice[$index] ?? 0;
        $stock = $request->variantStock[$index] ?? 0;

        DB::table('productcolors')->insert([
            'product_id' => $update_data->id,
            'color_id'   => $colorId,
            'price'      => $price,
            'stock'      => $stock,
            'created_at'=> now(),
            'updated_at'=> now(),
        ]);
    }
}

        // ✅ IMAGE UPLOAD
        $images = $request->file('image');
        if ($images) {
            foreach ($images as $image) {
                $name = time().'-'.$image->getClientOriginalName();
                $name = strtolower(preg_replace('/\s+/', '-', $name));
                $uploadPath = 'public/uploads/product/';
                $image->move($uploadPath, $name);
                $imageUrl = $uploadPath.$name;

                $pimage = new Productimage();
                $pimage->product_id = $update_data->id;
                $pimage->image = $imageUrl;
                $pimage->save();
            }
        }

        DB::commit();

        Toastr::success('Success','Product updated with variants successfully');
        return redirect()->route('products.index');

    } catch (\Exception $e) {
        DB::rollback();
        Toastr::error('Error', $e->getMessage());
        return back();
    }

}
 
    public function inactive(Request $request)
    {
        $inactive = Product::find($request->hidden_id);
        $inactive->status = 0;
        $inactive->save();
        Toastr::success('Success','Data inactive successfully');
        return redirect()->back();
    }
    public function active(Request $request)
    {
        $active = Product::find($request->hidden_id);
        $active->status = 1;
        $active->save();
        Toastr::success('Success','Data active successfully');
        return redirect()->back();
    }
    public function destroy(Request $request)
    {
        $delete_data = Product::find($request->hidden_id);
        $delete_data->delete();
        Toastr::success('Success','Data delete successfully');
        return redirect()->back();
    }
    public function imgdestroy(Request $request)
    { 
        $delete_data = Productimage::find($request->id);
        File::delete($delete_data->image);
        $delete_data->delete();
        Toastr::success('Success','Data delete successfully');
        return redirect()->back();
    } 
    public function pricedestroy(Request $request)
    { 
        $delete_data = Productprice::find($request->id);
        $delete_data->delete();
        Toastr::success('Success','Product price delete successfully');
        return redirect()->back();
    }
    public function hotDeals(){
        $data = Product::with('image')->where('status', 1)->orderBy('topsale','desc')->get();
        return view('backEnd.product.hot_deals', compact('data'));
    }

    public function flashSales(){
        $data = Product::with('image')->where('status', 1)->orderBy('flashsale','desc')->get();
        return view('backEnd.product.flash_sales', compact('data'));
    }

    public function toggleHotDeal(Request $request){
        $product = Product::findOrFail($request->id);
        $product->topsale = $product->topsale ? 0 : 1;
        $product->save();
        return response()->json(['status'=>'success','topsale'=>$product->topsale]);
    }

    public function toggleFlashSale(Request $request){
        $product = Product::findOrFail($request->id);
        $product->flashsale = $product->flashsale ? 0 : 1;
        $product->save();
        return response()->json(['status'=>'success','flashsale'=>$product->flashsale]);
    }

    public function update_flash_sales(Request $request){
        Product::whereIn('id', $request->input('product_ids'))->update(['flashsale' => $request->status]);
        return response()->json(['status'=>'success']);
    }

    public function update_deals(Request $request){
        $products = Product::whereIn('id', $request->input('product_ids'))->update(['topsale' => $request->status]);
        return response()->json(['status'=>'success','message'=>'Hot deals product status change']);
    }
    public function update_feature(Request $request){
        $products = Product::whereIn('id', $request->input('product_ids'))->update(['feature_product' => $request->status]);
        return response()->json(['status'=>'success','message'=>'Feature product status change']);
    }
    public function update_status(Request $request){
        $products = Product::whereIn('id', $request->input('product_ids'))->update(['status' => $request->status]);
        return response()->json(['status'=>'success','message'=>'Product status change successfully']);
    }

    public function getYouTubeVideoId($input)
    {
        // Check if the input is a valid YouTube video ID (11 characters long)
        if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $input)) {
            return $input;
        }

        // Updated pattern to also match YouTube shorts URL
        $pattern = '/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:shorts\/|[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/';

        preg_match($pattern, $input, $matches);

        return isset($matches[1]) ? $matches[1] : null;
    }

}
