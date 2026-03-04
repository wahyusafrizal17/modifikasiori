<?php

namespace App\Http\Controllers\Produksi;

use App\Http\Controllers\Controller;
use App\Models\BahanBaku;
use App\Models\BahanSiapProduksi;
use App\Models\Kemasan;
use App\Models\TeamProduksi;
use Illuminate\Http\Request;

class MasterDataController extends Controller
{
    public function bahanBaku(Request $request)
    {
        $query = BahanBaku::with('supplier');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%");
            });
        }

        $items = $query->orderBy('nama')->paginate(20)->withQueryString();
        $totalStok = BahanBaku::sum('stok');

        return view('produksi.master-data.bahan-baku', compact('items', 'totalStok'));
    }

    public function kemasan(Request $request)
    {
        $query = Kemasan::with('supplier');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%");
            });
        }

        $items = $query->orderBy('nama')->paginate(20)->withQueryString();
        $totalStok = Kemasan::sum('stok');

        return view('produksi.master-data.kemasan', compact('items', 'totalStok'));
    }

    public function teamProduksi(Request $request)
    {
        $query = TeamProduksi::query();

        if ($request->filled('search')) {
            $query->where('nama', 'like', "%{$request->search}%");
        }

        $items = $query->orderBy('nama')->paginate(20)->withQueryString();

        return view('produksi.master-data.team-produksi', compact('items'));
    }

    public function bahanSiapProduksi(Request $request)
    {
        $query = BahanSiapProduksi::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%");
            });
        }

        $items = $query->orderBy('nama')->paginate(20)->withQueryString();
        $totalStok = BahanSiapProduksi::sum('stok');

        return view('produksi.master-data.bahan-siap-produksi', compact('items', 'totalStok'));
    }
}
