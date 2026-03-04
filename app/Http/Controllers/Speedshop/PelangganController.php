<?php

namespace App\Http\Controllers\Speedshop;

use App\Http\Controllers\Controller;
use App\Models\Kendaraan;
use App\Models\Pelanggan;
use Illuminate\Http\Request;

class PelangganController extends Controller
{
    public function index(Request $request)
    {
        $query = Pelanggan::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nama', 'like', "%{$s}%")
                    ->orWhere('no_hp', 'like', "%{$s}%")
                    ->orWhere('alamat', 'like', "%{$s}%");
            });
        }

        $pelanggans = $query->orderBy('nama')->paginate(20)->withQueryString();

        return view('speedshop.pelanggan.index', compact('pelanggans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
        ]);

        Pelanggan::create($validated);
        session()->flash('success', 'Pelanggan berhasil ditambahkan.');

        return response()->json(['success' => true]);
    }

    public function show(Pelanggan $pelanggan)
    {
        return response()->json($pelanggan);
    }

    public function update(Request $request, Pelanggan $pelanggan)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
        ]);

        $pelanggan->update($validated);
        session()->flash('success', 'Pelanggan berhasil diperbarui.');

        return response()->json(['success' => true]);
    }

    public function quickAdd(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
            'nomor_polisi' => ['required', 'string', 'max:20'],
            'merk' => ['required', 'string', 'max:100'],
            'tipe' => ['nullable', 'string', 'max:100'],
            'tahun' => ['nullable', 'integer', 'min:1900', 'max:2100'],
        ]);

        $pelanggan = Pelanggan::create([
            'nama' => $validated['nama'],
            'no_hp' => $validated['no_hp'] ?? null,
            'alamat' => $validated['alamat'] ?? null,
        ]);

        $kendaraan = Kendaraan::create([
            'pelanggan_id' => $pelanggan->id,
            'nomor_polisi' => $validated['nomor_polisi'],
            'merk' => $validated['merk'],
            'tipe' => $validated['tipe'] ?? null,
            'tahun' => $validated['tahun'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'pelanggan' => $pelanggan->load('kendaraans'),
        ]);
    }

    public function destroy(Pelanggan $pelanggan)
    {
        $pelanggan->delete();
        session()->flash('success', 'Pelanggan berhasil dihapus.');

        return response()->json(['success' => true]);
    }
}
