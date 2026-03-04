<?php

namespace App\Http\Controllers;

use App\Models\TeamProduksi;
use Illuminate\Http\Request;

class TeamProduksiController extends Controller
{
    public function index(Request $request)
    {
        $query = TeamProduksi::query();

        if ($request->filled('search')) {
            $query->where('nama', 'like', "%{$request->search}%");
        }

        $teams = $query->orderBy('nama')->paginate(20)->withQueryString();

        return view('admin.team-produksi.index', compact('teams'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
        ]);

        TeamProduksi::create($validated);
        session()->flash('success', 'Team Produksi berhasil ditambahkan.');

        return response()->json(['success' => true]);
    }

    public function show(TeamProduksi $teamProduksi)
    {
        return response()->json($teamProduksi);
    }

    public function update(Request $request, TeamProduksi $teamProduksi)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
        ]);

        $teamProduksi->update($validated);
        session()->flash('success', 'Team Produksi berhasil diperbarui.');

        return response()->json(['success' => true]);
    }

    public function destroy(TeamProduksi $teamProduksi)
    {
        $teamProduksi->delete();
        session()->flash('success', 'Team Produksi berhasil dihapus.');

        return response()->json(['success' => true]);
    }
}
