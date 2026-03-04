<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use App\Models\Supplier;
use Illuminate\Http\Request;

class BahanBakuController extends Controller
{
    public function index(Request $request)
    {
        $query = BahanBaku::with('supplier');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%");
            });
        }

        $bahanBakus = $query->orderBy('nama')->paginate(20)->withQueryString();
        $suppliers = Supplier::orderBy('nama')->get();

        return view('admin.bahan-baku.index', compact('bahanBakus', 'suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => ['required', 'string', 'max:50', 'unique:bahan_bakus,kode'],
            'nama' => ['required', 'string', 'max:255'],
            'harga' => ['required', 'numeric', 'min:0'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
        ]);

        $validated['stok'] = 0;
        BahanBaku::create($validated);
        session()->flash('success', 'Bahan Baku berhasil ditambahkan.');

        return response()->json(['success' => true]);
    }

    public function show(BahanBaku $bahanBaku)
    {
        return response()->json($bahanBaku);
    }

    public function update(Request $request, BahanBaku $bahanBaku)
    {
        $validated = $request->validate([
            'kode' => ['required', 'string', 'max:50', 'unique:bahan_bakus,kode,' . $bahanBaku->id],
            'nama' => ['required', 'string', 'max:255'],
            'harga' => ['required', 'numeric', 'min:0'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
        ]);

        $bahanBaku->update($validated);
        session()->flash('success', 'Bahan Baku berhasil diperbarui.');

        return response()->json(['success' => true]);
    }

    public function destroy(BahanBaku $bahanBaku)
    {
        $bahanBaku->delete();
        session()->flash('success', 'Bahan Baku berhasil dihapus.');

        return response()->json(['success' => true]);
    }
}
