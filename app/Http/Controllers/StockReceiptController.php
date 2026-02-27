<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\StockMutation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockReceiptController extends Controller
{
    public function index()
    {
        $warehouseId = auth()->user()->activeWarehouseId();

        $received = StockMutation::with(['fromWarehouse', 'toWarehouse', 'items.product', 'user'])
            ->where('to_warehouse_id', $warehouseId)
            ->where('status', 'received')
            ->latest()
            ->limit(20)
            ->get();

        $pending = StockMutation::with(['fromWarehouse', 'toWarehouse', 'items.product', 'user'])
            ->where('to_warehouse_id', $warehouseId)
            ->where('status', 'in_transit')
            ->latest()
            ->get();

        return view('admin.stock-receipt.index', compact('received', 'pending'));
    }

    public function receive(Request $request)
    {
        $request->validate([
            'kode_mutasi' => ['required', 'string'],
        ]);

        $user = auth()->user();
        $kodeMutasi = trim($request->kode_mutasi);

        $mutation = StockMutation::with('items.product')
            ->where('kode_mutasi', $kodeMutasi)
            ->first();

        if (!$mutation) {
            return back()->with('error', "Kode surat jalan \"{$kodeMutasi}\" tidak ditemukan.");
        }

        if ($mutation->to_warehouse_id !== $user->activeWarehouseId()) {
            return back()->with('error', 'Surat jalan ini bukan untuk gudang/bengkel Anda.');
        }

        if ($mutation->status === 'received') {
            return back()->with('error', 'Surat jalan ini sudah pernah diterima sebelumnya.');
        }

        if ($mutation->status === 'draft') {
            return back()->with('error', 'Surat jalan ini belum dikirim oleh gudang asal.');
        }

        if ($mutation->status !== 'in_transit') {
            return back()->with('error', 'Status surat jalan tidak valid untuk penerimaan.');
        }

        DB::transaction(function () use ($mutation) {
            foreach ($mutation->items as $item) {
                $sourceProduct = Product::findOrFail($item->product_id);

                $destProduct = Product::firstOrCreate(
                    [
                        'kode_produk' => $sourceProduct->kode_produk,
                        'warehouse_id' => $mutation->to_warehouse_id,
                    ],
                    [
                        'nama_produk' => $sourceProduct->nama_produk,
                        'category_id' => $sourceProduct->category_id,
                        'brand_id' => $sourceProduct->brand_id,
                        'harga_pembelian' => $sourceProduct->harga_pembelian,
                        'harga_jual' => $sourceProduct->harga_jual,
                        'jumlah' => 0,
                    ]
                );

                $destProduct->increment('jumlah', $item->qty);

                StockMovement::create([
                    'product_id' => $destProduct->id,
                    'type' => 'masuk',
                    'qty' => $item->qty,
                    'keterangan' => 'Terima mutasi ' . $mutation->kode_mutasi,
                    'reference' => $mutation->kode_mutasi,
                ]);
            }
            $mutation->update(['status' => 'received']);
        });

        $totalItems = $mutation->items->sum('qty');
        return back()->with('success', "Stok diterima! {$totalItems} unit dari surat jalan {$kodeMutasi} berhasil masuk.");
    }
}
