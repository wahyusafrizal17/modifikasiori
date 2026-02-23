<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceOrderRequest;
use App\Models\JasaServis;
use App\Models\Kendaraan;
use App\Models\Mekanik;
use App\Models\Pelanggan;
use App\Models\Product;
use App\Models\ServiceOrder;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = ServiceOrder::with(['pelanggan', 'kendaraan', 'mekanik']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn ($q) => $q->where('kode_servis', 'like', "%{$s}%")
                ->orWhereHas('pelanggan', fn ($q2) => $q2->where('nama', 'like', "%{$s}%")));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->latest()->paginate(20)->withQueryString();

        return view('admin.service-orders.index', compact('orders'));
    }

    public function create()
    {
        $pelanggans = Pelanggan::orderBy('nama')->get();
        $mekaniks = Mekanik::aktif()->orderBy('nama')->get();
        $jasaServis = JasaServis::forUser()->orderBy('nama')->get();
        $products = Product::forUser()->orderBy('nama_produk')->get();

        return view('admin.service-orders.create', compact('pelanggans', 'mekaniks', 'jasaServis', 'products'));
    }

    public function store(ServiceOrderRequest $request)
    {
        $data = $request->validated();

        if (!empty($data['product_items'])) {
            foreach ($data['product_items'] as $item) {
                $product = Product::find($item['product_id']);
                if ($product && $product->jumlah < $item['qty']) {
                    return back()->withInput()->with('error', "Stok {$product->nama_produk} tidak cukup (tersisa {$product->jumlah} unit).");
                }
            }
        }

        $data['kode_servis'] = ServiceOrder::generateKode();
        $data['status'] = 'antri';

        return DB::transaction(function () use ($data) {
            $order = ServiceOrder::create($data);

            if (!empty($data['jasa_items'])) {
                foreach ($data['jasa_items'] as $item) {
                    $order->jasaServis()->attach($item['jasa_servis_id'], ['biaya' => $item['biaya']]);
                }
            }

            if (!empty($data['product_items'])) {
                foreach ($data['product_items'] as $item) {
                    $order->products()->attach($item['product_id'], [
                        'qty' => $item['qty'],
                        'harga' => $item['harga'],
                    ]);
                    Product::where('id', $item['product_id'])->decrement('jumlah', $item['qty']);
                    StockMovement::create([
                        'product_id' => $item['product_id'],
                        'type' => 'keluar',
                        'qty' => $item['qty'],
                        'keterangan' => 'Pemakaian servis',
                        'reference' => $data['kode_servis'],
                    ]);
                }
            }

            $order->update(['estimasi_biaya' => $order->fresh()->grand_total]);

            return redirect()->route('admin.service-orders.show', $order)
                ->with('success', 'Work order berhasil dibuat.');
        });
    }

    public function show(ServiceOrder $serviceOrder)
    {
        $serviceOrder->load(['pelanggan.kota', 'kendaraan', 'mekanik', 'jasaServis', 'products', 'invoice']);

        return view('admin.service-orders.show', compact('serviceOrder'));
    }

    public function edit(ServiceOrder $serviceOrder)
    {
        if (in_array($serviceOrder->status, ['selesai', 'dibatalkan'])) {
            return redirect()->route('admin.service-orders.show', $serviceOrder)
                ->with('error', 'Order yang sudah selesai/dibatalkan tidak dapat diedit.');
        }

        $serviceOrder->load(['jasaServis', 'products']);
        $pelanggans = Pelanggan::orderBy('nama')->get();
        $mekaniks = Mekanik::aktif()->orderBy('nama')->get();
        $jasaServis = JasaServis::forUser()->orderBy('nama')->get();
        $products = Product::forUser()->orderBy('nama_produk')->get();

        return view('admin.service-orders.edit', compact('serviceOrder', 'pelanggans', 'mekaniks', 'jasaServis', 'products'));
    }

    public function update(ServiceOrderRequest $request, ServiceOrder $serviceOrder)
    {
        $data = $request->validated();

        if (!empty($data['product_items'])) {
            $oldQtyMap = $serviceOrder->products->pluck('pivot.qty', 'id')->toArray();
            foreach ($data['product_items'] as $item) {
                $product = Product::find($item['product_id']);
                if (!$product) continue;
                $restored = $oldQtyMap[$item['product_id']] ?? 0;
                $available = $product->jumlah + $restored;
                if ($available < $item['qty']) {
                    return back()->withInput()->with('error', "Stok {$product->nama_produk} tidak cukup (tersedia {$available} unit).");
                }
            }
        }

        return DB::transaction(function () use ($data, $serviceOrder) {
            $serviceOrder->update($data);

            foreach ($serviceOrder->products as $oldProduct) {
                Product::where('id', $oldProduct->id)->increment('jumlah', $oldProduct->pivot->qty);
                StockMovement::create([
                    'product_id' => $oldProduct->id,
                    'type' => 'masuk',
                    'qty' => $oldProduct->pivot->qty,
                    'keterangan' => 'Koreksi edit servis',
                    'reference' => $serviceOrder->kode_servis,
                ]);
            }

            $serviceOrder->jasaServis()->detach();
            $serviceOrder->products()->detach();

            if (!empty($data['jasa_items'])) {
                foreach ($data['jasa_items'] as $item) {
                    $serviceOrder->jasaServis()->attach($item['jasa_servis_id'], ['biaya' => $item['biaya']]);
                }
            }

            if (!empty($data['product_items'])) {
                foreach ($data['product_items'] as $item) {
                    $serviceOrder->products()->attach($item['product_id'], [
                        'qty' => $item['qty'],
                        'harga' => $item['harga'],
                    ]);
                    Product::where('id', $item['product_id'])->decrement('jumlah', $item['qty']);
                    StockMovement::create([
                        'product_id' => $item['product_id'],
                        'type' => 'keluar',
                        'qty' => $item['qty'],
                        'keterangan' => 'Pemakaian servis',
                        'reference' => $serviceOrder->kode_servis,
                    ]);
                }
            }

            $serviceOrder->update(['estimasi_biaya' => $serviceOrder->fresh()->grand_total]);

            return redirect()->route('admin.service-orders.show', $serviceOrder)
                ->with('success', 'Work order berhasil diperbarui.');
        });
    }

    public function updateStatus(Request $request, ServiceOrder $serviceOrder)
    {
        $request->validate(['status' => 'required|in:antri,proses,selesai,dibatalkan']);

        DB::transaction(function () use ($request, $serviceOrder) {
            if ($request->status === 'dibatalkan' && $serviceOrder->status !== 'dibatalkan') {
                $serviceOrder->load('products');
                foreach ($serviceOrder->products as $product) {
                    Product::where('id', $product->id)->increment('jumlah', $product->pivot->qty);
                    StockMovement::create([
                        'product_id' => $product->id,
                        'type' => 'masuk',
                        'qty' => $product->pivot->qty,
                        'keterangan' => 'Pembatalan servis',
                        'reference' => $serviceOrder->kode_servis,
                    ]);
                }
            }

            $serviceOrder->update([
                'status' => $request->status,
                'tanggal_selesai' => $request->status === 'selesai' ? now()->toDateString() : $serviceOrder->tanggal_selesai,
            ]);
        });

        session()->flash('success', "Status diubah menjadi {$request->status}.");

        return response()->json(['success' => true]);
    }

    public function kendaraanByPelanggan(Pelanggan $pelanggan)
    {
        return response()->json(
            $pelanggan->kendaraans()->select('id', 'nomor_polisi', 'merk', 'tipe')->get()
        );
    }

    public function destroy(ServiceOrder $serviceOrder)
    {
        DB::transaction(function () use ($serviceOrder) {
            if ($serviceOrder->status !== 'dibatalkan') {
                $serviceOrder->load('products');
                foreach ($serviceOrder->products as $product) {
                    Product::where('id', $product->id)->increment('jumlah', $product->pivot->qty);
                    StockMovement::create([
                        'product_id' => $product->id,
                        'type' => 'masuk',
                        'qty' => $product->pivot->qty,
                        'keterangan' => 'Hapus work order',
                        'reference' => $serviceOrder->kode_servis,
                    ]);
                }
            }

            $serviceOrder->delete();
        });

        return redirect()->route('admin.service-orders.index')
            ->with('success', 'Work order berhasil dihapus.');
    }
}
