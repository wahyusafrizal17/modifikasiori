@extends('layouts.speedshop')

@section('title', 'Detail Transaksi Penjualan')

@section('content')
<div class="space-y-6">
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('speedshop.dashboard') }}" class="transition hover:text-gray-700">Home</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('speedshop.transaksi') }}" class="transition hover:text-gray-700">Transaksi Penjualan</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="font-medium text-gray-900">{{ $transaksiPenjualan->no_transaksi }}</span>
    </nav>

    <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ $transaksiPenjualan->no_transaksi }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ $transaksiPenjualan->created_at->format('d M Y H:i') }} · {{ $transaksiPenjualan->user->name ?? '-' }}</p>
            </div>
            <a href="{{ route('speedshop.transaksi') }}" class="inline-flex items-center gap-2 rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50">Kembali</a>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-2">
            <div>
                <h3 class="text-sm font-semibold text-gray-700">Data Pembeli</h3>
                <dl class="mt-2 space-y-2 text-sm">
                    <div><dt class="inline font-medium text-gray-500">Nama:</dt> <dd class="inline text-gray-900">{{ $transaksiPenjualan->nama_pembeli ?: '-' }}</dd></div>
                    <div><dt class="inline font-medium text-gray-500">No HP:</dt> <dd class="inline text-gray-900">{{ $transaksiPenjualan->no_hp ?: '-' }}</dd></div>
                    <div><dt class="inline font-medium text-gray-500">Pembayaran:</dt> <dd class="inline text-gray-900">{{ ucfirst($transaksiPenjualan->jenis_pembayaran ?: '-') }}</dd></div>
                </dl>
            </div>
        </div>

        <div class="mt-6 overflow-x-auto rounded-xl border border-gray-100">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">No.</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Kode</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Nama Produk</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 text-center">Qty</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 text-right">Harga</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($transaksiPenjualan->items as $i => $item)
                    <tr>
                        <td class="px-5 py-4 text-gray-500">{{ $i + 1 }}</td>
                        <td class="px-5 py-4 font-medium text-gray-900">{{ $item->product->kode_produk ?? '-' }}</td>
                        <td class="px-5 py-4 text-gray-700">{{ $item->product->nama_produk ?? '-' }}</td>
                        <td class="px-5 py-4 text-center">{{ $item->qty }}</td>
                        <td class="px-5 py-4 text-right">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                        <td class="px-5 py-4 text-right font-medium">Rp {{ number_format($item->harga_satuan * $item->qty, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4 flex justify-end">
            <div class="rounded-lg bg-red-50 px-6 py-3">
                <span class="text-sm font-medium text-red-700">Total:</span>
                <span class="ml-2 text-lg font-bold text-red-800">Rp {{ number_format($transaksiPenjualan->items->sum(function ($i) { return $i->harga_satuan * $i->qty; }), 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
