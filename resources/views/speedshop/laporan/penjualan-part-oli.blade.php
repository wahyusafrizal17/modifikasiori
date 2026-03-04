@extends('layouts.speedshop')

@section('title', 'Laporan Penjualan Part & Oli')

@section('content')
<div class="space-y-8">
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('speedshop.dashboard') }}" class="transition hover:text-gray-700">Home</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="font-medium text-gray-900">Penjualan Part & Oli</span>
    </nav>

    <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm sm:p-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">Penjualan Part & Oli</h1>
                <p class="mt-1 text-sm text-gray-500">Periode {{ $dari->format('d M Y') }} - {{ $sampai->format('d M Y') }}</p>
            </div>
            @include('partials.speedshop.laporan-filter', ['exportRoute' => 'speedshop.laporan.penjualan-part-oli.export', 'dari' => $dari, 'sampai' => $sampai])
        </div>

        <div class="mt-8 overflow-x-auto rounded-xl border border-gray-100">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-5 py-3.5 text-xs font-bold uppercase text-gray-500">No.</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase text-gray-500">Kode Produk</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase text-gray-500">Nama Produk</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase text-gray-500">Kategori</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase text-gray-500 text-center">Total Qty</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase text-gray-500 text-right">Total Nilai</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($merged as $i => $row)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-4 text-gray-500">{{ $i + 1 }}</td>
                        <td class="px-5 py-4 font-medium">{{ $row->kode_produk }}</td>
                        <td class="px-5 py-4">{{ $row->nama_produk }}</td>
                        <td class="px-5 py-4">{{ $row->kategori }}</td>
                        <td class="px-5 py-4 text-center font-semibold">{{ $row->total_qty }}</td>
                        <td class="px-5 py-4 text-right font-medium">Rp {{ number_format($row->total_nilai, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-5 py-12 text-center text-gray-400">Tidak ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
