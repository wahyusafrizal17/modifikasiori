<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\TransaksiOffline;
use App\Models\TransaksiOfflineItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransaksiOfflineController extends Controller
{
    public function index(Request $request)
    {
        $query = TransaksiOffline::with(['items.product', 'user']);

        $warehouseId = auth()->user()->activeWarehouseId();
        if ($warehouseId !== null) {
            $query->where('warehouse_id', $warehouseId);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('no_transaksi', 'like', "%{$request->search}%")
                    ->orWhere('nama_toko', 'like', "%{$request->search}%")
                    ->orWhere('tujuan', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('tujuan')) {
            $query->where('tujuan', $request->tujuan);
        }

        $transaksis = $query->latest()->paginate(20)->withQueryString();

        return view('warehouse.transaksi-offline.index', compact('transaksis'));
    }

    public function show(TransaksiOffline $transaksiOffline)
    {
        $warehouseId = auth()->user()->activeWarehouseId();
        if ($warehouseId !== null && $transaksiOffline->warehouse_id != $warehouseId) {
            abort(404);
        }

        $transaksiOffline->load(['items.product', 'user', 'warehouse', 'petugas']);

        return view('warehouse.transaksi-offline.show', compact('transaksiOffline'));
    }

    public function create()
    {
        $warehouseId = auth()->user()->activeWarehouseId();
        $products = $warehouseId !== null
            ? Product::where('warehouse_id', $warehouseId)->orderBy('nama_produk')->get()
            : Product::orderBy('nama_produk')->get();

        return view('warehouse.transaksi-offline.create', compact('products'));
    }

    public function lookupByNoTransaksi(Request $request)
    {
        $noTransaksi = $request->get('no_transaksi');
        if (! $noTransaksi || ! trim($noTransaksi)) {
            return response()->json(['found' => false]);
        }

        $warehouseId = auth()->user()->activeWarehouseId();
        $query = TransaksiOffline::with(['items.product'])
            ->where('no_transaksi', trim($noTransaksi))
            ->where('tujuan', 'speedshop');

        if ($warehouseId !== null) {
            $query->where('warehouse_id', $warehouseId);
        }

        $transaksi = $query->first();
        if (! $transaksi) {
            return response()->json(['found' => false]);
        }

        return response()->json([
            'found' => true,
            'transaksi' => [
                'id' => $transaksi->id,
                'no_transaksi' => $transaksi->no_transaksi,
                'tujuan' => $transaksi->tujuan,
                'nama_toko' => $transaksi->nama_toko,
                'alamat' => $transaksi->alamat,
                'no_hp' => $transaksi->no_hp,
                'jenis_pembayaran' => $transaksi->jenis_pembayaran,
                'items' => $transaksi->items->map(fn ($i) => [
                    'product_id' => $i->product_id,
                    'kode_produk' => $i->product->kode_produk,
                    'nama_produk' => $i->product->nama_produk,
                    'qty' => $i->qty,
                ]),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'no_transaksi' => ['nullable', 'string', 'max:100'],
            'tujuan' => ['required', 'in:speedshop,reseller,umum'],
            'nama_toko' => ['nullable', 'string', 'max:200'],
            'alamat' => ['nullable', 'string', 'max:500'],
            'no_hp' => ['nullable', 'string', 'max:50'],
            'jenis_pembayaran' => ['nullable', 'string', 'max:50'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
        ]);

        if ($validator->fails()) {
            return redirect()->route('warehouse.transaksi-offline.create')
                ->withInput()
                ->withErrors($validator);
        }

        $warehouseId = auth()->user()->activeWarehouseId();

        foreach ($request->items as $item) {
            $product = Product::findOrFail($item['product_id']);
            if ($product->jumlah < $item['qty']) {
                return redirect()->route('warehouse.transaksi-offline.create')
                    ->withInput()
                    ->withErrors(['items' => "Stok produk {$product->nama_produk} tidak mencukupi. Stok tersedia: {$product->jumlah}"]);
            }
        }

        $existingTransaksi = null;
        if ($request->tujuan === 'speedshop' && $request->filled('no_transaksi')) {
            $existingTransaksi = TransaksiOffline::where('no_transaksi', trim($request->no_transaksi))
                ->where('tujuan', 'speedshop')
                ->when($warehouseId !== null, fn ($q) => $q->where('warehouse_id', $warehouseId))
                ->first();
        }

        DB::transaction(function () use ($request, $warehouseId, $existingTransaksi) {
            if ($existingTransaksi) {
                $transaksi = $existingTransaksi;
                $noTransaksi = $transaksi->no_transaksi;
                $transaksi->update([
                    'nama_toko' => $request->nama_toko ?: $transaksi->nama_toko,
                    'alamat' => $request->alamat ?: $transaksi->alamat,
                    'no_hp' => $request->no_hp ?: $transaksi->no_hp,
                    'petugas_id' => auth()->id(),
                    'jenis_pembayaran' => $request->jenis_pembayaran ?: $transaksi->jenis_pembayaran,
                ]);
            } else {
                $noTransaksi = $request->no_transaksi && trim($request->no_transaksi)
                    ? trim($request->no_transaksi)
                    : TransaksiOffline::generateNoTransaksi();

                $transaksi = TransaksiOffline::create([
                    'no_transaksi' => $noTransaksi,
                    'tujuan' => $request->tujuan,
                    'nama_toko' => $request->nama_toko,
                    'alamat' => $request->alamat,
                    'no_hp' => $request->no_hp,
                    'petugas_id' => auth()->id(),
                    'jenis_pembayaran' => $request->jenis_pembayaran,
                    'user_id' => auth()->id(),
                    'warehouse_id' => $warehouseId,
                ]);
            }

            foreach ($request->items as $item) {
                TransaksiOfflineItem::create([
                    'transaksi_offline_id' => $transaksi->id,
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                ]);

                $product = Product::findOrFail($item['product_id']);
                $product->decrement('jumlah', $item['qty']);

                StockMovement::create([
                    'product_id' => $product->id,
                    'type' => 'keluar',
                    'qty' => $item['qty'],
                    'keterangan' => "Transaksi Offline - No: {$transaksi->no_transaksi}",
                    'reference' => 'transaksi_offline:' . $transaksi->id,
                ]);
            }
        });

        $msg = $existingTransaksi
            ? 'Item berhasil ditambahkan ke transaksi.'
            : 'Transaksi offline berhasil dicatat. Stok produk telah dikurangi.';

        return redirect()->route('warehouse.transaksi-offline.index')->with('success', $msg);
    }
}
