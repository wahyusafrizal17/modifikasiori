<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockMovement;

class WarehouseDashboardController extends Controller
{
    public function index()
    {
        $totalProduk = Product::count();
        $stockMasukHariIni = StockMovement::where('type', 'masuk')->whereDate('created_at', today())->count();
        $totalTransaksiHariIni = 0;

        // Data chart: stock movement per hari (7 hari terakhir)
        $chartLabels = [];
        $chartMasuk = [];
        $chartKeluar = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $chartLabels[] = $date->format('d/m');
            $chartMasuk[] = StockMovement::where('type', 'masuk')->whereDate('created_at', $date->toDateString())->count();
            $chartKeluar[] = StockMovement::where('type', 'keluar')->whereDate('created_at', $date->toDateString())->count();
        }

        return view('warehouse.dashboard', compact(
            'totalProduk',
            'stockMasukHariIni',
            'totalTransaksiHariIni',
            'chartLabels',
            'chartMasuk',
            'chartKeluar',
        ));
    }
}
