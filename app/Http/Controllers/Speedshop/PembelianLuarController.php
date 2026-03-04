<?php

namespace App\Http\Controllers\Speedshop;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use App\Models\WarehouseTransaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PembelianLuarController extends Controller
{
    private function warehouseId(): ?int
    {
        return auth()->user()->activeWarehouseId();
    }

    public function index(Request $request)
    {
        $warehouseId = $this->warehouseId();

        if (!$warehouseId) {
            $transaksis = WarehouseTransaksi::query()->whereRaw('1=0')->paginate(20);
            $totalAll = $totalApproved = $totalRejected = 0;
            return view('speedshop.stock-in.pembelian-luar.index', compact('transaksis', 'totalAll', 'totalApproved', 'totalRejected'));
        }

        $query = WarehouseTransaksi::with(['user', 'approver', 'items.product', 'items.supplier'])
            ->where('warehouse_id', $warehouseId);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('kode', 'like', "%{$request->search}%");
        }

        $transaksis = $query->latest()->paginate(20)->withQueryString();

        $totalAll = WarehouseTransaksi::where('warehouse_id', $warehouseId)->count();
        $totalApproved = WarehouseTransaksi::where('warehouse_id', $warehouseId)->where('status', 'approved')->count();
        $totalRejected = WarehouseTransaksi::where('warehouse_id', $warehouseId)->where('status', 'rejected')->count();

