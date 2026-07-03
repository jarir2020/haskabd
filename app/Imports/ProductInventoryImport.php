<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class ProductInventoryImport implements ToModel, WithHeadingRow, SkipsEmptyRows
{
    public function model(array $row)
    {
        // Match by product_code first, fallback to name
        $product = null;

        if (!empty($row['product_code'])) {
            $product = Product::where('product_code', trim($row['product_code']))->first();
        }

        if (!$product && !empty($row['name'])) {
            $product = Product::where('name', trim($row['name']))->first();
        }

        if (!$product) {
            return null; // Skip unmatched rows
        }

        $updates = [];

        if (isset($row['stock']) && is_numeric($row['stock'])) {
            $updates['stock'] = (int) $row['stock'];
        }

        if (isset($row['purchase_price']) && is_numeric($row['purchase_price'])) {
            $updates['purchase_price'] = (int) $row['purchase_price'];
        }

        if (!empty($updates)) {
            $product->update($updates);
        }

        return null; // We update instead of create
    }
}
