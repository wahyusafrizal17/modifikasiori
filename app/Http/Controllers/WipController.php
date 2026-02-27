<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Wip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WipController extends Controller
{
    public function index(Request $request)
    {
        $query = Wip::with('product');

        if (!auth()->user()->isAdmin()) {
            $query->whereHas('product', fn ($q) => $q->where('warehouse_id', auth()->user()->activeWarehouseId()));
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn ($q) => $q->where('kode_wip', 'like', "%{$s}%")
                ->orWhereHas('product', fn ($q2) => $q2->where('kode_produk', 'like', "%{$s}%")->orWhere('nama_produk', 'like', "%{$s}%")));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $wips = $query->latest()->paginate(20)->withQueryString();
        $products = Product::forUser()->orderBy('nama_produk')->get();

        return view('admin.wip.index', compact('wips', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'qty' => ['required', 'integer', 'min:1'],
            'tanggal_mulai' => ['required', 'date'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);

        $validated['kode_wip'] = Wip::generateKode();
        $validated['status'] = 'proses';

        Wip::create($validated);
        session()->flash('success', 'WIP berhasil ditambahkan. Produk sedang dalam produksi.');

        return response()->json(['success' => true]);
    }

    public function updateStatus(Request $request, Wip $wip)
    {
        $request->validate(['status' => 'required|in:selesai,dibatalkan']);

        if ($wip->status !== 'proses') {
            return response()->json(['error' => 'Hanya WIP dengan status proses yang bisa diubah.'], 422);
        }

        DB::transaction(function () use ($request, $wip) {
            $wip->update([
                'status' => $request->status,
                'tanggal_selesai' => $request->status === 'selesai' ? now()->toDateString() : null,
            ]);

            if ($request->status === 'selesai') {
                Product::where('id', $wip->product_id)->increment('jumlah', $wip->qty);

                StockMovement::create([
                    'product_id' => $wip->product_id,
                    'type' => 'masuk',
                    'qty' => $wip->qty,
                    'keterangan' => 'Produksi selesai (WIP)',
                    'reference' => $wip->kode_wip,
                ]);
            }
        });

        $msg = $request->status === 'selesai'
            ? "Produksi selesai! Stok bertambah {$wip->qty} unit."
            : 'WIP dibatalkan.';

        session()->flash('success', $msg);

        return response()->json(['success' => true]);
    }

    public function destroy(Wip $wip)
    {
        if ($wip->status === 'selesai') {
            return response()->json(['error' => 'WIP yang sudah selesai tidak bisa dihapus.'], 422);
        }

        $wip->delete();
        session()->flash('success', 'WIP berhasil dihapus.');

        return response()->json(['success' => true]);
    }
}
