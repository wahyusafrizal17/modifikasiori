<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\JasaServisController;
use App\Http\Controllers\KotaController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Produksi\ProduksiDashboardController;
use App\Http\Controllers\Produksi\MasterDataController;
use App\Http\Controllers\Produksi\MutasiController;
use App\Http\Controllers\Produksi\PackingController;
use App\Http\Controllers\Produksi\ProductionController;
use App\Http\Controllers\Produksi\StockInController;
use App\Http\Controllers\Speedshop\PelangganController;
use App\Http\Controllers\Speedshop\LaporanController;
use App\Http\Controllers\Speedshop\PembelianLuarController;
use App\Http\Controllers\Speedshop\PembelianMOController;
use App\Http\Controllers\Speedshop\SpeedshopDashboardController;
use App\Http\Controllers\Speedshop\TransaksiPenjualanController;
use App\Http\Controllers\Speedshop\WorkOrderController;
use App\Http\Controllers\BahanBakuController;
use App\Http\Controllers\KemasanController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TeamProduksiController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Warehouse\MutasiMasukController;
use App\Http\Controllers\Warehouse\WarehouseDashboardController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\SpeedshopController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Notifications (shared across all sections)
Route::middleware('auth')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{appNotification}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
});

Route::post('/admin/switch-section', function (\Illuminate\Http\Request $request) {
    $section = $request->input('section', 'admin');
    if (!in_array($section, ['admin', 'produksi', 'warehouse', 'speedshop'])) {
        $section = 'admin';
    }

    $warehouseId = $request->input('warehouse_id');
    session(['admin_section' => $section]);

    if (in_array($section, ['warehouse', 'speedshop']) && $warehouseId) {
        session(['admin_warehouse_id' => (int) $warehouseId]);
    } else {
        session()->forget('admin_warehouse_id');
    }

    return redirect()->to(match ($section) {
        'produksi' => route('produksi.dashboard'),
        'warehouse' => route('warehouse.dashboard'),
        'speedshop' => route('speedshop.dashboard'),
        default => route('admin.dashboard'),
    });
})->middleware(['auth', 'role:Admin'])->name('admin.switch-section');

// ============================================================
// PRODUKSI SECTION (Manager & Staf Produksi)
// ============================================================
Route::middleware(['auth', 'section:produksi'])->prefix('produksi')->name('produksi.')->group(function () {
    Route::get('/dashboard', [ProduksiDashboardController::class, 'index'])->name('dashboard');

    Route::get('/stock-in', [StockInController::class, 'index'])->name('stock-in.index');
    Route::get('/stock-in/create', [StockInController::class, 'create'])->name('stock-in.create');
    Route::post('/stock-in', [StockInController::class, 'store'])->name('stock-in.store');
    Route::get('/stock-in/{stockIn}', [StockInController::class, 'show'])->name('stock-in.show');
    Route::get('/stock-in/{stockIn}/pdf', [StockInController::class, 'pdf'])->name('stock-in.pdf');
    Route::post('/stock-in/{stockIn}/approve', [StockInController::class, 'approve'])->name('stock-in.approve');
    Route::post('/stock-in/{stockIn}/reject', [StockInController::class, 'reject'])->name('stock-in.reject');

    Route::get('/production', [ProductionController::class, 'index'])->name('production.index');
    Route::get('/production/create', [ProductionController::class, 'create'])->name('production.create');
    Route::post('/production', [ProductionController::class, 'store'])->name('production.store');
    Route::get('/production/{production}', [ProductionController::class, 'show'])->name('production.show');
    Route::post('/production/{production}/report', [ProductionController::class, 'report'])->name('production.report');
    Route::get('/packing', [PackingController::class, 'index'])->name('packing.index');
    Route::get('/packing/create', [PackingController::class, 'create'])->name('packing.create');
    Route::post('/packing', [PackingController::class, 'store'])->name('packing.store');
    Route::get('/packing/{packing}', [PackingController::class, 'show'])->name('packing.show');
    Route::get('/mutasi', [MutasiController::class, 'index'])->name('mutasi.index');
    Route::get('/mutasi/create', [MutasiController::class, 'create'])->name('mutasi.create');
    Route::post('/mutasi', [MutasiController::class, 'store'])->name('mutasi.store');
    Route::get('/mutasi/{mutasi}', [MutasiController::class, 'show'])->name('mutasi.show');

    Route::get('/master/bahan-baku', [MasterDataController::class, 'bahanBaku'])->name('master.bahan-baku');
    Route::get('/master/kemasan', [MasterDataController::class, 'kemasan'])->name('master.kemasan');
    Route::get('/master/team-produksi', [MasterDataController::class, 'teamProduksi'])->name('master.team-produksi');
    Route::get('/master/bahan-siap-produksi', [MasterDataController::class, 'bahanSiapProduksi'])->name('master.bahan-siap-produksi');
});

