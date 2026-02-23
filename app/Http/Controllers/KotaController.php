<?php

namespace App\Http\Controllers;

use App\Models\Kota;
use Illuminate\Http\Request;

class KotaController extends Controller
{
    public function index(Request $request)
    {
        $query = Kota::query();

        if ($request->filled('search')) {
            $query->where('nama', 'like', "%{$request->search}%");
        }

        $kotas = $query->orderBy('nama')->paginate(20)->withQueryString();

        return view('admin.kotas.index', compact('kotas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255', 'unique:kotas'],
        ]);

        Kota::create($validated);
        session()->flash('success', 'Kota berhasil ditambahkan.');

        return response()->json(['success' => true]);
    }

    public function show(Kota $kota)
    {
        return response()->json($kota);
    }

    public function update(Request $request, Kota $kota)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255', 'unique:kotas,nama,' . $kota->id],
        ]);

        $kota->update($validated);
        session()->flash('success', 'Kota berhasil diperbarui.');

        return response()->json(['success' => true]);
    }

    public function destroy(Kota $kota)
    {
        $kota->delete();
        session()->flash('success', 'Kota berhasil dihapus.');

        return response()->json(['success' => true]);
    }
}
