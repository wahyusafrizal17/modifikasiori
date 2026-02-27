<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Production;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductionController extends Controller
{
    public function index(Request $request)
    {
        $query = Production::with(['warehouse', 'user']);

        if ($request->filled('search')) {
            $query->where('kode_produksi', 'like', "%{$request->search}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $productions = $query->latest()->paginate(20)->withQueryString();

        return view('admin.productions.index', compact('productions'));
    }

    public function create()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Hanya admin yang dapat membuat produksi.');
        }

        $products = Product::where('warehouse_id', auth()->user()->activeWarehouseId())
            ->orderBy('nama_produk')->get();

        return view('admin.productions.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
            'catatan' => ['nullable', 'string'],
            'materials' => ['required', 'array', 'min:1'],
            'materials.*.product_id' => ['required', 'exists:products,id'],
            'materials.*.qty' => ['required', 'integer', 'min:1'],
            'results' => ['required', 'array', 'min:1'],
            'results.*.product_id' => ['required', 'exists:products,id'],
            'results.*.qty' => ['required', 'integer', 'min:1'],
        ]);

        DB::transaction(function () use ($validated) {
            $production = Production::create([
                'kode_produksi' => Production::generateCode(),
                'warehouse_id' => auth()->user()->activeWarehouseId(),
                'tanggal' => $validated['tanggal'],
                'status' => 'proses',
                'catatan' => $validated['catatan'] ?? null,
                'user_id' => auth()->id(),
            ]);

            foreach ($validated['materials'] as $mat) {
                $production->materials()->create([
                    'product_id' => $mat['product_id'],
                    'qty' => $mat['qty'],
                ]);

                Product::where('id', $mat['product_id'])->decrement('jumlah', $mat['qty']);
                StockMovement::create([
                    'product_id' => $mat['product_id'],
                    'type' => 'keluar',
                    'qty' => $mat['qty'],
                    'keterangan' => 'Bahan baku produksi ' . $production->kode_produksi,
                    'reference' => $production->kode_produksi,
                ]);
            }

            foreach ($validated['results'] as $res) {
                $production->results()->create([
                    'product_id' => $res['product_id'],
                    'qty' => $res['qty'],
                    'qc_status' => 'pending',
                ]);
            }
        });

        session()->flash('success', 'Produksi berhasil dibuat. Bahan baku telah dikurangi.');
        return redirect()->route('admin.productions.index');
    }

    public function show(Production $production)
    {
        $production->load(['warehouse', 'materials.product', 'results.product', 'user']);
        return view('admin.productions.show', compact('production'));
    }

    public function updateStatus(Request $request, Production $production)
    {
        $request->validate(['status' => ['required', 'in:qc,selesai,gagal']]);
        $newStatus = $request->status;

        if ($production->status === 'proses' && $newStatus === 'qc') {
            $production->update(['status' => 'qc']);
            session()->flash('success', 'Produksi masuk tahap QC.');
        } elseif ($production->status === 'qc' && $newStatus === 'selesai') {
            DB::transaction(function () use ($production) {
                foreach ($production->results as $result) {
                    $result->update(['qc_status' => 'passed']);
                    Product::where('id', $result->product_id)->increment('jumlah', $result->qty);
                    StockMovement::create([
                        'product_id' => $result->product_id,
                        'type' => 'masuk',
                        'qty' => $result->qty,
                        'keterangan' => 'Hasil produksi (QC passed) ' . $production->kode_produksi,
                        'reference' => $production->kode_produksi,
                    ]);
                }
                $production->update(['status' => 'selesai']);
            });
            session()->flash('success', 'Produksi selesai, hasil produksi masuk stok.');
        } elseif ($production->status === 'qc' && $newStatus === 'gagal') {
            DB::transaction(function () use ($production, $request) {
                foreach ($production->results as $result) {
                    $result->update([
                        'qc_status' => 'failed',
                        'qc_notes' => $request->qc_notes ?? null,
                    ]);
                }
                $production->update(['status' => 'gagal']);
            });
            session()->flash('success', 'Produksi ditandai gagal QC.');
        }

        return response()->json(['success' => true]);
    }

    public function destroy(Production $production)
    {
        if ($production->status === 'selesai') {
            return response()->json(['error' => 'Tidak dapat menghapus produksi yang sudah selesai.'], 422);
        }

        if ($production->status === 'proses' || $production->status === 'qc') {
            DB::transaction(function () use ($production) {
                foreach ($production->materials as $mat) {
                    Product::where('id', $mat->product_id)->increment('jumlah', $mat->qty);
                    StockMovement::create([
                        'product_id' => $mat->product_id,
                        'type' => 'masuk',
                        'qty' => $mat->qty,
                        'keterangan' => 'Batal produksi ' . $production->kode_produksi,
                        'reference' => $production->kode_produksi,
                    ]);
                }
                $production->delete();
            });
        } else {
            $production->delete();
        }

        session()->flash('success', 'Produksi berhasil dihapus.');
        return response()->json(['success' => true]);
    }
}
