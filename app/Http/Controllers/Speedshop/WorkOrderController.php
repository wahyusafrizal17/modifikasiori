<?php

namespace App\Http\Controllers\Speedshop;

use App\Http\Controllers\Controller;
use App\Models\JasaServis;
use App\Models\Kota;
use App\Models\Mekanik;
use App\Models\Pelanggan;
use App\Models\Product;
use App\Models\ServiceOrder;
use Illuminate\Http\Request;

class WorkOrderController extends Controller
{
    private function warehouseId(): ?int
    {
        return auth()->user()->activeWarehouseId();
    }

    public function index(Request $request)
    {
        $query = ServiceOrder::with(['pelanggan', 'kendaraan', 'mekanik'])
            ->when($warehouseId = $this->warehouseId(), fn ($q) => $q->where(function ($q2) use ($warehouseId) {
                $q2->where('warehouse_id', $warehouseId)->orWhereNull('warehouse_id');
            }));

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('kode_servis', 'like', "%{$s}%")
                    ->orWhereHas('pelanggan', fn ($q) => $q->where('nama', 'like', "%{$s}%"))
                    ->orWhereHas('kendaraan', fn ($q) => $q->where('nomor_polisi', 'like', "%{$s}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('kategori_service')) {
            $query->where('kategori_service', $request->kategori_service);
        }

        $orders = $query->latest('tanggal_masuk')->paginate(20)->withQueryString();
        $mekaniks = Mekanik::aktif()->orderBy('nama')->get();

        return view('speedshop.work-order.index', compact('orders', 'mekaniks'));
    }

    public function history(Request $request)
    {
        $query = ServiceOrder::with(['pelanggan', 'kendaraan', 'mekanik', 'jasaServis', 'products'])
            ->where('status', 'selesai')
            ->when($warehouseId = $this->warehouseId(), fn ($q) => $q->where(function ($q2) use ($warehouseId) {
                $q2->where('warehouse_id', $warehouseId)->orWhereNull('warehouse_id');
            }));

        if ($request->filled('dari_tanggal')) {
            $query->whereDate('tanggal_selesai', '>=', $request->dari_tanggal);
        }
        if ($request->filled('sampai_tanggal')) {
            $query->whereDate('tanggal_selesai', '<=', $request->sampai_tanggal);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('kode_servis', 'like', "%{$s}%")
                    ->orWhereHas('pelanggan', fn ($q) => $q->where('nama', 'like', "%{$s}%"))
                    ->orWhereHas('kendaraan', fn ($q) => $q->where('nomor_polisi', 'like', "%{$s}%"));
            });
        }

        $orders = $query->latest('tanggal_selesai')->paginate(20)->withQueryString();

