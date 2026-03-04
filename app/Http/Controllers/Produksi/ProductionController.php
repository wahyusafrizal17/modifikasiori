<?php

namespace App\Http\Controllers\Produksi;

use App\Http\Controllers\Controller;
use App\Models\BahanBaku;
use App\Models\BahanSiapProduksi;
use App\Models\Production;
use App\Models\TeamProduksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductionController extends Controller
{
    public function index(Request $request)
    {
        $query = Production::with(['teamProduksi', 'user', 'reporter', 'outputs.bahanSiapProduksi']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('kode', 'like', "%{$request->search}%");
        }

        $productions = $query->latest()->paginate(20)->withQueryString();

        $totalAll = Production::count();
        $totalProses = Production::where('status', 'proses')->count();
        $totalSelesai = Production::where('status', 'selesai')->count();

        return view('produksi.production.index', compact('productions', 'totalAll', 'totalProses', 'totalSelesai'));
    }

    public function create()
    {
        $bahanBakus = BahanBaku::where('stok', '>', 0)->orderBy('nama')->get();
        $teams = TeamProduksi::orderBy('nama')->get();
        $bspList = BahanSiapProduksi::orderBy('nama')->get();

        return view('produksi.production.create', compact('bahanBakus', 'teams', 'bspList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'team_produksi_id' => ['required', 'exists:team_produksis,id'],
            'catatan' => ['nullable', 'string', 'max:500'],
            'outputs' => ['required', 'array', 'min:1'],
            'outputs.*.bsp_id' => ['nullable', 'integer'],
            'outputs.*.bsp_nama' => ['nullable', 'string', 'max:255'],
            'outputs.*.jumlah_target' => ['required', 'integer', 'min:1'],
            'outputs.*.items' => ['required', 'array', 'min:1'],
            'outputs.*.items.*.bahan_baku_id' => ['required', 'integer'],
            'outputs.*.items.*.jumlah' => ['required', 'integer', 'min:1'],
        ]);

        foreach ($request->outputs as $output) {
            foreach ($output['items'] as $item) {
                $bb = BahanBaku::find($item['bahan_baku_id']);
                if (!$bb) {
                    return response()->json(['message' => 'Bahan baku tidak ditemukan.'], 422);
                }
                if ($bb->stok < $item['jumlah']) {
                    return response()->json(['message' => "Stok {$bb->nama} tidak mencukupi. Tersedia: {$bb->stok}"], 422);
                }
            }
        }

        DB::transaction(function () use ($request) {
            $production = Production::create([
                'kode' => Production::generateKode(),
                'tanggal' => now()->toDateString(),
                'team_produksi_id' => $request->team_produksi_id,
                'user_id' => auth()->id(),
                'status' => 'proses',
                'catatan' => $request->catatan,
            ]);

            foreach ($request->outputs as $outputData) {
                if (!empty($outputData['bsp_id'])) {
                    $bspId = $outputData['bsp_id'];
                } else {
                    $bsp = BahanSiapProduksi::create([
                        'kode' => BahanSiapProduksi::generateKode(),
                        'nama' => $outputData['bsp_nama'],
                        'stok' => 0,
                    ]);
                    $bspId = $bsp->id;
                }

                $output = $production->outputs()->create([
                    'bahan_siap_produksi_id' => $bspId,
                    'jumlah_target' => $outputData['jumlah_target'],
                ]);

                foreach ($outputData['items'] as $item) {
                    $output->items()->create([
                        'bahan_baku_id' => $item['bahan_baku_id'],
                        'jumlah' => $item['jumlah'],
                    ]);

                    BahanBaku::find($item['bahan_baku_id'])->decrement('stok', $item['jumlah']);
                }
            }
        });

        session()->flash('success', 'Produksi berhasil dibuat. Stok bahan baku telah dikurangi.');
        return response()->json(['success' => true]);
    }

    public function show(Production $production)
    {
        $production->load([
            'teamProduksi', 'user', 'reporter',
            'outputs.bahanSiapProduksi', 'outputs.items.bahanBaku',
        ]);
        return view('produksi.production.show', compact('production'));
    }

    public function report(Request $request, Production $production)
    {
        if (!$production->isProses()) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Produksi sudah dilaporkan.'], 422);
            }
            return back()->with('error', 'Produksi sudah dilaporkan.');
        }

        $request->validate([
            'outputs' => ['required', 'array', 'min:1'],
            'outputs.*.output_id' => ['required', 'exists:production_outputs,id'],
            'outputs.*.jumlah_selesai' => ['required', 'integer', 'min:0'],
            'outputs.*.jumlah_gagal' => ['required', 'integer', 'min:0'],
            'outputs.*.alasan_gagal' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($request, $production) {
            $production->update([
                'status' => 'selesai',
                'reported_by' => auth()->id(),
                'reported_at' => now(),
            ]);

            foreach ($request->outputs as $data) {
                $output = $production->outputs()->findOrFail($data['output_id']);
                $output->update([
                    'jumlah_selesai' => $data['jumlah_selesai'],
                    'jumlah_gagal' => $data['jumlah_gagal'],
                    'alasan_gagal' => $data['alasan_gagal'] ?? null,
                ]);

                if ($data['jumlah_selesai'] > 0) {
                    $output->bahanSiapProduksi->increment('stok', $data['jumlah_selesai']);
                }
            }
        });

        $totalSelesai = collect($request->outputs)->sum('jumlah_selesai');
        $msg = "Laporan produksi berhasil disimpan. {$totalSelesai} unit BSP ditambahkan ke stok.";

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $msg]);
        }
        return redirect()->route('produksi.production.show', $production)->with('success', $msg);
    }
}
