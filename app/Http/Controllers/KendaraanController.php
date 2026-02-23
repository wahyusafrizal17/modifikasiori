<?php

namespace App\Http\Controllers;

use App\Http\Requests\KendaraanRequest;
use App\Models\Kendaraan;
use App\Models\Pelanggan;
use Illuminate\Http\Request;

class KendaraanController extends Controller
{
    public function index(Request $request)
    {
        $query = Kendaraan::with('pelanggan');
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('nomor_polisi','like',"%{$s}%")->orWhere('merk','like',"%{$s}%")->orWhere('tipe','like',"%{$s}%"));
        }
        $kendaraans = $query->latest()->paginate(20)->withQueryString();
        $pelanggans = Pelanggan::orderBy('nama')->get();
        return view('admin.kendaraans.index', compact('kendaraans','pelanggans'));
    }

    public function store(KendaraanRequest $request)
    {
        Kendaraan::create($request->validated());
        session()->flash('success','Kendaraan berhasil ditambahkan.');
        return response()->json(['success'=>true]);
    }

    public function show(Kendaraan $kendaraan)
    {
        return response()->json($kendaraan);
    }

    public function update(KendaraanRequest $request, Kendaraan $kendaraan)
    {
        $kendaraan->update($request->validated());
        session()->flash('success','Kendaraan berhasil diperbarui.');
        return response()->json(['success'=>true]);
    }

    public function destroy(Kendaraan $kendaraan)
    {
        $kendaraan->delete();
        session()->flash('success','Kendaraan berhasil dihapus.');
        return response()->json(['success'=>true]);
    }
}
