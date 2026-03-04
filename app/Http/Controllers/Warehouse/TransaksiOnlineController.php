<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\TransaksiOnline;
use App\Models\TransaksiOnlineItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransaksiOnlineController extends Controller
{
    public function index(Request $request)
    {
        $query = TransaksiOnline::with(['items.product', 'user']);

        $warehouseId = auth()->user()->activeWarehouseId();
        if ($warehouseId !== null) {
            $query->where('warehouse_id', $warehouseId);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('no_resi', 'like', "%{$request->search}%")
                    ->orWhereHas('items.product', fn ($p) => $p->where('kode_produk', 'like', "%{$request->search}%")
                        ->orWhere('nama_produk', 'like', "%{$request->search}%"));
            });
        }

        $transaksis = $query->latest()->paginate(20)->withQueryString();

        return view('warehouse.transaksi-online.index', compact('transaksis'));
    }

    public function show(TransaksiOnline $transaksiOnline)
    {
        $this->authorizeOrWarehouse($transaksiOnline);

        $transaksiOnline->load(['items.product', 'user', 'warehouse']);

        return view('warehouse.transaksi-online.show', compact('transaksiOnline'));
    }

    private function authorizeOrWarehouse(TransaksiOnline $transaksiOnline): void
    {
        $warehouseId = auth()->user()->activeWarehouseId();
        if ($warehouseId !== null && $transaksiOnline->warehouse_id != $warehouseId) {
            abort(404);
        }
    }

    public function create()
    {
        $warehouseId = auth()->user()->activeWarehouseId();
        $products = $warehouseId !== null
            ? Product::where('warehouse_id', $warehouseId)->orderBy('nama_produk')->get()
            : Product::orderBy('nama_produk')->get();

        return view('warehouse.transaksi-online.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'no_resi' => ['required', 'string', 'max:100'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
        ]);

        if ($validator->fails()) {
            return redirect()->route('warehouse.transaksi-online.create')
                ->withInput()
                ->withErrors($validator);
        }

        foreach ($request->items as $item) {
            $product = Product::findOrFail($item['product_id']);
            if ($product->jumlah < $item['qty']) {
                return redirect()->route('warehouse.transaksi-online.create')
                    ->withInput()
                    ->withErrors(['items' => "Stok produk {$product->nama_produk} tidak mencukupi. Stok tersedia: {$product->jumlah}"]);
            }
        }

        DB::transaction(function () use ($request) {
            $warehouseId = auth()->user()->activeWarehouseId();

            $transaksi = TransaksiOnline::create([
                'no_resi' => $request->no_resi,
                'user_id' => auth()->id(),
                'warehouse_id' => $warehouseId,
            ]);

            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $qty = (int) $item['qty'];

                TransaksiOnlineItem::create([
                    'transaksi_online_id' => $transaksi->id,
                    'product_id' => $product->id,
                    'qty' => $qty,
                ]);

                $product->decrement('jumlah', $qty);

                StockMovement::create([
                    'product_id' => $product->id,
                    'type' => 'keluar',
                    'qty' => $qty,
                    'keterangan' => "Transaksi Online - No RESI: {$transaksi->no_resi}",
                    'reference' => 'transaksi_online:' . $transaksi->id,
                ]);
            }
        });

        return redirect()->route('warehouse.transaksi-online.index')
            ->with('success', 'Transaksi online berhasil dicatat. Stok produk telah dikurangi.');
    }
}
