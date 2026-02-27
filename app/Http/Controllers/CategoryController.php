<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::forUser();

        if ($request->filled('search')) {
            $query->where('nama', 'like', "%{$request->search}%");
        }

        $categories = $query->orderBy('nama')->paginate(20)->withQueryString();

        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255', 'unique:categories'],
        ]);

        $validated['warehouse_id'] = auth()->user()->activeWarehouseId();
        Category::create($validated);
        session()->flash('success', 'Kategori berhasil ditambahkan.');

        return response()->json(['success' => true]);
    }

    public function show(Category $category)
    {
        return response()->json($category);
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255', 'unique:categories,nama,' . $category->id],
        ]);

        $category->update($validated);
        session()->flash('success', 'Kategori berhasil diperbarui.');

        return response()->json(['success' => true]);
    }

    public function destroy(Category $category)
    {
        $category->delete();
        session()->flash('success', 'Kategori berhasil dihapus.');

        return response()->json(['success' => true]);
    }
}
