<?php

namespace App\Exports\Speedshop;

use App\Models\ServiceOrder;
use App\Models\TransaksiPenjualan;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LaporanBiayaExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(
        protected array $filters = []
    ) {}

    public function collection()
    {
        $warehouseId = auth()->user()->activeWarehouseId();
        $dari = !empty($this->filters['dari']) ? Carbon::parse($this->filters['dari'])->startOfDay() : now()->startOfMonth();
        $sampai = !empty($this->filters['sampai']) ? Carbon::parse($this->filters['sampai'])->endOfDay() : now()->endOfDay();

        $rows = collect();

        ServiceOrder::with(['jasaServis', 'products', 'mekanik'])
            ->when($warehouseId, fn ($q) => $q->where('warehouse_id', $warehouseId))
            ->whereBetween('tanggal_masuk', [$dari, $sampai])
            ->whereIn('status', ['proses', 'selesai'])
            ->orderBy('tanggal_masuk')
            ->get()
            ->each(function ($so) use (&$rows) {
                $biayaJasa = $so->jasaServis->sum('pivot.biaya');
                $biayaSparepart = $so->products->sum(fn ($p) => $p->pivot->qty * ($p->hpp ?? $p->harga_pembelian ?? 0));
                $rows->push((object) [
                    'tanggal' => $so->tanggal_masuk->format('d/m/Y'),
                    'kode' => $so->kode_servis,
                    'tipe' => 'Service Order',
                    'biaya_jasa' => $biayaJasa,
                    'biaya_sparepart' => $biayaSparepart,
                    'total' => $biayaJasa + $biayaSparepart,
                ]);
            });

        TransaksiPenjualan::with('items.product')
            ->when($warehouseId, fn ($q) => $q->where('warehouse_id', $warehouseId))
            ->whereBetween('created_at', [$dari, $sampai])
            ->get()
            ->each(function ($tp) use (&$rows) {
                $biaya = 0;
                foreach ($tp->items as $item) {
                    $hpp = $item->product->hpp ?? $item->product->harga_pembelian ?? 0;
                    $biaya += $hpp * $item->qty;
                }
                $rows->push((object) [
                    'tanggal' => $tp->created_at->format('d/m/Y'),
                    'kode' => $tp->no_transaksi,
                    'tipe' => 'Penjualan Langsung',
                    'biaya_jasa' => 0,
                    'biaya_sparepart' => $biaya,
                    'total' => $biaya,
                ]);
            });

        return $rows->sortBy('tanggal')->values();
    }

    public function headings(): array
    {
        return ['Tanggal', 'Kode', 'Tipe', 'Biaya Jasa', 'Biaya Sparepart', 'Total Biaya'];
    }

    public function map($row): array
    {
        return [
            $row->tanggal,
            $row->kode,
            $row->tipe,
            $row->biaya_jasa,
            $row->biaya_sparepart,
            $row->total,
        ];
    }
}
