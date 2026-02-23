<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Product;
use App\Models\ServiceOrder;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        $servisHariIni = ServiceOrder::whereDate('tanggal_masuk', $today)->count();
        $pendapatanHariIni = Invoice::whereDate('tanggal', $today)->sum('grand_total');
        $servisDalamProses = ServiceOrder::where('status', 'proses')->count();
        $servisAntri = ServiceOrder::where('status', 'antri')->count();

        $stockProducts = Product::forUser()
            ->where('jumlah', '>', 0)
            ->orderByDesc('jumlah')
            ->limit(15)
            ->get();

        $lowStockProducts = Product::forUser()
            ->with('category')
            ->where('jumlah', '<=', 5)
            ->orderBy('jumlah')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'servisHariIni', 'pendapatanHariIni', 'servisDalamProses', 'servisAntri',
            'stockProducts', 'lowStockProducts'
        ));
    }
}