// ============================================================
// WAREHOUSE SECTION (Manager & Staf Warehouse)
// ============================================================
Route::middleware(['auth', 'section:warehouse'])->prefix('warehouse')->name('warehouse.')->group(function () {
    Route::get('/dashboard', [WarehouseDashboardController::class, 'index'])->name('dashboard');
    Route::get('/stock-in', fn () => view('warehouse.placeholder', ['title' => 'Stock IN']))->name('stock-in');
    Route::get('/mutasi-masuk', [MutasiMasukController::class, 'index'])->name('mutasi-masuk.index');
    Route::get('/mutasi-masuk/{mutasi}', [MutasiMasukController::class, 'show'])->name('mutasi-masuk.show');
    Route::resource('products', ProductController::class)->except(['create', 'edit']);
    Route::put('products/{product}/update-prices', [ProductController::class, 'updatePrices'])->name('products.update-prices');
    Route::get('products/{product}/stock-history', [ProductController::class, 'stockHistory'])->name('products.stock-history');
    Route::get('/transaksi', [\App\Http\Controllers\Warehouse\TransaksiController::class, 'index'])->name('transaksi.index');
    Route::get('/transaksi/create', [\App\Http\Controllers\Warehouse\TransaksiController::class, 'create'])->name('transaksi.create');
    Route::post('/transaksi', [\App\Http\Controllers\Warehouse\TransaksiController::class, 'store'])->name('transaksi.store');
    Route::get('/transaksi/{warehouse_transaksi}', [\App\Http\Controllers\Warehouse\TransaksiController::class, 'show'])->name('transaksi.show');
    Route::post('/transaksi/{warehouse_transaksi}/approve', [\App\Http\Controllers\Warehouse\TransaksiController::class, 'approve'])->name('transaksi.approve');
    Route::post('/transaksi/{warehouse_transaksi}/reject', [\App\Http\Controllers\Warehouse\TransaksiController::class, 'reject'])->name('transaksi.reject');
    Route::get('/transaksi-speedshop', [MutasiMasukController::class, 'transaksiSpeedshop'])->name('transaksi-speedshop');
    Route::get('/transaksi-speedshop/create', [MutasiMasukController::class, 'createMutasiSpeedshop'])->name('transaksi-speedshop.create');
    Route::post('/transaksi-speedshop', [MutasiMasukController::class, 'storeMutasiSpeedshop'])->name('transaksi-speedshop.store');
    Route::get('/transaksi-speedshop/{mutasi}', [MutasiMasukController::class, 'show'])->name('transaksi-speedshop.show');
    Route::post('/transaksi-speedshop/{mutasi}/verify', [MutasiMasukController::class, 'verify'])->name('transaksi-speedshop.verify');
    Route::post('/mutasi-masuk/{mutasi}/verify', [MutasiMasukController::class, 'verify'])->name('mutasi-masuk.verify');
    Route::get('/transaksi-offline', [\App\Http\Controllers\Warehouse\TransaksiOfflineController::class, 'index'])->name('transaksi-offline.index');
    Route::get('/transaksi-offline/create', [\App\Http\Controllers\Warehouse\TransaksiOfflineController::class, 'create'])->name('transaksi-offline.create');
    Route::get('/transaksi-offline/lookup', [\App\Http\Controllers\Warehouse\TransaksiOfflineController::class, 'lookupByNoTransaksi'])->name('transaksi-offline.lookup');
    Route::post('/transaksi-offline', [\App\Http\Controllers\Warehouse\TransaksiOfflineController::class, 'store'])->name('transaksi-offline.store');
    Route::get('/transaksi-offline/{transaksiOffline}', [\App\Http\Controllers\Warehouse\TransaksiOfflineController::class, 'show'])->name('transaksi-offline.show');
    Route::get('/transaksi-online', [\App\Http\Controllers\Warehouse\TransaksiOnlineController::class, 'index'])->name('transaksi-online.index');
    Route::get('/transaksi-online/create', [\App\Http\Controllers\Warehouse\TransaksiOnlineController::class, 'create'])->name('transaksi-online.create');
    Route::post('/transaksi-online', [\App\Http\Controllers\Warehouse\TransaksiOnlineController::class, 'store'])->name('transaksi-online.store');
    Route::get('/transaksi-online/{transaksiOnline}', [\App\Http\Controllers\Warehouse\TransaksiOnlineController::class, 'show'])->name('transaksi-online.show');
});

