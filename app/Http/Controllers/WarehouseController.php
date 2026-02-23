<?php

namespace App\Http\Controllers;

use App\Models\Kota;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index(Request $request)
    {
        $query = Warehouse::with('kota');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        $warehouses = $query->latest()->paginate(20)->withQueryString();
        $kotas = Kota::orderBy('nama')->get();

        return view('admin.warehouses.index', compact('warehouses', 'kotas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
            'kota_id' => ['nullable', 'exists:kotas,id'],
        ]);

        Warehouse::create($validated);
        session()->flash('success', 'Warehouse berhasil ditambahkan.');

        return response()->json(['success' => true]);
    }

    public function show(Warehouse $warehouse)
    {
        return response()->json($warehouse);
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
            'kota_id' => ['nullable', 'exists:kotas,id'],
        ]);

        $warehouse->update($validated);
        session()->flash('success', 'Warehouse berhasil diperbarui.');

        return response()->json(['success' => true]);
    }

    public function destroy(Warehouse $warehouse)
    {
        $warehouse->delete();
        session()->flash('success', 'Warehouse berhasil dihapus.');

        return response()->json(['success' => true]);
    }
}
