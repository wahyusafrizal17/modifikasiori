<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\StockMutation;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockMutationController extends Controller
{
    public function index(Request $request)
    {
        $query = StockMutation::with(['fromWarehouse', 'toWarehouse', 'user']);

        if ($request->filled('search')) {
            $query->where('kode_mutasi', 'like', "%{$request->search}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $mutations = $query->latest()->paginate(20)->withQueryString();

        return view('admin.stock-mutations.index', compact('mutations'));
    }

    public function create()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Hanya admin/warehouse yang dapat membuat mutasi stok.');
        }

        $warehouses = Warehouse::orderBy('nama')->get();
        $products = Product::where('warehouse_id', auth()->user()->activeWarehouseId())
            ->orderBy('nama_produk')->get();

        return view('admin.stock-mutations.create', compact('warehouses', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'from_warehouse_id' => ['required', 'exists:warehouses,id'],
            'to_warehouse_id' => ['required', 'exists:warehouses,id', 'different:from_warehouse_id'],
            'tanggal' => ['required', 'date'],
            'catatan' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
        ]);

        DB::transaction(function () use ($validated) {
            $mutation = StockMutation::create([
                'kode_mutasi' => StockMutation::generateCode(),
                'from_warehouse_id' => $validated['from_warehouse_id'],
                'to_warehouse_id' => $validated['to_warehouse_id'],
                'tanggal' => $validated['tanggal'],
                'status' => 'draft',
                'catatan' => $validated['catatan'] ?? null,
                'user_id' => auth()->id(),
            ]);

            foreach ($validated['items'] as $item) {
                $mutation->items()->create([
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                ]);
            }
        });

        session()->flash('success', 'Mutasi stok berhasil dibuat.');
        return redirect()->route('admin.stock-mutations.index');
    }

    public function show(StockMutation $stockMutation)
    {
        $stockMutation->load(['fromWarehouse', 'toWarehouse', 'items.product', 'user']);
        return view('admin.stock-mutations.show', compact('stockMutation'));
    }

    public function updateStatus(Request $request, StockMutation $stockMutation)
    {
        $request->validate(['status' => ['required', 'in:in_transit,received']]);
        $newStatus = $request->status;

        if ($stockMutation->status === 'draft' && $newStatus === 'in_transit') {
            DB::transaction(function () use ($stockMutation) {
                foreach ($stockMutation->items as $item) {
                    Product::where('id', $item->product_id)->decrement('jumlah', $item->qty);
                    StockMovement::create([
                        'product_id' => $item->product_id,
                        'type' => 'keluar',
                        'qty' => $item->qty,
                        'keterangan' => 'Mutasi keluar ' . $stockMutation->kode_mutasi,
                        'reference' => $stockMutation->kode_mutasi,
                    ]);
                }
                $stockMutation->update(['status' => 'in_transit']);
            });
            session()->flash('success', 'Mutasi dikirim, stok gudang asal dikurangi.');
        } elseif ($stockMutation->status === 'in_transit' && $newStatus === 'received') {
            DB::transaction(function () use ($stockMutation) {
                foreach ($stockMutation->items as $item) {
                    $sourceProduct = Product::findOrFail($item->product_id);

                    $destProduct = Product::firstOrCreate(
                        [
                            'kode_produk' => $sourceProduct->kode_produk,
                            'warehouse_id' => $stockMutation->to_warehouse_id,
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
                        'keterangan' => 'Mutasi masuk ' . $stockMutation->kode_mutasi,
                        'reference' => $stockMutation->kode_mutasi,
                    ]);
                }
                $stockMutation->update(['status' => 'received']);
            });
            session()->flash('success', 'Barang diterima, stok gudang tujuan ditambahkan.');
        }

        return response()->json(['success' => true]);
    }

    public function destroy(StockMutation $stockMutation)
    {
        if ($stockMutation->status !== 'draft') {
            return response()->json(['error' => 'Hanya mutasi berstatus draft yang dapat dihapus.'], 422);
        }

        $stockMutation->delete();
        session()->flash('success', 'Mutasi stok berhasil dihapus.');

        return response()->json(['success' => true]);
    }
}
