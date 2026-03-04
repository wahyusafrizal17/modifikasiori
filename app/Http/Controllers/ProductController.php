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
        $categories = Category::orderBy('nama')->get();
        $brands = Brand::orderBy('nama')->get();

        $section = $this->detectSection();
        $routePrefix = $section . '.products';

        return view("{$section}.products.index", compact('products', 'categories', 'brands', 'routePrefix'));
    }

    private function detectSection(): string
    {
        $name = request()->route()->getName() ?? '';
        if (str_starts_with($name, 'warehouse.')) return 'warehouse';
        if (str_starts_with($name, 'speedshop.')) return 'speedshop';
        return 'admin';
    }

    private function isWarehouseSection(): bool
    {
        return str_starts_with(request()->route()->getName() ?? '', 'warehouse.');
    }

    public function store(Request $request)
    {
        if ($this->isWarehouseSection()) {
            abort(403, 'Warehouse tidak dapat menambah produk. Hanya Admin yang dapat mengelola data produk.');
        }

        $validated = $request->validate([
            'kode_produk' => ['required', 'string', 'max:50', 'unique:products'],
            'nama_produk' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'brand_id' => ['nullable', 'exists:brands,id'],
            'hpp' => ['nullable', 'numeric', 'min:0'],
            'harga_jual_speedshop' => ['nullable', 'numeric', 'min:0'],
            'harga_jual_reseler' => ['nullable', 'numeric', 'min:0'],
            'harga_eceran_terendah' => ['nullable', 'numeric', 'min:0'],
        ]);

        $validated['jumlah'] = 0;
        $validated['hpp'] = (float) ($validated['hpp'] ?? 0);
        $validated['harga_jual_speedshop'] = (float) ($validated['harga_jual_speedshop'] ?? 0);
        $validated['harga_jual_reseler'] = (float) ($validated['harga_jual_reseler'] ?? 0);
        $validated['harga_eceran_terendah'] = (float) ($validated['harga_eceran_terendah'] ?? 0);
        $validated['warehouse_id'] = auth()->user()->activeWarehouseId();
        Product::create($validated);
        session()->flash('success', 'Produk berhasil ditambahkan.');

        return response()->json(['success' => true]);
    }

    public function show(Product $product)
    {
        if ($this->isWarehouseSection()) {
            abort(403, 'Warehouse hanya dapat melihat daftar produk dan stok.');
        }

        return response()->json($product);
    }

    public function update(Request $request, Product $product)
    {
        if ($this->isWarehouseSection()) {
            abort(403, 'Warehouse tidak dapat mengubah produk. Hanya Admin yang dapat mengelola data produk.');
        }

        $validated = $request->validate([
            'kode_produk' => ['required', 'string', 'max:50', 'unique:products,kode_produk,' . $product->id],
            'nama_produk' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'brand_id' => ['nullable', 'exists:brands,id'],
            'hpp' => ['nullable', 'numeric', 'min:0'],
            'harga_jual_speedshop' => ['nullable', 'numeric', 'min:0'],
            'harga_jual_reseler' => ['nullable', 'numeric', 'min:0'],
            'harga_eceran_terendah' => ['nullable', 'numeric', 'min:0'],
        ]);

        $validated['hpp'] = (float) ($validated['hpp'] ?? 0);
        $validated['harga_jual_speedshop'] = (float) ($validated['harga_jual_speedshop'] ?? 0);
        $validated['harga_jual_reseler'] = (float) ($validated['harga_jual_reseler'] ?? 0);
        $validated['harga_eceran_terendah'] = (float) ($validated['harga_eceran_terendah'] ?? 0);
        $product->update($validated);
        session()->flash('success', 'Produk berhasil diperbarui.');

        return response()->json(['success' => true]);
    }

    public function destroy(Product $product)
    {
        if ($this->isWarehouseSection()) {
            abort(403, 'Warehouse tidak dapat menghapus produk. Hanya Admin yang dapat mengelola data produk.');
        }

        $product->delete();
        session()->flash('success', 'Produk berhasil dihapus.');

        return response()->json(['success' => true]);
    }

    public function updatePrices(Request $request, Product $product)
    {
        if (! $this->isWarehouseSection()) {
            abort(403, 'Hanya warehouse yang dapat mengupdate harga produk.');
        }

        $warehouseId = auth()->user()->activeWarehouseId();
        if ($warehouseId !== null && $product->warehouse_id !== $warehouseId) {
            abort(403, 'Anda tidak memiliki akses ke produk ini.');
        }

        $validated = $request->validate([
            'hpp' => ['required', 'numeric', 'min:0'],
            'harga_jual_speedshop' => ['required', 'numeric', 'min:0'],
            'harga_jual_reseler' => ['required', 'numeric', 'min:0'],
            'harga_eceran_terendah' => ['required', 'numeric', 'min:0'],
        ]);

        $product->update($validated);
        session()->flash('success', 'Harga produk berhasil diperbarui.');

        return response()->json(['success' => true]);
    }

    public function stockHistory(Product $product)
    {
        if ($this->isWarehouseSection()) {
            $warehouseId = auth()->user()->activeWarehouseId();
            if ($warehouseId !== null && $product->warehouse_id !== $warehouseId) {
                abort(403, 'Anda tidak memiliki akses ke produk ini.');
            }
        }

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
