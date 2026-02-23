@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-8">
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.dashboard') }}" class="transition hover:text-gray-700">Home</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="font-medium text-gray-900">Dashboard</span>
    </nav>

    {{-- Bengkel Stats --}}
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Servis Hari Ini</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($servisHariIni) }}</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-500">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Pendapatan Hari Ini</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-50 text-green-500">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Sedang Proses</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($servisDalamProses) }}</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-50 text-amber-500">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Antrian</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($servisAntri) }}</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-red-50 text-red-500">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm lg:col-span-2">
            <h2 class="text-lg font-bold text-gray-900">Stok Produk</h2>
            <p class="mt-1 text-xs text-gray-400">15 produk dengan stok terbanyak</p>
            <div class="mt-6" style="height: 300px;">
                <canvas id="stockByCategoryChart"></canvas>
            </div>
        </div>

        <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold text-gray-900">Stok Terendah</h2>
            <p class="mt-1 text-xs text-gray-400">10 produk dengan stok paling sedikit</p>
            <div class="mt-6" style="height: 300px;">
                <canvas id="topProductsChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Low Stock --}}
    <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-gray-900">Produk Stok Rendah</h2>
                <p class="mt-1 text-xs text-gray-400">Produk dengan stok &le; 5 unit yang perlu restock</p>
            </div>
            <a href="{{ route('admin.products.index') }}" class="text-sm font-medium text-red-500 transition hover:text-red-600">Lihat Semua &rarr;</a>
        </div>
        <div class="mt-6 overflow-x-auto rounded-xl border border-gray-100">
            <table class="w-full text-left text-sm">
                <thead><tr class="bg-gray-50">
                    <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Kode</th>
                    <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Produk</th>
                    <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Kategori</th>
                    <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 text-center">Stok</th>
                    <th class="px-5 py-3.5 text-right text-xs font-bold uppercase tracking-wider text-gray-500">Harga Jual</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($lowStockProducts as $product)
                    <tr class="transition hover:bg-gray-50">
                        <td class="px-5 py-4 font-medium text-gray-900">{{ $product->kode_produk }}</td>
                        <td class="px-5 py-4 text-gray-700">{{ $product->nama_produk }}</td>
                        <td class="px-5 py-4 text-gray-600">{{ $product->category->nama ?? '-' }}</td>
                        <td class="px-5 py-4 text-center"><span class="inline-flex items-center rounded-lg {{ $product->jumlah == 0 ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700' }} px-2.5 py-1 text-xs font-semibold">{{ $product->jumlah }}</span></td>
                        <td class="px-5 py-4 text-right text-gray-700">Rp {{ number_format($product->harga_jual, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-5 py-12 text-center text-gray-400">Semua produk memiliki stok cukup.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const colors = ['#ef4444','#3b82f6','#22c55e','#f59e0b','#8b5cf6','#ec4899','#14b8a6','#f97316','#6366f1','#06b6d4','#84cc16','#e11d48','#0891b2','#a855f7','#10b981'];

    // Stock by Product (vertical)
    const productStockLabels = @json($stockProducts->pluck('kode_produk'));
    const productStockData = @json($stockProducts->pluck('jumlah'));

    new Chart(document.getElementById('stockByCategoryChart'), {
        type: 'bar',
        data: {
            labels: productStockLabels,
            datasets: [{ label: 'Stok', data: productStockData, backgroundColor: colors.slice(0, productStockLabels.length), borderRadius: 6, barPercentage: 0.7 }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true }, x: { ticks: { maxRotation: 45, minRotation: 45, font: { size: 10 } } } } }
    });

    // Low Stock Products
    const productLabels = @json($lowStockProducts->pluck('nama_produk'));
    const productData = @json($lowStockProducts->pluck('jumlah'));

    new Chart(document.getElementById('topProductsChart'), {
        type: 'doughnut',
        data: {
            labels: productLabels,
            datasets: [{ data: productData, backgroundColor: colors.slice(0, productLabels.length), borderWidth: 0 }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, padding: 8, font: { size: 10 } } } } }
    });
});
</script>
@endpush
