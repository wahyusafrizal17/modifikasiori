<?php

namespace App\Imports;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsImport implements ToModel, WithHeadingRow
{
    protected int $warehouseId;

    public function __construct()
    {
        $this->warehouseId = auth()->user()->warehouse_id;
    }

    public function model(array $row): ?Product
    {
        $categoryName = trim($row['kategori'] ?? '');
        $category = Category::forUser()
            ->where('nama', $categoryName)
            ->first();

        if (!$category) {
            return null;
        }

        $kodeProduk = trim($row['kode_produk'] ?? '');
        $namaProduk = trim($row['nama_produk'] ?? '');
        if (empty($kodeProduk) || empty($namaProduk)) {
            return null;
        }

        if (Product::where('kode_produk', $kodeProduk)->exists()) {
            return null;
        }

        $brandName = trim($row['brand'] ?? '');
        $brand = $brandName ? Brand::forUser()->where('nama', $brandName)->first() : null;

        return new Product([
            'kode_produk' => $kodeProduk,
            'nama_produk' => $namaProduk,
            'category_id' => $category->id,
            'brand_id' => $brand?->id,
            'jumlah' => (int) ($row['stok'] ?? 0),
            'harga_pembelian' => (float) ($row['harga_pembelian'] ?? $row['harga_pembelian_rp'] ?? 0),
            'harga_jual' => (float) ($row['harga_jual'] ?? $row['harga_jual_rp'] ?? 0),
            'warehouse_id' => $this->warehouseId,
        ]);
    }
}
