<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Warehouse;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalProduk = Product::count();
        $totalUsers = User::count();
        $totalWarehouses = Warehouse::count();
        $totalSuppliers = Supplier::count();
        $totalCategories = Category::count();
        $totalBrands = Brand::count();

        // Data chart: distribusi master data (doughnut)
        $chartLabels = ['Produk', 'Users', 'Warehouse', 'Supplier', 'Kategori', 'Brand'];
        $chartData = [
            $totalProduk,
            $totalUsers,
            $totalWarehouses,
            $totalSuppliers,
            $totalCategories,
            $totalBrands,
        ];

        return view('admin.dashboard', compact(
            'totalProduk',
            'totalUsers',
            'totalWarehouses',
            'totalSuppliers',
            'totalCategories',
            'totalBrands',
            'chartLabels',
            'chartData',
        ));
    }
}
