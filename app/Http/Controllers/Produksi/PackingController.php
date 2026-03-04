<?php

namespace App\Http\Controllers\Produksi;

use App\Http\Controllers\Controller;
use App\Models\BahanSiapProduksi;
use App\Models\Kemasan;
use App\Models\Packing;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PackingController extends Controller
{
    public function index(Request $request)
    {
        $query = Packing::with(['user', 'details.product']);

        if ($request->filled('search')) {
            $query->where('kode', 'like', "%{$request->search}%");
        }

        $packings = $query->latest()->paginate(20)->withQueryString();
        $totalBsp = BahanSiapProduksi::where('stok', '>', 0)->sum('stok');
        $totalKemasan = Kemasan::where('stok', '>', 0)->sum('stok');
        $totalPacking = Packing::count();
        $totalUnit = DB::table('packing_details')->sum('quantity');

        return view('produksi.packing.index', compact('packings', 'totalBsp', 'totalKemasan', 'totalPacking', 'totalUnit'));
    }

    public function create()
    {
        $bspList = BahanSiapProduksi::where('stok', '>', 0)->orderBy('nama')->get();
        $kemasans = Kemasan::where('stok', '>', 0)->orderBy('nama')->get();
        $products = Product::whereIn('id', function ($q) {
            $q->selectRaw('MIN(id)')->from('products')->groupBy('kode_produk');
        })->orderBy('nama_produk')->get();

        return view('produksi.packing.create', compact('bspList', 'kemasans', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'catatan' => ['nullable', 'string', 'max:500'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.bsp' => ['nullable', 'array'],
            'items.*.bsp.*.id' => ['required', 'exists:bahan_siap_produksis,id'],
            'items.*.bsp.*.qty' => ['required', 'integer', 'min:1'],
            'items.*.kemasan' => ['nullable', 'array'],
            'items.*.kemasan.*.id' => ['required', 'exists:kemasans,id'],
            'items.*.kemasan.*.qty' => ['required', 'integer', 'min:1'],
        ]);

        $items = $request->input('items', []);

        foreach ($items as $idx => $item) {
            $bspItems = collect($item['bsp'] ?? [])->filter(fn ($b) => ! empty($b['id']) && (int) ($b['qty'] ?? 0) > 0);
            foreach ($bspItems as $b) {
                $bsp = BahanSiapProduksi::find($b['id']);
                if (! $bsp || $bsp->stok < (int) $b['qty']) {
                    return response()->json(['message' => 'Stok BSP ' . ($bsp->kode ?? '') . ' tidak mencukupi (item #' . ($idx + 1) . ').'], 422);
                }
            }
            $kemasanItems = collect($item['kemasan'] ?? [])->filter(fn ($k) => ! empty($k['id']) && (int) ($k['qty'] ?? 0) > 0);
            foreach ($kemasanItems as $k) {
                $kemasan = Kemasan::find($k['id']);
                if (! $kemasan || $kemasan->stok < (int) $k['qty']) {
                    return response()->json(['message' => 'Stok kemasan ' . ($kemasan->nama ?? '') . ' tidak mencukupi (item #' . ($idx + 1) . ').'], 422);
                }
            }
        }

        DB::transaction(function () use ($request, $items) {
            $packing = Packing::create([
                'kode' => Packing::generateKode(),
                'tanggal' => now()->toDateString(),
                'user_id' => auth()->id(),
                'catatan' => $request->catatan,
            ]);

            foreach ($items as $item) {
                $bspItems = collect($item['bsp'] ?? [])->filter(fn ($b) => ! empty($b['id']) && (int) ($b['qty'] ?? 0) > 0);
                $kemasanItems = collect($item['kemasan'] ?? [])->filter(fn ($k) => ! empty($k['id']) && (int) ($k['qty'] ?? 0) > 0);

                $detail = $packing->details()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => (int) $item['quantity'],
                ]);

                foreach ($bspItems as $b) {
                    $detail->items()->create([
                        'packing_id' => $packing->id,
                        'type' => 'bahan_siap_produksi',
                        'bahan_siap_produksi_id' => $b['id'],
                        'kemasan_id' => null,
                        'quantity' => (int) $b['qty'],
                    ]);
                    BahanSiapProduksi::find($b['id'])->decrement('stok', (int) $b['qty']);
                }

                foreach ($kemasanItems as $k) {
                    $detail->items()->create([
                        'packing_id' => $packing->id,
                        'type' => 'kemasan',
                        'kemasan_id' => $k['id'],
                        'quantity' => (int) $k['qty'],
                    ]);
                    Kemasan::find($k['id'])->decrement('stok', (int) $k['qty']);
                }
            }
        });

        $msg = 'Packing berhasil disimpan. Produk belum masuk stok.';

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $msg, 'redirect' => route('produksi.packing.index')]);
        }

        return redirect()->route('produksi.packing.index')->with('success', $msg);
    }

    public function show(Packing $packing)
    {
        $packing->load(['user', 'details.product', 'details.items.kemasan', 'details.items.bahanSiapProduksi']);
        return view('produksi.packing.show', compact('packing'));
    }
}
