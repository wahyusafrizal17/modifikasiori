<?php

namespace App\Http\Controllers;

use App\Models\Kemasan;
use App\Models\Supplier;
use Illuminate\Http\Request;

class KemasanController extends Controller
{
    public function index(Request $request)
    {
        $query = Kemasan::with('supplier');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%");
            });
        }

        $kemasans = $query->orderBy('nama')->paginate(20)->withQueryString();
        $suppliers = Supplier::orderBy('nama')->get();

        return view('admin.kemasan.index', compact('kemasans', 'suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => ['required', 'string', 'max:50', 'unique:kemasans,kode'],
            'nama' => ['required', 'string', 'max:255'],
            'harga' => ['required', 'numeric', 'min:0'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
        ]);

        $validated['stok'] = 0;
        Kemasan::create($validated);
        session()->flash('success', 'Kemasan berhasil ditambahkan.');

        return response()->json(['success' => true]);
    }

    public function show(Kemasan $kemasan)
    {
        return response()->json($kemasan);
    }

    public function update(Request $request, Kemasan $kemasan)
    {
        $validated = $request->validate([
            'kode' => ['required', 'string', 'max:50', 'unique:kemasans,kode,' . $kemasan->id],
            'nama' => ['required', 'string', 'max:255'],
            'harga' => ['required', 'numeric', 'min:0'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
        ]);

        $kemasan->update($validated);
        session()->flash('success', 'Kemasan berhasil diperbarui.');

        return response()->json(['success' => true]);
    }

    public function destroy(Kemasan $kemasan)
    {
        $kemasan->delete();
        session()->flash('success', 'Kemasan berhasil dihapus.');

        return response()->json(['success' => true]);
    }
}
