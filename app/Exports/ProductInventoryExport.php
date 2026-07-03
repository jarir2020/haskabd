<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductInventoryExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $ids;

    public function __construct(array $ids = null)
    {
        $this->ids = $ids;
    }

    public function collection()
    {
        $query = Product::with('category')->select('id','name','product_code','stock','purchase_price','new_price','status','category_id');

        if ($this->ids) {
            $query->whereIn('id', $this->ids);
        }

        return $query->orderBy('name')->get();
    }

    public function headings(): array
    {
        return ['ID', 'Name', 'Product Code', 'Category', 'Stock', 'Purchase Price', 'Sale Price', 'Status'];
    }

    public function map($product): array
    {
        return [
            $product->id,
            $product->name,
            $product->product_code,
            $product->category ? $product->category->name : '',
            $product->stock,
            $product->purchase_price,
            $product->new_price,
            $product->status ? 'Active' : 'Inactive',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
