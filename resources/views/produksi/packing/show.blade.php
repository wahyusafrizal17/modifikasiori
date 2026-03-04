@extends('layouts.produksi')

@section('title', 'Detail Packing - ' . $packing->kode)

@section('content')
<div class="space-y-6">
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('produksi.dashboard') }}" class="transition hover:text-gray-700">Home</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('produksi.packing.index') }}" class="transition hover:text-gray-700">Packing Kemas</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="font-medium text-gray-900">{{ $packing->kode }}</span>
    </nav>

    @include('partials.flash')

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6">
            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900">Informasi Packing</h2>
                <div class="mt-4 flex flex-wrap gap-4">
                    <div class="min-w-[140px]">
                        <label class="block text-xs font-semibold text-gray-500">Kode</label>
                        <p class="mt-1 font-medium text-gray-900">{{ $packing->kode }}</p>
                    </div>
                    <div class="min-w-[140px]">
                        <label class="block text-xs font-semibold text-gray-500">Tanggal</label>
                        <p class="mt-1 text-gray-700">{{ $packing->tanggal->format('d M Y') }}</p>
                    </div>
                    <div class="min-w-[140px]">
                        <label class="block text-xs font-semibold text-gray-500">Dibuat oleh</label>
                        <p class="mt-1 text-gray-700">{{ $packing->user->name ?? '-' }}</p>
                    </div>
                </div>
                @if($packing->catatan)
                <div class="mt-4">
                    <label class="block text-xs font-semibold text-gray-500">Catatan</label>
                    <p class="mt-1 text-gray-700">{{ $packing->catatan }}</p>
                </div>
                @endif
            </div>

            {{-- Daftar produk (detail) per transaksi --}}
            @foreach($packing->details as $dIdx => $detail)
            <div class="rounded-xl border border-gray-100 bg-white shadow-sm overflow-hidden">
                <div class="flex items-center justify-between border-b border-gray-100 bg-gray-50/50 px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-100 text-sm font-bold text-red-600">{{ $dIdx + 1 }}</div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900">{{ $detail->product->nama_produk ?? '-' }}</h3>
                            <p class="text-xs text-gray-500">{{ $detail->product->kode_produk ?? '' }} — {{ $detail->quantity }} unit</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center rounded-lg bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-700">{{ $detail->quantity }} unit</span>
                </div>

                <div class="p-6">
                    <p class="mb-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Bahan yang digunakan</p>
                    @if($detail->items->isEmpty())
                        <p class="text-sm text-gray-400">Tidak ada BSP/kemasan dicatat.</p>
                    @else
                    <div class="overflow-x-auto rounded-xl border border-gray-100">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Tipe</th>
                                    <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Kode / Nama</th>
                                    <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 text-right">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($detail->items as $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        @if($item->type === 'bahan_siap_produksi')
                                            <span class="inline-flex items-center rounded-lg bg-green-100 px-2 py-0.5 text-xs font-semibold text-green-700">BSP</span>
                                        @else
                                            <span class="inline-flex items-center rounded-lg bg-purple-100 px-2 py-0.5 text-xs font-semibold text-purple-700">Kemasan</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-gray-700">
                                        @if($item->type === 'bahan_siap_produksi' && $item->bahanSiapProduksi)
                                            [{{ $item->bahanSiapProduksi->kode }}] {{ $item->bahanSiapProduksi->nama }}
                                        @elseif($item->kemasan)
                                            [{{ $item->kemasan->kode }}] {{ $item->kemasan->nama }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right font-semibold text-gray-900">{{ $item->quantity }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        <div class="space-y-6">
            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900">Ringkasan</h2>
                <div class="mt-4 space-y-3">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Jumlah Produk</span>
                        <span class="font-semibold text-gray-900">{{ $packing->details->count() }} item</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Total Unit</span>
                        <span class="font-semibold text-gray-900">{{ $packing->details->sum('quantity') }} unit</span>
                    </div>
                    @php
                        $totalBsp = $packing->details->sum(fn ($d) => $d->items->where('type', 'bahan_siap_produksi')->sum('quantity'));
                        $totalKemasan = $packing->details->sum(fn ($d) => $d->items->where('type', 'kemasan')->sum('quantity'));
                    @endphp
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Total BSP</span>
                        <span class="font-semibold text-gray-700">{{ $totalBsp }} unit</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Total Kemasan</span>
                        <span class="font-semibold text-gray-700">{{ $totalKemasan }} unit</span>
                    </div>
                </div>
                <div class="mt-4 rounded-lg border border-amber-200 bg-amber-50/80 p-3">
                    <div class="flex items-center gap-2">
                        <svg class="h-3.5 w-3.5 flex-shrink-0 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <p class="text-xs text-amber-700">Hasil packing belum masuk stok — akan diproses via Mutasi</p>
                    </div>
                </div>
            </div>

            <a href="{{ route('produksi.packing.index') }}" class="flex w-full items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white px-5 py-3 text-sm font-medium text-gray-600 shadow-sm transition hover:bg-gray-50">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Daftar
            </a>
        </div>
    </div>
</div>
@endsection
