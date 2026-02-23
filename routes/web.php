<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\JasaServisController;
use App\Http\Controllers\KendaraanController;
use App\Http\Controllers\KotaController;
use App\Http\Controllers\MekanikController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ServiceOrderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\WipController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // WIP (Produksi)
    Route::get('wip', [WipController::class, 'index'])->name('wip.index');
    Route::post('wip', [WipController::class, 'store'])->name('wip.store');
    Route::patch('wip/{wip}/status', [WipController::class, 'updateStatus'])->name('wip.update-status');
    Route::delete('wip/{wip}', [WipController::class, 'destroy'])->name('wip.destroy');

    // Master Data
    Route::resource('products', ProductController::class)->except(['create', 'edit']);
    Route::post('products/{product}/stock-in', [ProductController::class, 'stockIn'])->name('products.stock-in');
    Route::get('products/{product}/stock-history', [ProductController::class, 'stockHistory'])->name('products.stock-history');
    Route::resource('jasa-servis', JasaServisController::class)->except(['create', 'edit']);
    Route::resource('categories', CategoryController::class)->except(['create', 'edit']);
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
