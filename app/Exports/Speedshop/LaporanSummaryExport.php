<?php

namespace App\Exports\Speedshop;

use App\Models\Invoice;
use App\Models\ServiceOrder;
use App\Models\TransaksiPenjualan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LaporanSummaryExport implements FromArray, WithHeadings
{
    public function __construct(protected array $filters = [])
    {
    }

    public function array(): array
    {
        $warehouseId = auth()->user()->activeWarehouseId();
        $dari = ! empty($this->filters['dari']) ? Carbon::parse($this->filters['dari'])->startOfDay() : now()->startOfMonth();
        $sampai = ! empty($this->filters['sampai']) ? Carbon::parse($this->filters['sampai'])->endOfDay() : now()->endOfDay();

        $totalServiceOrder = ServiceOrder::when($warehouseId, fn ($q) => $q->where('warehouse_id', $warehouseId))
            ->whereBetween('tanggal_masuk', [$dari, $sampai])
            ->count();

        $totalTransaksiPenjualan = TransaksiPenjualan::when($warehouseId, fn ($q) => $q->where('warehouse_id', $warehouseId))
            ->whereBetween('created_at', [$dari, $sampai])
            ->count();

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

        return [
            ['Ringkasan Periode '.$dari->format('d/m/Y').' - '.$sampai->format('d/m/Y')],
            [''],
            ['Total Service Order', $totalServiceOrder],
            ['Total Transaksi Penjualan', $totalTransaksiPenjualan],
            [''],
            ['Pendapatan dari Invoice (Service)', number_format($pendapatanInvoice, 0, ',', '.')],
            ['Pendapatan Penjualan Langsung', number_format($pendapatanPenjualan, 0, ',', '.')],
            ['Total Pendapatan', number_format($totalPendapatan, 0, ',', '.')],
        ];
    }

    public function headings(): array
    {
        return ['Keterangan', 'Nilai'];
    }
}
