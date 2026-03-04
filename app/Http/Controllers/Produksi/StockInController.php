<?php

namespace App\Http\Controllers\Produksi;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\BahanBaku;
use App\Models\Kemasan;
use App\Models\StockIn;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockInController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = StockIn::with(['user', 'approver', 'items.itemable']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('kode', 'like', "%{$request->search}%");
        }

        $stockIns = $query->latest()->paginate(20)->withQueryString();

        $totalAll = StockIn::count();
        $totalApproved = StockIn::where('status', 'approved')->count();
        $totalRejected = StockIn::where('status', 'rejected')->count();

        return view('produksi.stock-in.index', compact('stockIns', 'totalAll', 'totalApproved', 'totalRejected'));
    }

    public function create()
    {
        $bahanBakus = BahanBaku::orderBy('nama')->get();
        $kemasans = Kemasan::orderBy('nama')->get();

        return view('produksi.stock-in.create', compact('bahanBakus', 'kemasans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'catatan' => ['nullable', 'string', 'max:500'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.type' => ['required', 'in:bahan_baku,kemasan'],
            'items.*.id' => ['required', 'integer'],
            'items.*.jumlah' => ['required', 'integer', 'min:1'],
        ]);

        foreach ($request->items as $item) {
            $model = $item['type'] === 'bahan_baku' ? BahanBaku::class : Kemasan::class;
            if (!$model::find($item['id'])) {
                return response()->json(['message' => 'Item tidak ditemukan.'], 422);
            }
        }

        DB::transaction(function () use ($request) {
            $stockIn = StockIn::create([
                'kode' => StockIn::generateKode(),
                'user_id' => auth()->id(),
                'status' => 'pending',
                'catatan' => $request->catatan,
            ]);

            foreach ($request->items as $item) {
                $stockIn->items()->create([
                    'itemable_type' => $item['type'] === 'bahan_baku'
                        ? BahanBaku::class
                        : Kemasan::class,
                    'itemable_id' => $item['id'],
                    'jumlah' => $item['jumlah'],
                ]);
            }

            $managers = User::where('section', 'produksi')
                ->where('role', 'Manager')
                ->get();

            foreach ($managers as $manager) {
                AppNotification::create([
                    'user_id' => $manager->id,
                    'type' => 'stock_in_pending',
                    'title' => 'Stock IN Baru Menunggu Verifikasi',
                    'message' => auth()->user()->name . ' mengajukan stock in ' . $stockIn->kode,
                    'data' => ['stock_in_id' => $stockIn->id],
                    'link' => route('produksi.stock-in.show', $stockIn),
                ]);
            }
        });

        session()->flash('success', 'Stock IN berhasil diajukan, menunggu verifikasi Manager.');
        return response()->json(['success' => true]);
    }

    public function show(StockIn $stockIn)
    {
        $stockIn->load(['user', 'approver', 'items.itemable']);
        return view('produksi.stock-in.show', compact('stockIn'));
    }

    public function approve(StockIn $stockIn)
    {
        if (!$stockIn->isPending()) {
            if (request()->wantsJson()) {
                return response()->json(['message' => 'Stock IN sudah diproses.'], 422);
            }
            return back()->with('error', 'Stock IN sudah diproses.');
        }

        DB::transaction(function () use ($stockIn) {
            $stockIn->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            foreach ($stockIn->items as $item) {
                $item->itemable->increment('stok', $item->jumlah);
            }

            AppNotification::create([
                'user_id' => $stockIn->user_id,
                'type' => 'stock_in_approved',
                'title' => 'Stock IN Disetujui',
                'message' => auth()->user()->name . ' menyetujui stock in ' . $stockIn->kode,
                'data' => ['stock_in_id' => $stockIn->id],
                'link' => route('produksi.stock-in.show', $stockIn),
            ]);
        });

        $msg = 'Stock IN ' . $stockIn->kode . ' berhasil disetujui. Stok telah diperbarui.';
        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => $msg]);
        }
        return redirect()->route('produksi.stock-in.show', $stockIn)->with('success', $msg);
    }

    public function reject(Request $request, StockIn $stockIn)
    {
        if (!$stockIn->isPending()) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Stock IN sudah diproses.'], 422);
            }
            return back()->with('error', 'Stock IN sudah diproses.');
        }

        $request->validate([
            'rejected_reason' => ['required', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($request, $stockIn) {
            $stockIn->update([
                'status' => 'rejected',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'rejected_reason' => $request->rejected_reason,
            ]);

            AppNotification::create([
                'user_id' => $stockIn->user_id,
                'type' => 'stock_in_rejected',
                'title' => 'Stock IN Ditolak',
                'message' => auth()->user()->name . ' menolak stock in ' . $stockIn->kode . ': ' . $request->rejected_reason,
                'data' => ['stock_in_id' => $stockIn->id],
                'link' => route('produksi.stock-in.show', $stockIn),
            ]);
        });

        $msg = 'Stock IN ' . $stockIn->kode . ' telah ditolak.';
        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $msg]);
        }
        return redirect()->route('produksi.stock-in.show', $stockIn)->with('success', $msg);
    }

    public function pdf(StockIn $stockIn)
    {
        $stockIn->load(['user', 'approver', 'items.itemable']);

        return view('produksi.stock-in.pdf', compact('stockIn'));
    }
}
