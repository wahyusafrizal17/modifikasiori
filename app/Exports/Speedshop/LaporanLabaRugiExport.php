<?php

namespace App\Exports\Speedshop;

use App\Models\Invoice;
use App\Models\ServiceOrder;
use App\Models\TransaksiPenjualan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LaporanLabaRugiExport implements FromArray, WithHeadings
{
    public function __construct(
        protected array $filters = []
    ) {}

    public function array(): array
    {
        $warehouseId = auth()->user()->activeWarehouseId();
        $dari = !empty($this->filters['dari']) ? Carbon::parse($this->filters['dari'])->startOfDay() : now()->startOfMonth();
        $sampai = !empty($this->filters['sampai']) ? Carbon::parse($this->filters['sampai'])->endOfDay() : now()->endOfDay();

        $pendapatanInvoice = Invoice::when($warehouseId, fn ($q) => $q->whereHas('serviceOrder', fn ($sq) => $sq->where('warehouse_id', $warehouseId)))
            ->whereBetween('tanggal', [$dari, $sampai])
            ->sum('grand_total');

        $pendapatanPenjualan = DB::table('transaksi_penjualan_items')
            ->join('transaksi_penjualan', 'transaksi_penjualan_items.transaksi_penjualan_id', '=', 'transaksi_penjualan.id')
            ->when($warehouseId, fn ($q) => $q->where('transaksi_penjualan.warehouse_id', $warehouseId))
            ->whereBetween('transaksi_penjualan.created_at', [$dari, $sampai])
            ->selectRaw('SUM(transaksi_penjualan_items.qty * transaksi_penjualan_items.harga_satuan) as total')
            ->value('total') ?? 0;

        $totalPendapatan = $pendapatanInvoice + $pendapatanPenjualan;

        $serviceOrders = ServiceOrder::with('products')
            ->when($warehouseId, fn ($q) => $q->where('warehouse_id', $warehouseId))
            ->whereBetween('tanggal_masuk', [$dari, $sampai])
            ->whereIn('status', ['proses', 'selesai'])
            ->get();

        $biayaSparepart = 0;
        foreach ($serviceOrders as $so) {
            foreach ($so->products as $p) {
                $hpp = $p->hpp ?? $p->harga_pembelian ?? 0;
                $biayaSparepart += $hpp * $p->pivot->qty;
            }
        }

        $transaksiPenjualan = TransaksiPenjualan::with('items.product')
            ->when($warehouseId, fn ($q) => $q->where('warehouse_id', $warehouseId))
            ->whereBetween('created_at', [$dari, $sampai])
            ->get();

        $biayaPenjualan = 0;
        foreach ($transaksiPenjualan as $tp) {
            foreach ($tp->items as $item) {
                $hpp = $item->product->hpp ?? $item->product->harga_pembelian ?? 0;
                $biayaPenjualan += $hpp * $item->qty;
            }
        }

        $totalBiaya = $biayaSparepart + $biayaPenjualan;
        $labaRugi = $totalPendapatan - $totalBiaya;

        return [
            ['PENDAPATAN', '', ''],
            ['Pendapatan dari Invoice (Service)', '', number_format($pendapatanInvoice, 0, ',', '.')],
            ['Pendapatan Penjualan Langsung', '', number_format($pendapatanPenjualan, 0, ',', '.')],
            ['Total Pendapatan', '', number_format($totalPendapatan, 0, ',', '.')],
            ['', '', ''],
            ['BIAYA', '', ''],
            ['Biaya HPP Sparepart (Service)', '', number_format($biayaSparepart, 0, ',', '.')],
            ['Biaya HPP Penjualan Langsung', '', number_format($biayaPenjualan, 0, ',', '.')],
            ['Total Biaya', '', number_format($totalBiaya, 0, ',', '.')],
            ['', '', ''],
            ['LABA / RUGI', '', number_format($labaRugi, 0, ',', '.')],
        ];
    }

    public function headings(): array
    {
        return ['Keterangan', '', 'Nilai (Rp)'];
    }
}
