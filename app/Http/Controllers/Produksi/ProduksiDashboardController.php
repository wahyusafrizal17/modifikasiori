<?php

namespace App\Http\Controllers\Produksi;

use App\Http\Controllers\Controller;
use App\Models\Production;

class ProduksiDashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        $produksiHariIni = Production::whereDate('created_at', $today)->count();
        $produksiProses = Production::where('status', 'proses')->count();
        $produksiSelesai = Production::where('status', 'selesai')->count();

        $recentProductions = Production::with(['teamProduksi', 'outputs.bahanSiapProduksi'])
            ->latest()
            ->limit(5)
            ->get();

        // Data chart: produksi per hari (7 hari terakhir)
        $chartProduksiLabels = [];
        $chartProduksiData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $chartProduksiLabels[] = $date->format('d/m');
            $chartProduksiData[] = Production::whereDate('created_at', $date->toDateString())->count();
        }

        $chartStatusLabels = ['Proses', 'Selesai'];
        $chartStatusData = [$produksiProses, $produksiSelesai];

        return view('produksi.dashboard', compact(
            'produksiHariIni',
            'produksiProses',
            'produksiSelesai',
            'recentProductions',
            'chartProduksiLabels',
            'chartProduksiData',
            'chartStatusLabels',
            'chartStatusData',
        ));
    }
}
