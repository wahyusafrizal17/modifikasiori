<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\ServiceOrder;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->toDateString());
        $metode = $request->input('metode', '');

        $query = Invoice::with([
            'serviceOrder.pelanggan',
            'serviceOrder.kendaraan',
            'serviceOrder.mekanik',
            'serviceOrder.jasaServis',
            'serviceOrder.products',
        ])
            ->whereBetween('tanggal', [$from, $to]);

        if ($metode) {
            $query->where('metode_pembayaran', $metode);
        }

        $invoices = $query->latest('tanggal')->get();

        $unitEntry = ServiceOrder::whereBetween('tanggal_masuk', [$from, $to])->count();

        $totalParts = 0;
        $countParts = 0;
        $totalJasa = 0;
        $countJasa = 0;
        $totalOmset = 0;

        foreach ($invoices as $inv) {
            $totalParts += $inv->total_sparepart;
            $totalJasa += $inv->total_jasa;
            $totalOmset += $inv->grand_total;

            if ($inv->serviceOrder) {
                $countParts += $inv->serviceOrder->products->count();
                $countJasa += $inv->serviceOrder->jasaServis->count();
            }
        }

        return view('admin.laporan.index', compact(
            'invoices', 'from', 'to', 'metode',
            'unitEntry', 'totalParts', 'countParts',
            'totalJasa', 'countJasa', 'totalOmset'
        ));
    }

    public function print(Request $request)
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->toDateString());
        $metode = $request->input('metode', '');

        $query = Invoice::with([
            'serviceOrder.pelanggan',
            'serviceOrder.kendaraan',
            'serviceOrder.mekanik',
        ])
            ->whereBetween('tanggal', [$from, $to]);

        if ($metode) {
            $query->where('metode_pembayaran', $metode);
        }

        $invoices = $query->latest('tanggal')->get();
        $totalOmset = $invoices->sum('grand_total');

        return view('admin.laporan.print', compact('invoices', 'from', 'to', 'totalOmset'));
    }

    public function exportCsv(Request $request)
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->toDateString());
        $metode = $request->input('metode', '');

        $query = Invoice::with([
            'serviceOrder.pelanggan',
            'serviceOrder.kendaraan',
            'serviceOrder.mekanik',
        ])
            ->whereBetween('tanggal', [$from, $to]);

        if ($metode) {
            $query->where('metode_pembayaran', $metode);
        }

        $invoices = $query->latest('tanggal')->get();

        $filename = "laporan_{$from}_{$to}.csv";
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($invoices) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, [
                'No', 'Kode Servis', 'No. Invoice', 'Tgl. Invoice', 'Nama Pelanggan',
                'Nama Mekanik', 'No. HP', 'No. Polisi', 'Merk', 'Tipe',
                'Tahun', 'Metode Bayar', 'Total Jasa', 'Total Sparepart',
                'Diskon', 'Grand Total',
            ]);

            foreach ($invoices as $i => $inv) {
                $order = $inv->serviceOrder;
                fputcsv($file, [
                    $i + 1,
                    $order?->kode_servis,
                    $inv->kode_invoice,
                    $inv->tanggal->format('d/m/Y'),
                    $order?->pelanggan?->nama,
                    $order?->mekanik?->nama,
                    $order?->pelanggan?->no_hp,
                    $order?->kendaraan?->nomor_polisi,
                    $order?->kendaraan?->merk,
                    $order?->kendaraan?->tipe,
                    $order?->kendaraan?->tahun,
                    ucfirst($inv->metode_pembayaran),
                    $inv->total_jasa,
                    $inv->total_sparepart,
                    $inv->diskon,
                    $inv->grand_total,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
