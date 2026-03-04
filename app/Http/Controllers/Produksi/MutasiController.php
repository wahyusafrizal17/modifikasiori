<?php

namespace App\Http\Controllers\Produksi;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\Mutasi;
use App\Models\MutasiItem;
use App\Models\Packing;
use App\Models\Product;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MutasiController extends Controller
{
    public function index(Request $request)
    {
        $query = Mutasi::with(['user', 'warehouse', 'items.product']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                  ->orWhere('nomor_surat_jalan', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $mutasis = $query->latest()->paginate(20)->withQueryString();

        $totalAll = Mutasi::count();
        $totalDikirim = Mutasi::where('status', 'dikirim')->count();
        $totalDiterima = Mutasi::where('status', 'diterima')->count();

        return view('produksi.mutasi.index', compact('mutasis', 'totalAll', 'totalDikirim', 'totalDiterima'));
    }

    public function create()
    {
        $availableProducts = $this->getAvailableProducts();
        $warehouses = Warehouse::orderBy('nama')->get();
        return view('produksi.mutasi.create', compact('availableProducts', 'warehouses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'nomor_surat_jalan' => ['required', 'string', 'max:100'],
            'catatan' => ['nullable', 'string', 'max:500'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        $availableProducts = $this->getAvailableProducts();

        foreach ($request->items as $item) {
            $available = $availableProducts->firstWhere('product_id', $item['product_id']);
            if (!$available || $available->available < (int) $item['quantity']) {
                $name = $available->nama_produk ?? 'Produk';
                return response()->json([
                    'message' => "Stok {$name} tidak mencukupi. Tersedia: " . ($available->available ?? 0),
                ], 422);
            }
        }

        $mutasi = null;

        DB::transaction(function () use ($request, &$mutasi) {
            $mutasi = Mutasi::create([
                'kode' => Mutasi::generateKode(),
                'nomor_surat_jalan' => $request->nomor_surat_jalan,
                'tanggal' => now()->toDateString(),
                'user_id' => auth()->id(),
                'warehouse_id' => $request->warehouse_id,
                'sumber' => 'produksi',
                'status' => 'dikirim',
                'catatan' => $request->catatan,
            ]);

            foreach ($request->items as $item) {
                $mutasi->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => (int) $item['quantity'],
                ]);
            }

            $warehouse = Warehouse::find($request->warehouse_id);

            $warehouseUsers = User::where('section', 'warehouse')
                ->where(function ($q) use ($request) {
                    $q->where('warehouse_id', $request->warehouse_id)
                      ->orWhereNull('warehouse_id');
                })
                ->pluck('id');
            $adminUsers = User::where('role', 'Admin')->pluck('id');
            $notifyUserIds = $warehouseUsers->merge($adminUsers)->unique();

            $totalItems = collect($request->items)->sum('quantity');
            $mutasiLink = route('warehouse.transaksi-speedshop.show', $mutasi);
            $speedshopLink = route('speedshop.stock-in.pembelian-mo.show-mutasi', $mutasi);

            foreach ($notifyUserIds as $userId) {
                AppNotification::create([
                    'user_id' => $userId,
                    'type' => 'mutasi_produk',
                    'title' => 'Mutasi Produk Baru',
                    'message' => "Mutasi {$mutasi->kode} ({$totalItems} unit) ke {$warehouse->nama} — Surat Jalan: {$mutasi->nomor_surat_jalan}",
                    'data' => ['mutasi_id' => $mutasi->id, 'kode' => $mutasi->kode],
                    'link' => $mutasiLink,
                ]);
            }

            $speedshopUsers = User::where('section', 'speedshop')
                ->where('warehouse_id', $request->warehouse_id)
                ->pluck('id');
            foreach ($speedshopUsers as $userId) {
                AppNotification::create([
                    'user_id' => $userId,
                    'type' => 'mutasi_produk',
                    'title' => 'Mutasi Pembelian MO Baru',
                    'message' => "Mutasi {$mutasi->kode} ({$totalItems} unit) ke {$warehouse->nama} — Surat Jalan: {$mutasi->nomor_surat_jalan}",
                    'data' => ['mutasi_id' => $mutasi->id, 'kode' => $mutasi->kode],
                    'link' => $speedshopLink,
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Mutasi berhasil dibuat. Notifikasi telah dikirim ke Warehouse.',
            'redirect' => route('produksi.mutasi.show', $mutasi),
        ]);
    }

    public function show(Mutasi $mutasi)
    {
        $mutasi->load(['user', 'warehouse', 'items.product']);
        return view('produksi.mutasi.show', compact('mutasi'));
    }

    private function getAvailableProducts()
    {
        $packed = DB::table('packing_details')
            ->select('product_id', DB::raw('SUM(quantity) as total_packed'))
            ->groupBy('product_id');

        $mutated = DB::table('mutasi_items')
            ->select('product_id', DB::raw('SUM(quantity) as total_mutated'))
            ->groupBy('product_id');

        return DB::table('products')
            ->joinSub($packed, 'packed', 'products.id', '=', 'packed.product_id')
            ->leftJoinSub($mutated, 'mutated', 'products.id', '=', 'mutated.product_id')
            ->select(
                'products.id as product_id',
                'products.kode_produk',
                'products.nama_produk',
                DB::raw('CAST(packed.total_packed AS SIGNED) as total_packed'),
                DB::raw('COALESCE(CAST(mutated.total_mutated AS SIGNED), 0) as total_mutated'),
                DB::raw('CAST(packed.total_packed AS SIGNED) - COALESCE(CAST(mutated.total_mutated AS SIGNED), 0) as available')
            )
            ->having('available', '>', 0)
            ->orderBy('products.nama_produk')
            ->get();
    }
}
