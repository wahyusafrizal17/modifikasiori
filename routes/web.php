<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\JasaServisController;
use App\Http\Controllers\KendaraanController;
use App\Http\Controllers\KotaController;
use App\Http\Controllers\MekanikController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ServiceOrderController;
use App\Http\Controllers\StockMutationController;
use App\Http\Controllers\StockReceiptController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\WarehouseSwitchController;
use App\Http\Controllers\WipController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::post('/switch-warehouse', [WarehouseSwitchController::class, 'switch'])->name('switch-warehouse');
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // WIP (Produksi Lama)
    Route::get('wip', [WipController::class, 'index'])->name('wip.index');
    Route::post('wip', [WipController::class, 'store'])->name('wip.store');
    Route::patch('wip/{wip}/status', [WipController::class, 'updateStatus'])->name('wip.update-status');
    Route::delete('wip/{wip}', [WipController::class, 'destroy'])->name('wip.destroy');

    // Pembelian (Purchase from Supplier)
    Route::resource('purchases', PurchaseController::class)->except(['edit', 'update']);
    Route::patch('purchases/{purchase}/status', [PurchaseController::class, 'updateStatus'])->name('purchases.update-status');

    // Produksi (Production with QC)
    Route::resource('productions', ProductionController::class)->except(['edit', 'update']);
    Route::patch('productions/{production}/status', [ProductionController::class, 'updateStatus'])->name('productions.update-status');

    // Mutasi Stok (Stock Transfer between Warehouses)
    Route::resource('stock-mutations', StockMutationController::class)->except(['edit', 'update']);
    Route::patch('stock-mutations/{stock_mutation}/status', [StockMutationController::class, 'updateStatus'])->name('stock-mutations.update-status');

    // Terima Stok (Bengkel receives stock via surat jalan code)
    Route::get('stock-receipt', [StockReceiptController::class, 'index'])->name('stock-receipt.index');
    Route::post('stock-receipt/receive', [StockReceiptController::class, 'receive'])->name('stock-receipt.receive');

    // Master Data
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
    Route::resource('users', UserController::class)->except(['create', 'edit']);

    // Bengkel
    Route::resource('pelanggans', PelangganController::class)->except(['create', 'edit']);
    Route::resource('kendaraans', KendaraanController::class)->except(['create', 'edit']);
    Route::resource('mekaniks', MekanikController::class)->except(['create', 'edit']);

    // Service Orders (full pages for create/edit)
    Route::resource('service-orders', ServiceOrderController::class);
    Route::patch('service-orders/{service_order}/status', [ServiceOrderController::class, 'updateStatus'])->name('service-orders.update-status');
    Route::get('api/pelanggans/{pelanggan}/kendaraans', [ServiceOrderController::class, 'kendaraanByPelanggan'])->name('api.pelanggan-kendaraans');

    // Invoices
    Route::get('invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::post('invoices', [InvoiceController::class, 'store'])->name('invoices.store');
    Route::get('invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');

    // Laporan
    Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('laporan/print', [LaporanController::class, 'print'])->name('laporan.print');
    Route::get('laporan/export-csv', [LaporanController::class, 'exportCsv'])->name('laporan.export-csv');
});
