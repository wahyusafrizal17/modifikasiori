@extends('layouts.speedshop')

@section('title', 'Laporan Summary')

@section('content')
<div class="space-y-8">
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('speedshop.dashboard') }}" class="transition hover:text-gray-700">Home</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="font-medium text-gray-900">Laporan Summary</span>
    </nav>

    <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm sm:p-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">Laporan Summary</h1>
                <p class="mt-1 text-sm text-gray-500">Periode {{ $dari->format('d M Y') }} - {{ $sampai->format('d M Y') }}</p>
            </div>
            @include('partials.speedshop.laporan-filter', ['exportRoute' => 'speedshop.laporan.summary.export', 'dari' => $dari, 'sampai' => $sampai])
        </div>

        <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
            <div class="rounded-xl border border-gray-100 bg-blue-50 p-6">
                <p class="text-xs font-semibold uppercase text-blue-600">Total Service Order</p>
                <p class="mt-2 text-3xl font-bold text-blue-900">{{ number_format($totalServiceOrder) }}</p>
            </div>
            <div class="rounded-xl border border-gray-100 bg-emerald-50 p-6">
                <p class="text-xs font-semibold uppercase text-emerald-600">Total Transaksi Penjualan</p>
                <p class="mt-2 text-3xl font-bold text-emerald-900">{{ number_format($totalTransaksiPenjualan) }}</p>
            </div>
            <div class="rounded-xl border border-gray-100 bg-amber-50 p-6">
                <p class="text-xs font-semibold uppercase text-amber-600">Pendapatan Invoice</p>
                <p class="mt-2 text-xl font-bold text-amber-900">Rp {{ number_format($pendapatanInvoice, 0, ',', '.') }}</p>
            </div>
            <div class="rounded-xl border border-gray-100 bg-purple-50 p-6">
                <p class="text-xs font-semibold uppercase text-purple-600">Pendapatan Penjualan</p>
                <p class="mt-2 text-xl font-bold text-purple-900">Rp {{ number_format($pendapatanPenjualan, 0, ',', '.') }}</p>
            </div>
            <div class="rounded-xl border border-gray-100 bg-red-50 p-6">
                <p class="text-xs font-semibold uppercase text-red-600">Total Pendapatan</p>
                <p class="mt-2 text-xl font-bold text-red-900">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
