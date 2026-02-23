<?php

namespace App\Http\Controllers;

use App\Http\Requests\PelangganRequest;
use App\Models\Kota;
use App\Models\Pelanggan;
use Illuminate\Http\Request;

class PelangganController extends Controller
{
    public function index(Request $request)
    {
        $query = Pelanggan::with('kota')->withCount('kendaraans');
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('nama','like',"%{$s}%")->orWhere('no_hp','like',"%{$s}%"));
        }
        $pelanggans = $query->latest()->paginate(20)->withQueryString();
        $kotas = Kota::orderBy('nama')->get();
        return view('admin.pelanggans.index', compact('pelanggans','kotas'));
    }

    public function store(PelangganRequest $request)
    {
        Pelanggan::create($request->validated());
        session()->flash('success','Pelanggan berhasil ditambahkan.');
        return response()->json(['success'=>true]);
    }

    public function show(Pelanggan $pelanggan)
    {
        // For AJAX edit - return JSON
        if (request()->wantsJson()) {
            return response()->json($pelanggan);
        }
        // For detail page with service history
        $pelanggan->load(['kota','kendaraans','serviceOrders' => fn($q) => $q->with('kendaraan','mekanik','invoice')->latest()]);
        return view('admin.pelanggans.show', compact('pelanggan'));
    }

    public function update(PelangganRequest $request, Pelanggan $pelanggan)
    {
        $pelanggan->update($request->validated());
        session()->flash('success','Pelanggan berhasil diperbarui.');
        return response()->json(['success'=>true]);
    }

    public function destroy(Pelanggan $pelanggan)
    {
        $pelanggan->delete();
        session()->flash('success','Pelanggan berhasil dihapus.');
        return response()->json(['success'=>true]);
    }
}
