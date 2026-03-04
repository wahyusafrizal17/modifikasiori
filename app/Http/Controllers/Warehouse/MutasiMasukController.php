<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\Mutasi;
use App\Models\Product;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MutasiMasukController extends Controller
{
    public function index(Request $request)
    {
        return $this->mutasiIndex($request, [
            'pageTitle' => 'Mutasi Masuk',
            'breadcrumb' => 'Mutasi Masuk',
            'indexRoute' => 'warehouse.mutasi-masuk.index',
            'description' => 'Produk yang di-mutasi dari Produksi ke Warehouse Anda',
            'searchPlaceholder' => 'Cari kode / surat jalan...',
        ]);
    }

    public function transaksiSpeedshop(Request $request)
    {
        return $this->mutasiIndex($request, [
            'pageTitle' => 'Transaksi Speedshop (Mutasi)',
            'breadcrumb' => 'Transaksi Speedshop (Mutasi)',
            'indexRoute' => 'warehouse.transaksi-speedshop',
            'description' => 'Lihat mutasi dari Produksi ke Warehouse dan buat mutasi ke Speedshop',
            'searchPlaceholder' => 'Cari No. Surat Jalan...',
            'showSumberFilter' => true,
            'showCreateMutasiSpeedshop' => true,
        ]);
    }

    protected function mutasiIndex(Request $request, array $config): \Illuminate\View\View
    {
        $query = Mutasi::with(['user', 'warehouse', 'items.product']);

        $warehouseId = auth()->user()->activeWarehouseId();

        if (! empty($config['showCreateMutasiSpeedshop'])) {
            // Transaksi Speedshop: tampilkan mutasi yang tujuan ke warehouse saya ATAU mutasi yang saya buat (kirim ke speedshop)
            $query->where(function ($q) use ($warehouseId) {
                $q->where('warehouse_id', $warehouseId);
                $q->orWhere(function ($q2) {
                    $q2->where('user_id', auth()->id())
                        ->where('sumber', 'warehouse');
                });
            });
        } elseif ($warehouseId !== null) {
            $query->where('warehouse_id', $warehouseId);
        }

        if (! empty($config['showSumberFilter']) && $request->filled('sumber')) {
            if (in_array($request->sumber, ['produksi', 'warehouse'])) {
                $query->where('sumber', $request->sumber);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                  ->orWhere('nomor_surat_jalan', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if (in_array($request->status, ['dikirim', 'diterima'])) {
                $query->where('status', $request->status);
            }
        }

        $mutasis = $query->latest()->paginate(20)->withQueryString();

        return view('warehouse.mutasi-masuk.index', compact('mutasis', 'config'));
    }

    public function createMutasiSpeedshop()
    {
        $warehouseId = auth()->user()->activeWarehouseId();
        if ($warehouseId === null) {
            return redirect()->route('warehouse.transaksi-speedshop')
                ->with('error', 'Warehouse tidak terdeteksi.');
        }

        $products = Product::where('warehouse_id', $warehouseId)
            ->where('jumlah', '>', 0)
            ->orderBy('nama_produk')
            ->get(['id', 'kode_produk', 'nama_produk', 'jumlah', 'harga_jual_speedshop']);

        $warehouses = Warehouse::orderBy('nama')->get(['id', 'nama', 'alamat']);

        return view('warehouse.mutasi-ke-speedshop.create', compact('products', 'warehouses'));
    }

    public function storeMutasiSpeedshop(Request $request)
    {
        $request->validate([
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'nomor_surat_jalan' => ['required', 'string', 'max:100'],
            'catatan' => ['nullable', 'string', 'max:500'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        $warehouseId = auth()->user()->activeWarehouseId();
        if ($warehouseId === null) {
            return response()->json(['message' => 'Warehouse tidak terdeteksi.'], 403);
        }

        $tujuanWarehouseId = (int) $request->warehouse_id;

        $products = Product::where('warehouse_id', $warehouseId)
            ->whereIn('id', collect($request->items)->pluck('product_id'))
            ->get()
            ->keyBy('id');

        foreach ($request->items as $item) {
            $product = $products->get($item['product_id']);
            $qty = (int) $item['quantity'];
            if (! $product || $product->jumlah < $qty) {
                return response()->json([
                    'message' => 'Stok ' . ($product->nama_produk ?? 'produk') . ' tidak mencukupi. Tersedia: ' . ($product->jumlah ?? 0),
                ], 422);
            }
        }

        $mutasi = null;
        $warehouseTujuan = Warehouse::find($tujuanWarehouseId);

        DB::transaction(function () use ($request, $warehouseId, $tujuanWarehouseId, $warehouseTujuan, &$mutasi) {
            $mutasi = Mutasi::create([
                'kode' => Mutasi::generateKode(),
                'nomor_surat_jalan' => $request->nomor_surat_jalan,
                'tanggal' => now()->toDateString(),
                'user_id' => auth()->id(),
                'warehouse_id' => $tujuanWarehouseId,
                'sumber' => 'warehouse',
                'status' => 'dikirim',
                'catatan' => $request->catatan,
            ]);

            foreach ($request->items as $item) {
                $mutasi->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => (int) $item['quantity'],
                ]);
                Product::where('id', $item['product_id'])->decrement('jumlah', (int) $item['quantity']);
            }

            $totalItems = collect($request->items)->sum('quantity');
            $speedshopLink = route('speedshop.stock-in.pembelian-mo.show-mutasi', $mutasi);
            $speedshopUsers = User::where('section', 'speedshop')
                ->where(function ($q) use ($tujuanWarehouseId) {
                    $q->where('warehouse_id', $tujuanWarehouseId)
                      ->orWhereNull('warehouse_id');
                })
                ->pluck('id')
                ->unique();

            $tujuanNama = $warehouseTujuan ? $warehouseTujuan->nama : 'Speedshop';

            foreach ($speedshopUsers as $userId) {
                AppNotification::create([
                    'user_id' => $userId,
                    'type' => 'mutasi_produk',
                    'title' => 'Mutasi dari Warehouse',
                    'message' => "Mutasi {$mutasi->kode} ({$totalItems} unit) ke {$tujuanNama} — Surat Jalan: {$mutasi->nomor_surat_jalan}",
                    'data' => ['mutasi_id' => $mutasi->id, 'kode' => $mutasi->kode],
                    'link' => $speedshopLink,
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Mutasi ke Speedshop berhasil dibuat. Notifikasi telah dikirim ke Speedshop.',
            'redirect' => route('warehouse.transaksi-speedshop.show', $mutasi),
        ]);
    }

    public function show(Request $request, Mutasi $mutasi)
    {
        $warehouseId = auth()->user()->activeWarehouseId();

        $isCreator = $mutasi->user_id === auth()->id();
        $isReceiver = $warehouseId !== null && $mutasi->warehouse_id === $warehouseId;

        if (! $isCreator && ! $isReceiver) {
            abort(403, 'Anda tidak memiliki akses ke mutasi ini.');
        }

        $mutasi->load(['user', 'warehouse', 'items.product']);

        $fromTransaksiSpeedshop = $request->query('from') === 'transaksi-speedshop'
            || request()->routeIs('warehouse.transaksi-speedshop.show');

        return view('warehouse.mutasi-masuk.show', compact('mutasi', 'fromTransaksiSpeedshop'));
    }

    public function verify(Mutasi $mutasi)
    {
        if (!$mutasi->isDikirim()) {
            return response()->json(['message' => 'Mutasi sudah diverifikasi sebelumnya.'], 422);
        }

        $warehouseId = auth()->user()->activeWarehouseId();
        $isCreator = $mutasi->user_id === auth()->id();
        $isReceiver = $warehouseId !== null && $mutasi->warehouse_id === $warehouseId;

        if (! $isCreator && ! $isReceiver) {
            abort(403, 'Anda tidak memiliki akses ke mutasi ini.');
        }

        if (!auth()->user()->isManager() && !auth()->user()->isAdmin()) {
            abort(403, 'Hanya Manager Warehouse atau Admin yang dapat memverifikasi mutasi.');
        }

        DB::transaction(function () use ($mutasi) {
            $mutasi->update(['status' => 'diterima']);

            foreach ($mutasi->items as $item) {
                $item->product->increment('jumlah', $item->quantity);
            }

            AppNotification::create([
                'user_id' => $mutasi->user_id,
                'type' => 'mutasi_diterima',
                'title' => 'Mutasi Diterima',
                'message' => auth()->user()->name . ' telah memverifikasi dan menerima mutasi ' . $mutasi->kode,
                'data' => ['mutasi_id' => $mutasi->id],
                'link' => route('produksi.mutasi.show', $mutasi),
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Mutasi berhasil diverifikasi. Stok produk telah ditambahkan ke warehouse.',
        ]);
    }
}
