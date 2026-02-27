<?php

namespace App\Http\Controllers;

use App\Models\Kota;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::with('kota')->forUser();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%");
            });
        }

        $suppliers = $query->orderBy('nama')->paginate(20)->withQueryString();
        $kotas = Kota::orderBy('nama')->get();

        return view('admin.suppliers.index', compact('suppliers', 'kotas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
            'kota_id' => ['nullable', 'exists:kotas,id'],
        ]);

        $validated['warehouse_id'] = auth()->user()->activeWarehouseId();
        Supplier::create($validated);
        session()->flash('success', 'Supplier berhasil ditambahkan.');

        return response()->json(['success' => true]);
    }

    public function show(Supplier $supplier)
    {
        return response()->json($supplier);
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
            'kota_id' => ['nullable', 'exists:kotas,id'],
        ]);

        $supplier->update($validated);
        session()->flash('success', 'Supplier berhasil diperbarui.');

        return response()->json(['success' => true]);
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        session()->flash('success', 'Supplier berhasil dihapus.');

        return response()->json(['success' => true]);
    }
}
