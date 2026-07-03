<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Toastr;

class CustomerReviewController extends Controller
{
    public function index()
    {
        $reviews = Banner::where('category_id', 8)->orderBy('id')->get();
        return view('backEnd.customerReview.index', compact('reviews'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'images'   => 'required|array',
            'images.*' => 'required|file|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $uploadPath = 'public/uploads/banner/';

        foreach ($request->file('images') as $file) {
            $name = time() . '_' . $file->getClientOriginalName();
            $file->move($uploadPath, $name);
            Banner::create([
                'category_id' => 8,
                'link'        => '',
                'image'       => $uploadPath . $name,
                'status'      => 1,
            ]);
        }

        Toastr::success(count($request->file('images')) . ' review image(s) uploaded.', 'Success');
        return redirect()->route('admin.customer_reviews.index');
    }

    public function toggleStatus($id)
    {
        $banner = Banner::where('category_id', 8)->findOrFail($id);
        $banner->status = $banner->status ? 0 : 1;
        $banner->save();
        return response()->json(['status' => 'success', 'active' => $banner->status]);
    }

    public function destroy($id)
    {
        $banner = Banner::where('category_id', 8)->findOrFail($id);

        if ($banner->image && file_exists(base_path($banner->image))) {
            unlink(base_path($banner->image));
        }

        $banner->delete();
        return response()->json(['status' => 'success']);
    }
}
