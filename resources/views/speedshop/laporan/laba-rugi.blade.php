@extends('layouts.speedshop')

@section('title', 'Laporan Laba Rugi')

@section('content')
<div class="space-y-8">
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('speedshop.dashboard') }}" class="transition hover:text-gray-700">Home</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="font-medium text-gray-900">Laporan Laba Rugi</span>
    </nav>

    <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm sm:p-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">Laporan Laba Rugi</h1>
                <p class="mt-1 text-sm text-gray-500">Periode {{ $dari->format('d M Y') }} - {{ $sampai->format('d M Y') }}</p>
            </div>
            @include('partials.speedshop.laporan-filter', ['exportRoute' => 'speedshop.laporan.laba-rugi.export', 'dari' => $dari, 'sampai' => $sampai])
        </div>

        <div class="mt-8 grid gap-6 md:grid-cols-2">
            <div class="rounded-xl border border-gray-100 p-6">
                <h2 class="text-lg font-bold text-gray-900">Pendapatan</h2>
                <div class="mt-4 space-y-2">
                    <div class="flex justify-between"><span class="text-gray-600">Pendapatan Jasa (Invoice)</span><span class="font-medium">Rp {{ number_format($pendapatanJasa, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-600">Pendapatan Sparepart (Invoice)</span><span class="font-medium">Rp {{ number_format($pendapatanSparepart, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-600">Pendapatan Penjualan Langsung</span><span class="font-medium">Rp {{ number_format($pendapatanPenjualanLangsung, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between border-t pt-2 font-bold"><span>Total Pendapatan</span><span>Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</span></div>
                </div>
            </div>
            <div class="rounded-xl border border-gray-100 p-6">
                <h2 class="text-lg font-bold text-gray-900">Biaya</h2>
                <div class="mt-4 space-y-2">
                    <div class="flex justify-between"><span class="text-gray-600">Biaya HPP Sparepart</span><span class="font-medium">Rp {{ number_format($biayaSparepart, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-600">Biaya HPP Penjualan Langsung</span><span class="font-medium">Rp {{ number_format($biayaPenjualanLangsung, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between border-t pt-2 font-bold"><span>Total Biaya</span><span>Rp {{ number_format($totalBiaya, 0, ',', '.') }}</span></div>
                </div>
            </div>
        </div>

        <div class="mt-8 rounded-xl border-2 p-6 {{ $labaRugi >= 0 ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50' }}">
            <div class="flex items-center justify-between">
                <span class="text-lg font-bold {{ $labaRugi >= 0 ? 'text-green-800' : 'text-red-800' }}">Laba / Rugi</span>
                <span class="text-2xl font-bold {{ $labaRugi >= 0 ? 'text-green-900' : 'text-red-900' }}">Rp {{ number_format($labaRugi, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
