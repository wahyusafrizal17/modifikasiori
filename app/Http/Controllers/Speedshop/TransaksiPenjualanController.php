<?php

namespace App\Http\Controllers\Speedshop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\TransaksiPenjualan;
use App\Models\TransaksiPenjualanItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransaksiPenjualanController extends Controller
{
    private function warehouseId(): ?int
    {
        return auth()->user()->activeWarehouseId();
    }

    public function index(Request $request)
    {
        $query = TransaksiPenjualan::with(['items.product', 'user']);

        $warehouseId = $this->warehouseId();
        if ($warehouseId !== null) {
            $query->where('warehouse_id', $warehouseId);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('no_transaksi', 'like', "%{$s}%")
                    ->orWhere('nama_pembeli', 'like', "%{$s}%")
                    ->orWhere('no_hp', 'like', "%{$s}%");
            });
        }

        $transaksis = $query->latest()->paginate(20)->withQueryString();

        return view('speedshop.transaksi-penjualan.index', compact('transaksis'));
    }

    public function create()
    {
        $warehouseId = $this->warehouseId();
        $products = $warehouseId !== null
            ? Product::where('warehouse_id', $warehouseId)->orderBy('nama_produk')->get()
            : Product::orderBy('nama_produk')->get();

        return view('speedshop.transaksi-penjualan.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_pembeli' => ['nullable', 'string', 'max:200'],
            'no_hp' => ['nullable', 'string', 'max:50'],
            'jenis_pembayaran' => ['nullable', 'string', 'max:50'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
        ]);

        $warehouseId = $this->warehouseId();

        foreach ($request->items as $item) {
            $product = Product::findOrFail($item['product_id']);
            if ($product->jumlah < $item['qty']) {
                throw ValidationException::withMessages([
                    'items' => ["Stok produk {$product->nama_produk} tidak mencukupi. Stok tersedia: {$product->jumlah}"],
                ]);
            }
        }

        DB::transaction(function () use ($request, $warehouseId) {
            $transaksi = TransaksiPenjualan::create([
                'no_transaksi' => TransaksiPenjualan::generateNoTransaksi(),
                'nama_pembeli' => $request->nama_pembeli,
                'no_hp' => $request->no_hp,
                'jenis_pembayaran' => $request->jenis_pembayaran,
                'user_id' => auth()->id(),
                'warehouse_id' => $warehouseId,
            ]);

            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $hargaSatuan = $product->harga_jual_speedshop ?? $product->harga_jual ?? 0;

                TransaksiPenjualanItem::create([
                    'transaksi_penjualan_id' => $transaksi->id,
                    'product_id' => $product->id,
                    'qty' => $item['qty'],
                    'harga_satuan' => $hargaSatuan,
                ]);

                $product->decrement('jumlah', $item['qty']);

                StockMovement::create([
                    'product_id' => $product->id,
                    'type' => 'keluar',
                    'qty' => $item['qty'],
                    'keterangan' => "Transaksi Penjualan - No: {$transaksi->no_transaksi}",
                    'reference' => 'transaksi_penjualan:' . $transaksi->id,
                ]);
            }
        });

        return redirect()->route('speedshop.transaksi')
            ->with('success', 'Transaksi penjualan berhasil dicatat. Stok produk telah dikurangi.');
    }

    public function show(TransaksiPenjualan $transaksiPenjualan)
    {
        $warehouseId = $this->warehouseId();
        if ($warehouseId !== null && $transaksiPenjualan->warehouse_id !== null && $transaksiPenjualan->warehouse_id !== $warehouseId) {
            abort(403);
        }

        $transaksiPenjualan->load(['items.product', 'user']);

        return view('speedshop.transaksi-penjualan.show', compact('transaksiPenjualan'));
    }
}
