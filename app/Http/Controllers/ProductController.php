<?php

namespace App\Http\Controllers;

use App\Exports\ProductsExport;
use App\Exports\ProductsTemplateExport;
use App\Imports\ProductsImport;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand'])->forUser();

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

        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        $products = $query->latest()->paginate(20)->withQueryString();
        $categories = Category::forUser()->orderBy('nama')->get();
        $brands = Brand::forUser()->orderBy('nama')->get();

        return view('admin.products.index', compact('products', 'categories', 'brands'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_produk' => ['required', 'string', 'max:50', 'unique:products'],
            'nama_produk' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'brand_id' => ['nullable', 'exists:brands,id'],
            'harga_pembelian' => ['required', 'numeric', 'min:0'],
            'harga_jual' => ['required', 'numeric', 'min:0'],
        ]);

        $validated['jumlah'] = 0;
        $validated['warehouse_id'] = auth()->user()->activeWarehouseId();
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
            'brand_id' => ['nullable', 'exists:brands,id'],
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

    public function export()
    {
        return Excel::download(new ProductsExport, 'products-' . now()->format('Y-m-d-His') . '.xlsx');
    }

    public function template()
    {
        return Excel::download(new ProductsTemplateExport, 'template-products.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:5120'],
        ]);

        try {
            Excel::import(new ProductsImport, $request->file('file'));
            session()->flash('success', 'Import produk berhasil.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $messages = [];
            foreach ($failures as $failure) {
                $messages[] = 'Baris ' . $failure->row() . ': ' . implode(', ', $failure->errors());
            }
            return back()->with('error', implode(' ', $messages));
        } catch (\Throwable $e) {
            return back()->with('error', 'Import gagal: ' . $e->getMessage());
        }

        return redirect()->route('admin.products.index');
    }
}
