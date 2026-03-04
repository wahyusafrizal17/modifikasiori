<?php

namespace App\Http\Controllers\Speedshop;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Mekanik;
use App\Models\Product;
use App\Models\ServiceOrder;
use App\Models\TransaksiPenjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    private function warehouseId(): ?int
    {
        return auth()->user()->activeWarehouseId();
    }

    private function dateRange(Request $request): array
    {
        $dari = $request->get('dari') ? \Carbon\Carbon::parse($request->dari)->startOfDay() : now()->startOfMonth();
        $sampai = $request->get('sampai') ? \Carbon\Carbon::parse($request->sampai)->endOfDay() : now()->endOfDay();
        return [$dari, $sampai];
    }

    public function biaya(Request $request)
    {
        $warehouseId = $this->warehouseId();
        [$dari, $sampai] = $this->dateRange($request);

        $serviceOrders = ServiceOrder::with(['jasaServis', 'products', 'mekanik'])
            ->when($warehouseId, fn ($q) => $q->where('warehouse_id', $warehouseId))
            ->whereBetween('tanggal_masuk', [$dari, $sampai])
            ->whereIn('status', ['proses', 'selesai'])
            ->orderBy('tanggal_masuk')
            ->get();

        $totalBiayaJasa = 0;
        $totalBiayaSparepart = 0;
        foreach ($serviceOrders as $so) {
            $totalBiayaJasa += $so->jasaServis->sum('pivot.biaya');
            $totalBiayaSparepart += $so->products->sum(fn ($p) => $p->pivot->qty * ($p->hpp ?? $p->harga_pembelian ?? 0));
        }

        $transaksiPenjualan = TransaksiPenjualan::with('items.product')
            ->when($warehouseId, fn ($q) => $q->where('warehouse_id', $warehouseId))
            ->whereBetween('created_at', [$dari, $sampai])
            ->get();

        $totalBiayaPenjualan = 0;
        foreach ($transaksiPenjualan as $tp) {
            foreach ($tp->items as $item) {
                $hpp = $item->product->hpp ?? $item->product->harga_pembelian ?? 0;
                $totalBiayaPenjualan += $hpp * $item->qty;
            }
        }

        $totalBiaya = $totalBiayaJasa + $totalBiayaSparepart + $totalBiayaPenjualan;

        return view('speedshop.laporan.biaya', compact(
            'serviceOrders', 'transaksiPenjualan',
            'totalBiayaJasa', 'totalBiayaSparepart', 'totalBiayaPenjualan', 'totalBiaya',
            'dari', 'sampai'
        ));
    }

    public function labaRugi(Request $request)
    {
        $warehouseId = $this->warehouseId();
        [$dari, $sampai] = $this->dateRange($request);

        $invoices = Invoice::with('serviceOrder')
            ->when($warehouseId, fn ($q) => $q->whereHas('serviceOrder', fn ($sq) => $sq->where('warehouse_id', $warehouseId)))
            ->whereBetween('tanggal', [$dari, $sampai])
            ->get();

        $pendapatanJasa = $invoices->sum('total_jasa');
        $pendapatanSparepart = $invoices->sum('total_sparepart');
        $pendapatanTotal = $invoices->sum('grand_total');

        $transaksiPenjualan = TransaksiPenjualan::with('items.product')
            ->when($warehouseId, fn ($q) => $q->where('warehouse_id', $warehouseId))
            ->whereBetween('created_at', [$dari, $sampai])
            ->get();

        $pendapatanPenjualanLangsung = 0;
        $biayaPenjualanLangsung = 0;
        foreach ($transaksiPenjualan as $tp) {
            foreach ($tp->items as $item) {
                $pendapatanPenjualanLangsung += $item->harga_satuan * $item->qty;
                $hpp = $item->product->hpp ?? $item->product->harga_pembelian ?? 0;
                $biayaPenjualanLangsung += $hpp * $item->qty;
            }
        }

        $serviceOrders = ServiceOrder::with(['products', 'jasaServis'])
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

        $totalPendapatan = $pendapatanTotal + $pendapatanPenjualanLangsung;
        $totalBiaya = $biayaSparepart + $biayaPenjualanLangsung;
        $labaRugi = $totalPendapatan - $totalBiaya;

        return view('speedshop.laporan.laba-rugi', compact(
            'invoices', 'transaksiPenjualan', 'pendapatanJasa', 'pendapatanSparepart',
            'pendapatanPenjualanLangsung', 'totalPendapatan', 'totalBiaya', 'labaRugi',
            'biayaSparepart', 'biayaPenjualanLangsung',
            'dari', 'sampai'
        ));
    }

    public function summary(Request $request)
    {
        $warehouseId = $this->warehouseId();
        [$dari, $sampai] = $this->dateRange($request);

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

        return view('speedshop.laporan.summary', compact(
            'totalServiceOrder', 'totalTransaksiPenjualan',
            'pendapatanInvoice', 'pendapatanPenjualan', 'totalPendapatan',
            'dari', 'sampai'
        ));
    }

    public function penjualanPartOli(Request $request)
    {
        $warehouseId = $this->warehouseId();
        [$dari, $sampai] = $this->dateRange($request);

        $items = DB::table('transaksi_penjualan_items')
            ->join('transaksi_penjualan', 'transaksi_penjualan_items.transaksi_penjualan_id', '=', 'transaksi_penjualan.id')
            ->join('products', 'transaksi_penjualan_items.product_id', '=', 'products.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->when($warehouseId, fn ($q) => $q->where('transaksi_penjualan.warehouse_id', $warehouseId))
            ->whereBetween('transaksi_penjualan.created_at', [$dari, $sampai])
            ->selectRaw('products.id, products.kode_produk, products.nama_produk, categories.nama as kategori,
                SUM(transaksi_penjualan_items.qty) as total_qty,
                SUM(transaksi_penjualan_items.qty * transaksi_penjualan_items.harga_satuan) as total_nilai')
            ->groupBy('products.id', 'products.kode_produk', 'products.nama_produk', 'categories.nama')
            ->orderByDesc('total_qty')
            ->get();

        $serviceOrderItems = DB::table('service_order_products')
            ->join('service_orders', 'service_order_products.service_order_id', '=', 'service_orders.id')
            ->join('products', 'service_order_products.product_id', '=', 'products.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->when($warehouseId, fn ($q) => $q->where('service_orders.warehouse_id', $warehouseId))
            ->whereBetween('service_orders.tanggal_masuk', [$dari, $sampai])
            ->whereIn('service_orders.status', ['proses', 'selesai'])
            ->selectRaw('products.id, products.kode_produk, products.nama_produk, categories.nama as kategori,
                SUM(service_order_products.qty) as total_qty,
                SUM(service_order_products.qty * service_order_products.harga) as total_nilai')
            ->groupBy('products.id', 'products.kode_produk', 'products.nama_produk', 'categories.nama')
            ->get();

        $byProduct = [];
        foreach ($items as $i) {
            $key = $i->id;
            if (!isset($byProduct[$key])) {
                $byProduct[$key] = (object) ['kode_produk' => $i->kode_produk, 'nama_produk' => $i->nama_produk, 'kategori' => $i->kategori ?? '-', 'total_qty' => 0, 'total_nilai' => 0];
            }
            $byProduct[$key]->total_qty += $i->total_qty;
            $byProduct[$key]->total_nilai += $i->total_nilai;
        }
        foreach ($serviceOrderItems as $i) {
            $key = $i->id;
            if (!isset($byProduct[$key])) {
                $byProduct[$key] = (object) ['kode_produk' => $i->kode_produk, 'nama_produk' => $i->nama_produk, 'kategori' => $i->kategori ?? '-', 'total_qty' => 0, 'total_nilai' => 0];
            }
            $byProduct[$key]->total_qty += $i->total_qty;
            $byProduct[$key]->total_nilai += $i->total_nilai;
        }
        $merged = collect($byProduct)->sortByDesc('total_qty')->values();

        return view('speedshop.laporan.penjualan-part-oli', compact('merged', 'dari', 'sampai'));
    }

    public function mekanikPerformance(Request $request)
    {
        $warehouseId = $this->warehouseId();
        [$dari, $sampai] = $this->dateRange($request);

        $performances = ServiceOrder::with(['mekanik', 'jasaServis', 'products'])
            ->when($warehouseId, fn ($q) => $q->where('warehouse_id', $warehouseId))
            ->whereBetween('tanggal_masuk', [$dari, $sampai])
            ->whereIn('status', ['proses', 'selesai'])
            ->whereNotNull('mekanik_id')
            ->get()
            ->groupBy('mekanik_id')
            ->map(function ($orders, $mekanikId) {
                $mekanik = $orders->first()->mekanik;
                $totalJasa = 0;
                $totalSparepart = 0;
                foreach ($orders as $so) {
                    $totalJasa += $so->jasaServis->sum('pivot.biaya');
                    $totalSparepart += $so->products->sum(fn ($p) => $p->pivot->qty * $p->pivot->harga);
                }
                return (object) [
                    'mekanik' => $mekanik,
                    'total_order' => $orders->count(),
                    'total_jasa' => $totalJasa,
                    'total_sparepart' => $totalSparepart,
                    'total' => $totalJasa + $totalSparepart,
                ];
            })
            ->sortByDesc('total_order')
            ->values();

        return view('speedshop.laporan.mekanik-performance', compact('performances', 'dari', 'sampai'));
    }

    public function exportBiaya(Request $request)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\Speedshop\LaporanBiayaExport($request->only(['dari', 'sampai'])),
            'laporan-biaya-' . now()->format('Y-m-d-His') . '.xlsx'
        );
    }

    public function exportLabaRugi(Request $request)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\Speedshop\LaporanLabaRugiExport($request->only(['dari', 'sampai'])),
            'laporan-laba-rugi-' . now()->format('Y-m-d-His') . '.xlsx'
        );
    }

    public function exportSummary(Request $request)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\Speedshop\LaporanSummaryExport($request->only(['dari', 'sampai'])),
            'laporan-summary-' . now()->format('Y-m-d-His') . '.xlsx'
        );
    }

    public function exportPenjualanPartOli(Request $request)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\Speedshop\LaporanPenjualanPartOliExport($request->only(['dari', 'sampai'])),
            'laporan-penjualan-part-oli-' . now()->format('Y-m-d-His') . '.xlsx'
        );
    }

    public function exportMekanikPerformance(Request $request)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\Speedshop\LaporanMekanikPerformanceExport($request->only(['dari', 'sampai'])),
            'laporan-mekanik-performance-' . now()->format('Y-m-d-His') . '.xlsx'
        );
    }
}