        return view('speedshop.stock-in.pembelian-luar.index', compact('transaksis', 'totalAll', 'totalApproved', 'totalRejected'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('nama')->get();
        $warehouseId = $this->warehouseId();
        $products = $warehouseId !== null
            ? Product::where('warehouse_id', $warehouseId)->orderBy('nama_produk')->get()
            : Product::orderBy('nama_produk')->get();

        return view('speedshop.stock-in.pembelian-luar.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'catatan' => ['nullable', 'string', 'max:500'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.supplier_id' => ['nullable', 'exists:suppliers,id'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.harga_pembelian' => ['required', 'numeric', 'min:0'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
        ]);

        $warehouseId = $this->warehouseId();
        if (!$warehouseId) {
            return response()->json(['message' => 'Warehouse tidak ditemukan.'], 422);
        }

        DB::transaction(function () use ($request, $warehouseId) {
            $transaksi = WarehouseTransaksi::create([
                'kode' => WarehouseTransaksi::generateKode(),
                'user_id' => auth()->id(),
                'warehouse_id' => $warehouseId,
                'status' => 'pending',
                'catatan' => $request->catatan,
            ]);

            foreach ($request->items as $item) {
                $transaksi->items()->create([
                    'supplier_id' => $item['supplier_id'] ?? null,
                    'product_id' => $item['product_id'],
                    'harga_pembelian' => $item['harga_pembelian'],
                    'qty' => (int) $item['qty'],
                ]);
            }

            $managers = User::where('section', 'speedshop')
                ->where(function ($q) use ($warehouseId) {
                    $q->where('warehouse_id', $warehouseId)->orWhereNull('warehouse_id');
                })
                ->where('role', 'Manager')
                ->get();

            foreach ($managers as $manager) {
                AppNotification::create([
                    'user_id' => $manager->id,
                    'type' => 'speedshop_pembelian_luar_pending',
                    'title' => 'Pembelian Luar Speedshop Menunggu Verifikasi',
                    'message' => auth()->user()->name . ' mengajukan pembelian luar ' . $transaksi->kode,
                    'data' => ['warehouse_transaksi_id' => $transaksi->id],
                    'link' => route('speedshop.stock-in.pembelian-luar.show', $transaksi),
                ]);
            }
        });

        session()->flash('success', 'Transaksi berhasil diajukan, menunggu verifikasi Manager Speedshop.');
        return response()->json(['success' => true]);
    }

    public function show(WarehouseTransaksi $warehouse_transaksi)
    {
        $warehouseId = $this->warehouseId();
        if ($warehouse_transaksi->warehouse_id !== $warehouseId) {
            abort(403);
        }

        $warehouse_transaksi->load(['user', 'approver', 'warehouse', 'items.product', 'items.supplier']);
        $transaksi = $warehouse_transaksi;

        return view('speedshop.stock-in.pembelian-luar.show', compact('transaksi'));
    }

    public function approve(WarehouseTransaksi $warehouse_transaksi)
    {
        $transaksi = $warehouse_transaksi;
        if (!$transaksi->isPending()) {
            return response()->json(['message' => 'Transaksi sudah diproses.'], 422);
        }

        $warehouseId = $this->warehouseId();
        if ($transaksi->warehouse_id !== $warehouseId) {
            abort(403);
        }

        if (!auth()->user()->isManager() && !auth()->user()->isAdmin()) {
            abort(403, 'Hanya Manager Speedshop atau Admin yang dapat menyetujui.');
        }

        DB::transaction(function () use ($transaksi) {
            $transaksi->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            $tujuanWarehouseId = $transaksi->warehouse_id;

            foreach ($transaksi->items as $item) {
                $sourceProduct = $item->product;
                $productTujuan = Product::where('warehouse_id', $tujuanWarehouseId)
                    ->where('kode_produk', $sourceProduct->kode_produk)
                    ->first();

                if ($productTujuan) {
                    $productTujuan->increment('jumlah', $item->qty);
                } else {
                    Product::create([
                        'kode_produk' => $sourceProduct->kode_produk,
                        'nama_produk' => $sourceProduct->nama_produk,
                        'category_id' => $sourceProduct->category_id,
                        'brand_id' => $sourceProduct->brand_id,
                        'warehouse_id' => $tujuanWarehouseId,
                        'jumlah' => $item->qty,
                        'harga_pembelian' => $item->harga_pembelian ?? $sourceProduct->harga_pembelian ?? 0,
                        'harga_jual' => $sourceProduct->harga_jual ?? 0,
                        'hpp' => $sourceProduct->hpp ?? 0,
                        'harga_jual_speedshop' => $sourceProduct->harga_jual_speedshop ?? 0,
                        'harga_jual_reseler' => $sourceProduct->harga_jual_reseler ?? 0,
                        'harga_eceran_terendah' => $sourceProduct->harga_eceran_terendah ?? 0,
                    ]);
                }
            }

            AppNotification::create([
                'user_id' => $transaksi->user_id,
                'type' => 'speedshop_pembelian_luar_approved',
                'title' => 'Pembelian Luar Disetujui',
                'message' => auth()->user()->name . ' menyetujui transaksi ' . $transaksi->kode,
                'data' => ['warehouse_transaksi_id' => $transaksi->id],
                'link' => route('speedshop.stock-in.pembelian-luar.show', $transaksi),
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Transaksi ' . $transaksi->kode . ' berhasil disetujui. Stok telah diperbarui.',
        ]);
    }

    public function reject(Request $request, WarehouseTransaksi $warehouse_transaksi)
    {
        $transaksi = $warehouse_transaksi;
        if (!$transaksi->isPending()) {
            return response()->json(['message' => 'Transaksi sudah diproses.'], 422);
        }

        $warehouseId = $this->warehouseId();
        if ($transaksi->warehouse_id !== $warehouseId) {
            abort(403);
        }

        if (!auth()->user()->isManager() && !auth()->user()->isAdmin()) {
            abort(403, 'Hanya Manager Speedshop atau Admin yang dapat menolak.');
        }

        $request->validate([
            'rejected_reason' => ['required', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($request, $transaksi) {
            $transaksi->update([
                'status' => 'rejected',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'rejected_reason' => $request->rejected_reason,
            ]);

            AppNotification::create([
                'user_id' => $transaksi->user_id,
                'type' => 'speedshop_pembelian_luar_rejected',
                'title' => 'Pembelian Luar Ditolak',
                'message' => auth()->user()->name . ' menolak transaksi ' . $transaksi->kode . ': ' . $request->rejected_reason,
                'data' => ['warehouse_transaksi_id' => $transaksi->id],
                'link' => route('speedshop.stock-in.pembelian-luar.show', $transaksi),
            ]);
        });

        return response()->json(['success' => true, 'message' => 'Transaksi telah ditolak.']);
    }
}
