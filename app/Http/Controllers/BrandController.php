<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        $query = Brand::forUser();

        if ($request->filled('search')) {
            $query->where('nama', 'like', "%{$request->search}%");
        }

        $brands = $query->orderBy('nama')->paginate(20)->withQueryString();

        return view('admin.brands.index', compact('brands'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255', 'unique:brands'],
        ]);

        $validated['warehouse_id'] = auth()->user()->activeWarehouseId();
        Brand::create($validated);
        session()->flash('success', 'Brand berhasil ditambahkan.');

        return response()->json(['success' => true]);
    }

    public function show(Brand $brand)
    {
        return response()->json($brand);
    }

    public function update(Request $request, Brand $brand)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255', 'unique:brands,nama,' . $brand->id],
        ]);

        $brand->update($validated);
        session()->flash('success', 'Brand berhasil diperbarui.');

        return response()->json(['success' => true]);
    }

    public function destroy(Brand $brand)
    {
        $brand->delete();
        session()->flash('success', 'Brand berhasil dihapus.');

        return response()->json(['success' => true]);
    }
}
