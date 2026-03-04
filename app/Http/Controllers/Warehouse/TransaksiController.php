<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseTransaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $query = WarehouseTransaksi::with(['user', 'approver', 'items.product', 'items.supplier']);

        $warehouseId = auth()->user()->activeWarehouseId();
        if ($warehouseId !== null) {
            $query->where('warehouse_id', $warehouseId);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('kode', 'like', "%{$request->search}%");
        }

        $transaksis = $query->latest()->paginate(20)->withQueryString();

        $totalAll = WarehouseTransaksi::when($warehouseId !== null, fn ($q) => $q->where('warehouse_id', $warehouseId))->count();
        $totalApproved = WarehouseTransaksi::when($warehouseId !== null, fn ($q) => $q->where('warehouse_id', $warehouseId))->where('status', 'approved')->count();
        $totalRejected = WarehouseTransaksi::when($warehouseId !== null, fn ($q) => $q->where('warehouse_id', $warehouseId))->where('status', 'rejected')->count();

        return view('warehouse.transaksi.index', compact('transaksis', 'totalAll', 'totalApproved', 'totalRejected'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('nama')->get();
        $warehouseId = auth()->user()->activeWarehouseId();
        $products = $warehouseId !== null
            ? Product::where('warehouse_id', $warehouseId)->orderBy('nama_produk')->get()
            : Product::orderBy('nama_produk')->get();

        return view('warehouse.transaksi.create', compact('suppliers', 'products'));
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

        DB::transaction(function () use ($request) {
            $warehouseId = auth()->user()->activeWarehouseId();

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

            $managers = User::where('section', 'warehouse')
                ->where(function ($q) use ($warehouseId) {
                    $q->where('warehouse_id', $warehouseId)->orWhereNull('warehouse_id');
                })
                ->where('role', 'Manager')
                ->get();

            foreach ($managers as $manager) {
                AppNotification::create([
                    'user_id' => $manager->id,
                    'type' => 'warehouse_transaksi_pending',
                    'title' => 'Transaksi Warehouse Baru Menunggu Verifikasi',
                    'message' => auth()->user()->name . ' mengajukan transaksi ' . $transaksi->kode,
                    'data' => ['warehouse_transaksi_id' => $transaksi->id],
                    'link' => route('warehouse.transaksi.show', $transaksi),
                ]);
            }
        });

        session()->flash('success', 'Transaksi berhasil diajukan, menunggu verifikasi Manager Warehouse.');
        return response()->json(['success' => true]);
    }

    public function show(WarehouseTransaksi $warehouse_transaksi)
    {
        $warehouseId = auth()->user()->activeWarehouseId();
        if ($warehouseId !== null && $warehouse_transaksi->warehouse_id !== $warehouseId) {
            abort(403);
        }

        $warehouse_transaksi->load(['user', 'approver', 'warehouse', 'items.product', 'items.supplier']);

        return view('warehouse.transaksi.show', ['transaksi' => $warehouse_transaksi]);
    }

    public function approve(WarehouseTransaksi $warehouse_transaksi)
    {
        $transaksi = $warehouse_transaksi;
        if (!$transaksi->isPending()) {
            return response()->json(['message' => 'Transaksi sudah diproses.'], 422);
        }

        $warehouseId = auth()->user()->activeWarehouseId();
        if ($warehouseId !== null && $transaksi->warehouse_id !== $warehouseId) {
            abort(403);
        }

        DB::transaction(function () use ($transaksi) {
            $transaksi->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            foreach ($transaksi->items as $item) {
                $item->product->increment('jumlah', $item->qty);
            }

            AppNotification::create([
                'user_id' => $transaksi->user_id,
                'type' => 'warehouse_transaksi_approved',
                'title' => 'Transaksi Warehouse Disetujui',
                'message' => auth()->user()->name . ' menyetujui transaksi ' . $transaksi->kode,
                'data' => ['warehouse_transaksi_id' => $transaksi->id],
                'link' => route('warehouse.transaksi.show', $transaksi),
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

        $warehouseId = auth()->user()->activeWarehouseId();
        if ($warehouseId !== null && $transaksi->warehouse_id !== $warehouseId) {
            abort(403);
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
                'type' => 'warehouse_transaksi_rejected',
                'title' => 'Transaksi Warehouse Ditolak',
                'message' => auth()->user()->name . ' menolak transaksi ' . $transaksi->kode . ': ' . $request->rejected_reason,
                'data' => ['warehouse_transaksi_id' => $transaksi->id],
                'link' => route('warehouse.transaksi.show', $transaksi),
            ]);
        });

        return response()->json(['success' => true, 'message' => 'Transaksi telah ditolak.']);
    }
}
