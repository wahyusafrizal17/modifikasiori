<?php

namespace App\Http\Controllers;

use App\Models\JasaServis;
use Illuminate\Http\Request;

class JasaServisController extends Controller
{
    public function index(Request $request)
    {
        $query = JasaServis::forUser();

        if ($request->filled('search')) {
            $query->where('nama', 'like', "%{$request->search}%");
        }

        $jasaServis = $query->orderBy('nama')->paginate(20)->withQueryString();

        return view('admin.jasa-servis.index', compact('jasaServis'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'biaya' => ['required', 'numeric', 'min:0'],
        ]);

        $validated['warehouse_id'] = auth()->user()->warehouse_id;
        JasaServis::create($validated);
        session()->flash('success', 'Jasa servis berhasil ditambahkan.');

        return response()->json(['success' => true]);
    }

    public function show(JasaServis $jasaServi)
    {
        return response()->json($jasaServi);
    }

    public function update(Request $request, JasaServis $jasaServi)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'biaya' => ['required', 'numeric', 'min:0'],
        ]);

        $jasaServi->update($validated);
        session()->flash('success', 'Jasa servis berhasil diperbarui.');

        return response()->json(['success' => true]);
    }

    public function destroy(JasaServis $jasaServi)
    {
        $jasaServi->delete();
        session()->flash('success', 'Jasa servis berhasil dihapus.');

        return response()->json(['success' => true]);
    }
}
