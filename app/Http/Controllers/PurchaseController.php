<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $query = Purchase::with(['supplier', 'user']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_pembelian', 'like', "%{$search}%")
                  ->orWhereHas('supplier', fn ($s) => $s->where('nama', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $purchases = $query->latest()->paginate(20)->withQueryString();

        return view('admin.purchases.index', compact('purchases'));
    }

    public function create()
    {
        $suppliers = Supplier::forUser()->orderBy('nama')->get();
        $products = Product::where('warehouse_id', auth()->user()->activeWarehouseId())
            ->orderBy('nama_produk')->get();

        return view('admin.purchases.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'tanggal' => ['required', 'date'],
            'catatan' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'items.*.harga_satuan' => ['required', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($validated) {
            $purchase = Purchase::create([
                'kode_pembelian' => Purchase::generateCode(),
                'supplier_id' => $validated['supplier_id'],
                'warehouse_id' => auth()->user()->activeWarehouseId(),
                'tanggal' => $validated['tanggal'],
                'status' => 'draft',
                'catatan' => $validated['catatan'] ?? null,
                'user_id' => auth()->id(),
                'total' => 0,
            ]);

            $total = 0;
            foreach ($validated['items'] as $item) {
                $subtotal = $item['qty'] * $item['harga_satuan'];
                $purchase->items()->create([
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                    'harga_satuan' => $item['harga_satuan'],
                    'subtotal' => $subtotal,
                ]);
                $total += $subtotal;
            }

            $purchase->update(['total' => $total]);
        });

        session()->flash('success', 'Pembelian berhasil dibuat.');
        return redirect()->route('admin.purchases.index');
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'items.product', 'user']);
        return view('admin.purchases.show', compact('purchase'));
    }

    public function updateStatus(Request $request, Purchase $purchase)
    {
        $request->validate(['status' => ['required', 'in:confirmed,received']]);
        $newStatus = $request->status;

        if ($purchase->status === 'draft' && $newStatus === 'confirmed') {
            $purchase->update(['status' => 'confirmed']);
            session()->flash('success', 'Pembelian dikonfirmasi.');
        } elseif (in_array($purchase->status, ['draft', 'confirmed']) && $newStatus === 'received') {
            DB::transaction(function () use ($purchase) {
                foreach ($purchase->items as $item) {
                    Product::where('id', $item->product_id)->increment('jumlah', $item->qty);
                    StockMovement::create([
                        'product_id' => $item->product_id,
                        'type' => 'masuk',
                        'qty' => $item->qty,
                        'keterangan' => 'Pembelian ' . $purchase->kode_pembelian,
                        'reference' => $purchase->kode_pembelian,
                    ]);
                }
                $purchase->update(['status' => 'received']);
            });
            session()->flash('success', 'Barang diterima, stok diperbarui.');
        }

        return response()->json(['success' => true]);
    }

    public function destroy(Purchase $purchase)
    {
        if ($purchase->status === 'received') {
            return response()->json(['error' => 'Tidak dapat menghapus pembelian yang sudah diterima.'], 422);
        }

        $purchase->delete();
        session()->flash('success', 'Pembelian berhasil dihapus.');

        return response()->json(['success' => true]);
    }
}
