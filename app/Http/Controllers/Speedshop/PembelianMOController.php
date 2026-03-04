<?php

namespace App\Http\Controllers\Speedshop;

use App\Http\Controllers\Controller;
use App\Models\Mutasi;
use App\Models\Product;
use App\Models\TransaksiOffline;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PembelianMOController extends Controller
{
    private function warehouseId(): ?int
    {
        return auth()->user()->activeWarehouseId();
    }

    public function index(Request $request)
    {
        $warehouseId = $this->warehouseId();
        if (!$warehouseId) {
            $mutasis = Mutasi::query()->whereRaw('1=0')->paginate(20)->withQueryString();
            return view('speedshop.stock-in.pembelian-mo.index', [
                'mutasis' => $mutasis,
                'config' => $this->getConfig(),
            ]);
        }

        $query = Mutasi::with(['user', 'warehouse', 'items.product'])
            ->where('warehouse_id', $warehouseId);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                    ->orWhere('nomor_surat_jalan', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if (in_array($request->status, ['dikirim', 'diterima'])) {
                $query->where('status', $request->status);
            }
        }

        $mutasis = $query->latest()->paginate(20)->withQueryString();

        return view('speedshop.stock-in.pembelian-mo.index', [
            'mutasis' => $mutasis,
            'config' => $this->getConfig(),
        ]);
    }

    public function create()
    {
        return view('speedshop.stock-in.pembelian-mo.create');
    }

    public function lookup(Request $request)
    {
        $no = trim($request->get('no') ?? '');
        if (!$no) {
            return response()->json(['found' => false]);
        }

        $warehouseId = $this->warehouseId();

        $mutasi = Mutasi::with(['items.product'])
            ->where('warehouse_id', $warehouseId)
            ->where('status', 'dikirim')
            ->where(function ($q) use ($no) {
                $q->where('nomor_surat_jalan', 'like', "%{$no}%")
                    ->orWhere('kode', 'like', "%{$no}%");
            })
            ->first();

        if ($mutasi) {
            return response()->json([
                'found' => true,
                'type' => 'mutasi',
                'data' => [
                    'id' => $mutasi->id,
                    'kode' => $mutasi->kode,
                    'nomor_surat_jalan' => $mutasi->nomor_surat_jalan,
                    'tanggal' => $mutasi->tanggal?->format('d M Y'),
                    'items' => $mutasi->items->map(fn ($i) => [
                        'product_id' => $i->product_id,
                        'kode_produk' => $i->product->kode_produk ?? '-',
                        'nama_produk' => $i->product->nama_produk ?? '-',
                        'qty' => $i->quantity,
                    ]),
                ],
            ]);
        }

        $transaksi = TransaksiOffline::with(['items.product'])
            ->where('tujuan', 'speedshop')
            ->where('no_transaksi', $no)
            ->first();

        if ($transaksi) {
            return response()->json([
                'found' => true,
                'type' => 'transaksi_offline',
                'data' => [
                    'id' => $transaksi->id,
                    'no_transaksi' => $transaksi->no_transaksi,
                    'nama_toko' => $transaksi->nama_toko,
                    'items' => $transaksi->items->map(fn ($i) => [
                        'product_id' => $i->product_id,
                        'kode_produk' => $i->product->kode_produk ?? '-',
                        'nama_produk' => $i->product->nama_produk ?? '-',
                        'qty' => $i->qty,
                    ]),
                ],
            ]);
        }

        return response()->json(['found' => false]);
    }

    public function showMutasi(Mutasi $mutasi)
    {
        $warehouseId = $this->warehouseId();
        if ($warehouseId !== null && $mutasi->warehouse_id !== $warehouseId) {
            abort(403);
        }

        $mutasi->load(['user', 'warehouse', 'items.product']);
        return view('speedshop.stock-in.pembelian-mo.show-mutasi', compact('mutasi'));
    }

    public function verifyMutasi(Mutasi $mutasi)
    {
        if (!$mutasi->isDikirim()) {
            return response()->json(['message' => 'Mutasi sudah diverifikasi sebelumnya.'], 422);
        }

        $warehouseId = $this->warehouseId();
        if ($warehouseId !== null && $mutasi->warehouse_id !== $warehouseId) {
            abort(403);
        }

        if (!auth()->user()->isManager() && !auth()->user()->isAdmin()) {
            abort(403, 'Hanya Manager Speedshop atau Admin yang dapat memverifikasi.');
        }

        DB::transaction(function () use ($mutasi) {
            $mutasi->update(['status' => 'diterima']);
            $tujuanWarehouseId = $mutasi->warehouse_id;

            foreach ($mutasi->items as $item) {
                $sourceProduct = $item->product;
                $productTujuan = Product::where('warehouse_id', $tujuanWarehouseId)
                    ->where('kode_produk', $sourceProduct->kode_produk)
                    ->first();

                if ($productTujuan) {
                    $productTujuan->increment('jumlah', $item->quantity);
                } else {
                    Product::create([
                        'kode_produk' => $sourceProduct->kode_produk,
                        'nama_produk' => $sourceProduct->nama_produk,
                        'category_id' => $sourceProduct->category_id,
                        'brand_id' => $sourceProduct->brand_id,
                        'warehouse_id' => $tujuanWarehouseId,
                        'jumlah' => $item->quantity,
                        'harga_pembelian' => $sourceProduct->harga_pembelian ?? 0,
                        'harga_jual' => $sourceProduct->harga_jual ?? 0,
                        'hpp' => $sourceProduct->hpp ?? 0,
                        'harga_jual_speedshop' => $sourceProduct->harga_jual_speedshop ?? 0,
                        'harga_jual_reseler' => $sourceProduct->harga_jual_reseler ?? 0,
                        'harga_eceran_terendah' => $sourceProduct->harga_eceran_terendah ?? 0,
                    ]);
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Mutasi berhasil diverifikasi. Stok produk telah ditambahkan.',
        ]);
    }

    protected function getConfig(): array
    {
        return [
            'pageTitle' => 'Pembelian MO',
            'breadcrumb' => 'Pembelian MO',
            'indexRoute' => 'speedshop.stock-in.pembelian-mo.index',
            'description' => 'Cari dan lihat mutasi yang dikirim ke Speedshop Anda berdasarkan No. Surat Jalan',
            'searchPlaceholder' => 'Cari No. Surat Jalan...',
        ];
    }
}
