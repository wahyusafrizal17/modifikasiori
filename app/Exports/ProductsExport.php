<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductsExport implements FromQuery, WithHeadings, WithMapping
{
    public function query()
    {
        return Product::with(['category', 'brand'])->forUser()->orderBy('kode_produk');
    }

    public function headings(): array
    {
        return [
            'Kode Produk',
            'Nama Produk',
            'Kategori',
            'Brand',
            'Stok',
            'Harga Pembelian',
            'Harga Jual',
        ];
    }

    public function map($product): array
    {
        return [
            $product->kode_produk,
            $product->nama_produk,
            $product->category->nama ?? '-',
            $product->brand->nama ?? '-',
            $product->jumlah,
            $product->harga_pembelian,
            $product->harga_jual,
        ];
    }
}
