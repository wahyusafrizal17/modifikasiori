@extends('layouts.speedshop')

@section('title', 'Laporan Biaya')

@section('content')
<div class="space-y-8">
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('speedshop.dashboard') }}" class="transition hover:text-gray-700">Home</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="font-medium text-gray-900">Laporan Biaya</span>
    </nav>

    <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm sm:p-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">Laporan Biaya</h1>
                <p class="mt-1 text-sm text-gray-500">Periode {{ $dari->format('d M Y') }} - {{ $sampai->format('d M Y') }}</p>
            </div>
            @include('partials.speedshop.laporan-filter', ['exportRoute' => 'speedshop.laporan.biaya.export', 'dari' => $dari, 'sampai' => $sampai])
        </div>

        <div class="mt-8 grid gap-4 sm:grid-cols-4">
            <div class="rounded-xl border border-gray-100 bg-blue-50 p-4">
                <p class="text-xs font-semibold uppercase text-blue-600">Biaya Jasa</p>
                <p class="mt-1 text-xl font-bold text-blue-900">Rp {{ number_format($totalBiayaJasa, 0, ',', '.') }}</p>
            </div>
            <div class="rounded-xl border border-gray-100 bg-amber-50 p-4">
                <p class="text-xs font-semibold uppercase text-amber-600">Biaya Sparepart</p>
                <p class="mt-1 text-xl font-bold text-amber-900">Rp {{ number_format($totalBiayaSparepart, 0, ',', '.') }}</p>
            </div>
            <div class="rounded-xl border border-gray-100 bg-purple-50 p-4">
                <p class="text-xs font-semibold uppercase text-purple-600">Biaya Penjualan Langsung</p>
                <p class="mt-1 text-xl font-bold text-purple-900">Rp {{ number_format($totalBiayaPenjualan, 0, ',', '.') }}</p>
            </div>
            <div class="rounded-xl border border-gray-100 bg-red-50 p-4">
                <p class="text-xs font-semibold uppercase text-red-600">Total Biaya</p>
                <p class="mt-1 text-xl font-bold text-red-900">Rp {{ number_format($totalBiaya, 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="mt-8 overflow-x-auto rounded-xl border border-gray-100">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-5 py-3.5 text-xs font-bold uppercase text-gray-500">Tanggal</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase text-gray-500">Kode</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase text-gray-500">Tipe</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase text-gray-500 text-right">Biaya Jasa</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase text-gray-500 text-right">Biaya Sparepart</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase text-gray-500 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($serviceOrders as $so)
                    @php
                        $bj = $so->jasaServis->sum('pivot.biaya');
                        $bs = $so->products->sum(fn($p) => $p->pivot->qty * ($p->hpp ?? $p->harga_pembelian ?? 0));
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-4 text-gray-700">{{ $so->tanggal_masuk->format('d/m/Y') }}</td>
                        <td class="px-5 py-4 font-medium">{{ $so->kode_servis }}</td>
                        <td class="px-5 py-4">Service Order</td>
                        <td class="px-5 py-4 text-right">Rp {{ number_format($bj, 0, ',', '.') }}</td>
                        <td class="px-5 py-4 text-right">Rp {{ number_format($bs, 0, ',', '.') }}</td>
                        <td class="px-5 py-4 text-right font-medium">Rp {{ number_format($bj + $bs, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    @foreach($transaksiPenjualan as $tp)
                    @php
                        $b = 0;
                        foreach($tp->items as $i) { $b += ($i->product->hpp ?? $i->product->harga_pembelian ?? 0) * $i->qty; }
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-4 text-gray-700">{{ $tp->created_at->format('d/m/Y') }}</td>
                        <td class="px-5 py-4 font-medium">{{ $tp->no_transaksi }}</td>
                        <td class="px-5 py-4">Penjualan Langsung</td>
                        <td class="px-5 py-4 text-right">-</td>
                        <td class="px-5 py-4 text-right">Rp {{ number_format($b, 0, ',', '.') }}</td>
                        <td class="px-5 py-4 text-right font-medium">Rp {{ number_format($b, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    @if($serviceOrders->isEmpty() && $transaksiPenjualan->isEmpty())
                    <tr><td colspan="6" class="px-5 py-12 text-center text-gray-400">Tidak ada data</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