// ============================================================
// SPEEDSHOP SECTION (Manager & Staf Speedshop)
// ============================================================
Route::middleware(['auth', 'section:speedshop'])->prefix('speedshop')->name('speedshop.')->group(function () {
    Route::get('/dashboard', [SpeedshopDashboardController::class, 'index'])->name('dashboard');
    Route::post('work-orders/{service_order}/start', [WorkOrderController::class, 'start'])->name('work-orders.start');
    Route::post('work-orders/{service_order}/complete', [WorkOrderController::class, 'complete'])->name('work-orders.complete');
    Route::resource('work-orders', WorkOrderController::class)->only(['create', 'store', 'edit', 'update', 'destroy'])->parameters(['work_order' => 'service_order']);
    Route::post('pelanggans/quick-add', [PelangganController::class, 'quickAdd'])->name('pelanggans.quick-add');
    Route::resource('pelanggans', PelangganController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
    Route::get('/transaksi', [TransaksiPenjualanController::class, 'index'])->name('transaksi');
    Route::get('/transaksi/create', [TransaksiPenjualanController::class, 'create'])->name('transaksi-penjualan.create');
    Route::post('/transaksi', [TransaksiPenjualanController::class, 'store'])->name('transaksi-penjualan.store');
    Route::get('/transaksi/{transaksiPenjualan}', [TransaksiPenjualanController::class, 'show'])->name('transaksi-penjualan.show');
    Route::get('/wip', [WorkOrderController::class, 'index'])->name('wip');
    Route::get('/wip/{service_order}', [WorkOrderController::class, 'show'])->name('wip.show');
    Route::get('/history', [WorkOrderController::class, 'history'])->name('history');
    Route::get('/service-record', [WorkOrderController::class, 'serviceRecord'])->name('service-record');
    Route::get('/estimasi', [WorkOrderController::class, 'estimasi'])->name('estimasi');
    Route::get('/stock-in', fn () => redirect()->route('speedshop.stock-in.pembelian-mo.index'))->name('stock-in');
    Route::get('/stock-in/pembelian-mo', [PembelianMOController::class, 'index'])->name('stock-in.pembelian-mo.index');
    Route::get('/stock-in/pembelian-mo/create', [PembelianMOController::class, 'create'])->name('stock-in.pembelian-mo.create');
    Route::get('/stock-in/pembelian-mo/lookup', [PembelianMOController::class, 'lookup'])->name('stock-in.pembelian-mo.lookup');
    Route::get('/stock-in/pembelian-mo/mutasi/{mutasi}', [PembelianMOController::class, 'showMutasi'])->name('stock-in.pembelian-mo.show-mutasi');
    Route::post('/stock-in/pembelian-mo/mutasi/{mutasi}/verify', [PembelianMOController::class, 'verifyMutasi'])->name('stock-in.pembelian-mo.verify-mutasi');
    Route::get('/stock-in/pembelian-luar', [PembelianLuarController::class, 'index'])->name('stock-in.pembelian-luar.index');
    Route::get('/stock-in/pembelian-luar/create', [PembelianLuarController::class, 'create'])->name('stock-in.pembelian-luar.create');
    Route::post('/stock-in/pembelian-luar', [PembelianLuarController::class, 'store'])->name('stock-in.pembelian-luar.store');
    Route::get('/stock-in/pembelian-luar/{warehouse_transaksi}', [PembelianLuarController::class, 'show'])->name('stock-in.pembelian-luar.show');
    Route::post('/stock-in/pembelian-luar/{warehouse_transaksi}/approve', [PembelianLuarController::class, 'approve'])->name('stock-in.pembelian-luar.approve');
    Route::post('/stock-in/pembelian-luar/{warehouse_transaksi}/reject', [PembelianLuarController::class, 'reject'])->name('stock-in.pembelian-luar.reject');
    Route::resource('products', ProductController::class)->except(['create', 'edit']);
    Route::get('products/{product}/stock-history', [ProductController::class, 'stockHistory'])->name('products.stock-history');
    Route::get('/laporan', fn () => redirect()->route('speedshop.laporan.biaya'))->name('laporan');
    Route::get('/laporan/biaya', [LaporanController::class, 'biaya'])->name('laporan.biaya');
    Route::get('/laporan/biaya/export', [LaporanController::class, 'exportBiaya'])->name('laporan.biaya.export');
    Route::get('/laporan/laba-rugi', [LaporanController::class, 'labaRugi'])->name('laporan.laba-rugi');
    Route::get('/laporan/laba-rugi/export', [LaporanController::class, 'exportLabaRugi'])->name('laporan.laba-rugi.export');
    Route::get('/laporan/summary', [LaporanController::class, 'summary'])->name('laporan.summary');
    Route::get('/laporan/summary/export', [LaporanController::class, 'exportSummary'])->name('laporan.summary.export');
    Route::get('/laporan/penjualan-part-oli', [LaporanController::class, 'penjualanPartOli'])->name('laporan.penjualan-part-oli');
    Route::get('/laporan/penjualan-part-oli/export', [LaporanController::class, 'exportPenjualanPartOli'])->name('laporan.penjualan-part-oli.export');
    Route::get('/laporan/mekanik-performance', [LaporanController::class, 'mekanikPerformance'])->name('laporan.mekanik-performance');
    Route::get('/laporan/mekanik-performance/export', [LaporanController::class, 'exportMekanikPerformance'])->name('laporan.mekanik-performance.export');
});

// ============================================================
// SUPER ADMIN SECTION
// ============================================================
Route::middleware(['auth', 'role:Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Master Data - Existing
    Route::get('products/export', [ProductController::class, 'export'])->name('products.export');
    Route::get('products/template', [ProductController::class, 'template'])->name('products.template');
    Route::post('products/import', [ProductController::class, 'import'])->name('products.import');
    Route::resource('products', ProductController::class)->except(['create', 'edit']);
    Route::get('products/{product}/stock-history', [ProductController::class, 'stockHistory'])->name('products.stock-history');

    Route::get('jasa-servis/export', [JasaServisController::class, 'export'])->name('jasa-servis.export');
    Route::get('jasa-servis/template', [JasaServisController::class, 'template'])->name('jasa-servis.template');
    Route::post('jasa-servis/import', [JasaServisController::class, 'import'])->name('jasa-servis.import');
    Route::resource('jasa-servis', JasaServisController::class)->except(['create', 'edit']);

    Route::resource('categories', CategoryController::class)->except(['create', 'edit']);
    Route::resource('brands', BrandController::class)->except(['create', 'edit']);
    Route::resource('suppliers', SupplierController::class)->except(['create', 'edit']);
    Route::resource('kotas', KotaController::class)->except(['create', 'edit']);
    Route::resource('warehouses', WarehouseController::class)->except(['create', 'edit']);
    Route::resource('speedshops', SpeedshopController::class)->except(['create', 'edit']);
    Route::resource('users', UserController::class)->except(['create', 'edit']);

    Route::resource('team-produksi', TeamProduksiController::class)->except(['create', 'edit']);
    Route::resource('bahan-baku', BahanBakuController::class)->except(['create', 'edit']);
    Route::resource('kemasan', KemasanController::class)->except(['create', 'edit']);
});
