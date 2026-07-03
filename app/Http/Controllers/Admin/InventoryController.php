<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\OrderDetails;
use App\Models\InventoryNote;
use App\Imports\ProductInventoryImport;
use App\Exports\ProductInventoryExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Toastr;

class InventoryController extends Controller
{
    public function products(Request $request)
    {
        $query = Product::with('category')->select('id','name','product_code','stock','purchase_price','new_price','status','category_id');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('name', 'like', "%$s%")
                  ->orWhere('product_code', 'like', "%$s%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $sort = $request->get('sort', 'latest');
        match($sort) {
            'name'           => $query->orderBy('name'),
            'stock_asc'      => $query->orderBy('stock'),
            'stock_desc'     => $query->orderByDesc('stock'),
            'purchase_asc'   => $query->orderBy('purchase_price'),
            'purchase_desc'  => $query->orderByDesc('purchase_price'),
            default          => $query->latest(),
        };

        $products   = $query->paginate(50)->withQueryString();
        $categories = Category::select('id','name')->orderBy('name')->get();

        return view('backEnd.inventory.products', compact('products', 'categories'));
    }

    public function profit(Request $request)
    {
        $query = OrderDetails::query()
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->where('orders.order_status', 6)
            ->select(
                'order_details.product_id',
                'order_details.product_name',
                \DB::raw('SUM(order_details.qty) as total_sold'),
                \DB::raw('SUM(order_details.sale_price * order_details.qty) as total_revenue'),
                \DB::raw('SUM(order_details.purchase_price * order_details.qty) as total_cost'),
                \DB::raw('SUM((order_details.sale_price - order_details.purchase_price) * order_details.qty) as profit')
            )
            ->groupBy('order_details.product_id', 'order_details.product_name');

        if ($request->filled('search')) {
            $query->where('order_details.product_name', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('order_details.updated_at', [$request->start_date, $request->end_date]);
        }

        // Grand totals before pagination
        $totals = (clone $query)->get();
        $grand_revenue = $totals->sum('total_revenue');
        $grand_cost    = $totals->sum('total_cost');
        $grand_profit  = $totals->sum('profit');

        $rows = $query->paginate(50)->withQueryString();

        // Attach current stock from products table
        $productIds   = $rows->pluck('product_id')->filter()->unique();
        $stockMap     = Product::whereIn('id', $productIds)->pluck('stock', 'id');

        return view('backEnd.inventory.profit', compact('rows', 'stockMap', 'grand_revenue', 'grand_cost', 'grand_profit'));
    }

    public function tools()
    {
        $note        = InventoryNote::first();
        $noteContent = $note ? $note->content : '';
        return view('backEnd.inventory.tools', compact('noteContent'));
    }

    public function saveNote(Request $request)
    {
        $note = InventoryNote::firstOrNew(['id' => 1]);
        $note->content = $request->input('content', '');
        $note->save();
        return response()->json(['status' => 'success']);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id'             => 'required|exists:products,id',
            'stock'          => 'required|integer|min:0',
            'purchase_price' => 'required|integer|min:0',
        ]);

        Product::findOrFail($request->id)->update([
            'stock'          => $request->stock,
            'purchase_price' => $request->purchase_price,
        ]);

        return response()->json(['status' => 'success']);
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:xlsx,xls,csv|max:5120']);

        try {
            Excel::import(new ProductInventoryImport, $request->file('file'));
            Toastr::success('Inventory imported successfully.', 'Success');
        } catch (\Exception $e) {
            Toastr::error('Import failed: '.$e->getMessage(), 'Error');
        }

        return redirect()->route('admin.inventory.products');
    }

    public function export()
    {
        return Excel::download(new ProductInventoryExport, 'inventory-'.date('Y-m-d').'.xlsx');
    }

    public function sample()
    {
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="inventory-sample.csv"',
        ];

        $rows = [
            ['name', 'product_code', 'stock', 'purchase_price'],
            ['Sample Product 1', 'PROD001', 50, 100],
            ['Sample Product 2', 'PROD002', 25, 200],
        ];

        $callback = function() use ($rows) {
            $handle = fopen('php://output', 'w');
            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function bulkDelete(Request $request)
    {
        $request->validate(['ids' => 'required|array']);
        Product::whereIn('id', $request->ids)->delete();
        return response()->json(['status' => 'success', 'message' => count($request->ids).' products deleted.']);
    }

    public function bulkExport(Request $request)
    {
        $ids = $request->query('ids') ? explode(',', $request->query('ids')) : null;
        return Excel::download(new ProductInventoryExport($ids), 'inventory-selected-'.date('Y-m-d').'.xlsx');
    }
}
