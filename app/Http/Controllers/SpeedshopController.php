<?php

namespace App\Http\Controllers;

use App\Models\Kota;
use App\Models\Speedshop;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class SpeedshopController extends Controller
{
    public function index(Request $request)
    {
        $query = Speedshop::with(['kota', 'warehouse']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        $speedshops = $query->latest()->paginate(20)->withQueryString();
        $kotas = Kota::orderBy('nama')->get();
        $warehouses = Warehouse::orderBy('nama')->get();

        return view('admin.speedshops.index', compact('speedshops', 'kotas', 'warehouses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
            'kota_id' => ['nullable', 'exists:kotas,id'],
            'warehouse_id' => ['nullable', 'exists:warehouses,id'],
        ]);

        Speedshop::create($validated);
        session()->flash('success', 'Speedshop berhasil ditambahkan.');

        return response()->json(['success' => true]);
    }

    public function show(Speedshop $speedshop)
    {
        return response()->json($speedshop->load('warehouse', 'kota'));
    }

    public function update(Request $request, Speedshop $speedshop)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
            'kota_id' => ['nullable', 'exists:kotas,id'],
            'warehouse_id' => ['nullable', 'exists:warehouses,id'],
        ]);

        $speedshop->update($validated);
        session()->flash('success', 'Speedshop berhasil diperbarui.');

        return response()->json(['success' => true]);
    }

    public function destroy(Speedshop $speedshop)
    {
        if ($speedshop->users()->exists()) {
            session()->flash('error', 'Speedshop tidak dapat dihapus karena masih memiliki user.');

            return response()->json(['success' => false, 'message' => 'Speedshop masih memiliki user.'], 422);
        }

        $speedshop->delete();
        session()->flash('success', 'Speedshop berhasil dihapus.');

        return response()->json(['success' => true]);
    }
}