        return view('speedshop.history.index', compact('orders'));
    }

    public function serviceRecord(Request $request)
    {
        $query = ServiceOrder::with(['pelanggan', 'jasaServis', 'products'])
            ->where('status', 'selesai')
            ->whereNotNull('pelanggan_id')
            ->when($warehouseId = $this->warehouseId(), fn ($q) => $q->where(function ($q2) use ($warehouseId) {
                $q2->where('warehouse_id', $warehouseId)->orWhereNull('warehouse_id');
            }));

        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('pelanggan', fn ($q) => $q->where('nama', 'like', "%{$s}%")
                ->orWhere('no_hp', 'like', "%{$s}%"));
        }

        $orders = $query->get();
        $byPelanggan = $orders->groupBy('pelanggan_id')->map(function ($group) {
            $pelanggan = $group->first()->pelanggan;
            return (object) [
                'pelanggan' => $pelanggan,
                'total_kunjungan' => $group->count(),
                'total_pengeluaran' => $group->sum(fn ($o) => $o->grand_total),
            ];
        })->values();

        $sort = $request->get('sort', 'kunjungan');
        $byPelanggan = $sort === 'pengeluaran'
            ? $byPelanggan->sortByDesc('total_pengeluaran')->values()
            : $byPelanggan->sortByDesc('total_kunjungan')->values();

        return view('speedshop.service-record.index', compact('byPelanggan', 'sort'));
    }

    public function estimasi()
    {
        $pelanggans = Pelanggan::with(['kendaraans', 'kota'])->orderBy('nama')->get();
        $jasaServis = JasaServis::orderBy('nama')->get();
        $warehouseId = $this->warehouseId();
        $products = $warehouseId !== null
            ? Product::where('warehouse_id', $warehouseId)->orderBy('nama_produk')->get()
            : Product::orderBy('nama_produk')->get();
        $initialPelanggans = $pelanggans->map(fn ($p) => [
            'id' => $p->id,
            'nama' => $p->nama,
            'no_hp' => $p->no_hp,
            'alamat' => $p->alamat,
            'kota' => $p->kota?->nama,
            'kendaraans' => $p->kendaraans->map(fn ($k) => [
                'id' => $k->id,
                'nomor_polisi' => $k->nomor_polisi,
                'merk' => $k->merk,
                'tipe' => $k->tipe ?? '',
                'tahun' => $k->tahun,
            ])->values()->all(),
        ])->values()->all();

        return view('speedshop.estimasi.create', compact('pelanggans', 'jasaServis', 'products', 'initialPelanggans'));
    }

    public function create()
    {
        $pelanggans = Pelanggan::with('kendaraans')->orderBy('nama')->get();
        $mekaniks = Mekanik::aktif()->orderBy('nama')->get();
        $jasaServis = JasaServis::orderBy('nama')->get();

        $warehouseId = $this->warehouseId();
        $products = $warehouseId !== null
            ? Product::where('warehouse_id', $warehouseId)->orderBy('nama_produk')->get()
            : Product::orderBy('nama_produk')->get();

        $kotas = Kota::orderBy('nama')->get();
        $initialPelanggans = $pelanggans->map(fn ($p) => [
            'id' => $p->id,
            'nama' => $p->nama,
            'no_hp' => $p->no_hp,
            'kendaraans' => $p->kendaraans->toArray(),
        ])->values()->all();

        return view('speedshop.work-order.create', compact('pelanggans', 'mekaniks', 'jasaServis', 'products', 'kotas', 'initialPelanggans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pelanggan_id' => ['required', 'exists:pelanggans,id'],
            'kendaraan_id' => ['required', 'exists:kendaraans,id'],
            'mekanik_id' => ['nullable', 'exists:mekaniks,id'],
            'sumber_kedatangan' => ['nullable', 'string', 'in:' . implode(',', array_keys(ServiceOrder::SUMBER_KEDATANGAN))],
            'kategori_service' => ['nullable', 'string', 'in:' . implode(',', array_keys(ServiceOrder::KATEGORI_SERVICE))],
            'keluhan' => ['nullable', 'string'],
            'estimasi_biaya' => ['nullable', 'numeric', 'min:0'],
            'tanggal_masuk' => ['required', 'date'],
            'next_service_date' => ['nullable', 'date'],
            'jasa' => ['nullable', 'array'],
            'jasa.*.jasa_servis_id' => ['required_with:jasa', 'exists:jasa_servis,id'],
            'jasa.*.biaya' => ['required_with:jasa', 'numeric', 'min:0'],
            'sparepart' => ['nullable', 'array'],
            'sparepart.*.product_id' => ['required_with:sparepart', 'exists:products,id'],
            'sparepart.*.qty' => ['required_with:sparepart', 'integer', 'min:1'],
            'sparepart.*.harga' => ['required_with:sparepart', 'numeric', 'min:0'],
        ]);

        $validated['kode_servis'] = ServiceOrder::generateKode();
        $validated['warehouse_id'] = $this->warehouseId();
        $validated['status'] = 'antri';

        $serviceOrder = ServiceOrder::create($validated);

        foreach ($request->input('jasa', []) as $jasa) {
            $serviceOrder->jasaServis()->attach($jasa['jasa_servis_id'], ['biaya' => $jasa['biaya']]);
        }

        foreach ($request->input('sparepart', []) as $sp) {
            $serviceOrder->products()->attach($sp['product_id'], [
                'qty' => $sp['qty'],
                'harga' => $sp['harga'],
            ]);
        }

        session()->flash('success', 'Work Order berhasil dibuat.');

        return redirect()->route('speedshop.wip.show', $serviceOrder);
    }

    public function start(Request $request, ServiceOrder $service_order)
    {
        $warehouseId = $this->warehouseId();
        if ($warehouseId !== null && $service_order->warehouse_id !== null && $service_order->warehouse_id !== $warehouseId) {
            abort(403, 'Akses ditolak.');
        }
        if ($service_order->status !== 'antri') {
            return back()->with('error', 'Hanya work order dengan status Antri yang dapat diserahkan ke mekanik.');
        }

        $validated = $request->validate([
            'mekanik_id' => ['required', 'exists:mekaniks,id'],
        ]);

        $service_order->update([
            'status' => 'proses',
            'mekanik_id' => $validated['mekanik_id'],
        ]);

        session()->flash('success', 'Work Order diserahkan ke mekanik dan status diubah ke Proses.');

        return redirect()->back();
    }

    public function complete(ServiceOrder $service_order)
    {
        $warehouseId = $this->warehouseId();
        if ($warehouseId !== null && $service_order->warehouse_id !== null && $service_order->warehouse_id !== $warehouseId) {
            abort(403, 'Akses ditolak.');
        }
        if ($service_order->status !== 'proses') {
            return back()->with('error', 'Hanya work order dengan status Proses yang dapat diubah ke Selesai.');
        }

        $service_order->update([
            'status' => 'selesai',
            'tanggal_selesai' => now(),
        ]);

        session()->flash('success', 'Work Order berhasil diselesaikan.');

        return redirect()->back();
    }

    public function show(ServiceOrder $service_order)
    {
        $warehouseId = $this->warehouseId();
        if ($warehouseId !== null && $service_order->warehouse_id !== null && $service_order->warehouse_id !== $warehouseId) {
            abort(403, 'Akses ditolak.');
        }

        $service_order->load(['pelanggan.kota', 'kendaraan', 'mekanik', 'jasaServis', 'products']);
        $mekaniks = Mekanik::aktif()->orderBy('nama')->get();

        return view('speedshop.work-order.show', compact('service_order', 'mekaniks'));
    }
}
