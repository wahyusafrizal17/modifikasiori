@extends('layouts.warehouse')

@section('title', 'Detail Transaksi Online')

@section('content')
<div class="space-y-6">
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('warehouse.dashboard') }}" class="transition hover:text-gray-700">Home</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('warehouse.transaksi-online.index') }}" class="transition hover:text-gray-700">Transaksi Online</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="font-medium text-gray-900">Detail</span>
    </nav>

    <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Detail Transaksi Online</h2>
                <p class="mt-1 text-sm text-gray-500">No RESI: {{ $transaksiOnline->no_resi }}</p>
            </div>
            <a href="{{ route('warehouse.transaksi-online.index') }}" class="inline-flex h-10 items-center gap-2 rounded-xl border border-gray-200 px-4 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali
            </a>
        </div>

        <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-xl border border-gray-100 bg-gray-50/50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">No RESI</p>
                <p class="mt-1 font-semibold text-gray-900">{{ $transaksiOnline->no_resi }}</p>
            </div>
            <div class="rounded-xl border border-gray-100 bg-gray-50/50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Tanggal</p>
                <p class="mt-1 font-semibold text-gray-900">{{ $transaksiOnline->created_at->format('d M Y H:i') }}</p>
            </div>
            <div class="rounded-xl border border-gray-100 bg-gray-50/50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Dibuat Oleh</p>
                <p class="mt-1 font-semibold text-gray-900">{{ $transaksiOnline->user->name }}</p>
            </div>
            <div class="rounded-xl border border-gray-100 bg-gray-50/50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Total Qty</p>
                <p class="mt-1 font-semibold text-gray-900">{{ number_format($transaksiOnline->items->sum('qty')) }}</p>
            </div>
        </div>

        <div class="mt-8">
            <h3 class="text-sm font-bold uppercase tracking-wide text-gray-500">Daftar Produk</h3>
            <div class="mt-4 overflow-x-auto rounded-xl border border-gray-100">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 w-12">No.</th>
                            <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Kode Produk</th>
                            <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Nama Produk</th>
                            <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 text-center w-24">Qty</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($transaksiOnline->items as $idx => $item)
                        <tr class="transition hover:bg-gray-50">
                            <td class="px-5 py-4 text-gray-500">{{ $idx + 1 }}</td>
                            <td class="px-5 py-4 font-medium text-gray-900">{{ $item->product->kode_produk }}</td>
                            <td class="px-5 py-4 text-gray-700">{{ $item->product->nama_produk }}</td>
                            <td class="px-5 py-4 text-center font-medium text-gray-900">{{ number_format($item->qty) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
