<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category')->forUser();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_produk', 'like', "%{$search}%")
                  ->orWhere('nama_produk', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->latest()->paginate(20)->withQueryString();
        $categories = Category::forUser()->orderBy('nama')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_produk' => ['required', 'string', 'max:50', 'unique:products'],
            'nama_produk' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'harga_pembelian' => ['required', 'numeric', 'min:0'],
            'harga_jual' => ['required', 'numeric', 'min:0'],
        ]);

        $validated['jumlah'] = 0;
        $validated['warehouse_id'] = auth()->user()->warehouse_id;
        Product::create($validated);
        session()->flash('success', 'Produk berhasil ditambahkan.');

        return response()->json(['success' => true]);
    }

    public function show(Product $product)
    {
        return response()->json($product);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'kode_produk' => ['required', 'string', 'max:50', 'unique:products,kode_produk,' . $product->id],
            'nama_produk' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'harga_pembelian' => ['required', 'numeric', 'min:0'],
            'harga_jual' => ['required', 'numeric', 'min:0'],
        ]);

        $product->update($validated);
        session()->flash('success', 'Produk berhasil diperbarui.');

        return response()->json(['success' => true]);
    }

    public function destroy(Product $product)
    {
        $product->delete();
        session()->flash('success', 'Produk berhasil dihapus.');

        return response()->json(['success' => true]);
    }

    public function stockIn(Request $request, Product $product)
    {
        $validated = $request->validate([
            'qty' => ['required', 'integer', 'min:1'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($validated, $product) {
            $product->increment('jumlah', $validated['qty']);

            StockMovement::create([
                'product_id' => $product->id,
                'type' => 'masuk',
                'qty' => $validated['qty'],
                'keterangan' => $validated['keterangan'] ?? 'Stok masuk manual',
                'reference' => null,
            ]);
        });

        session()->flash('success', "Stok masuk {$validated['qty']} unit berhasil.");

        return response()->json(['success' => true]);
    }

    public function stockHistory(Product $product)
    {
        $movements = $product->stockMovements()
            ->latest()
            ->limit(50)
            ->get();

        return response()->json([
            'product' => $product->only(['id', 'kode_produk', 'nama_produk', 'jumlah']),
            'movements' => $movements,
        ]);
    }
}
