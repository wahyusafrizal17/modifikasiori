<?php

namespace App\Http\Controllers\Speedshop;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\ServiceOrder;

class SpeedshopDashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        $workOrderHariIni = ServiceOrder::whereDate('tanggal_masuk', $today)->count();
        $pendapatanHariIni = Invoice::whereDate('tanggal', $today)->sum('grand_total');
        $servisDalamProses = ServiceOrder::where('status', 'proses')->count();
        $servisAntri = ServiceOrder::where('status', 'antri')->count();
        $servisSelesai = ServiceOrder::where('status', 'selesai')->count();

        // Data chart: work order per hari (7 hari terakhir)
        $chartWOLabels = [];
        $chartWOData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $chartWOLabels[] = $date->format('d/m');
            $chartWOData[] = ServiceOrder::whereDate('tanggal_masuk', $date->toDateString())->count();
        }

        // Data chart: status servis (doughnut)
        $chartStatusLabels = ['Antri', 'Proses', 'Selesai'];
        $chartStatusData = [$servisAntri, $servisDalamProses, $servisSelesai];

        return view('speedshop.dashboard', compact(
            'workOrderHariIni',
            'pendapatanHariIni',
            'servisDalamProses',
            'servisAntri',
            'chartWOLabels',
            'chartWOData',
            'chartStatusLabels',
            'chartStatusData',
        ));
    }
}
