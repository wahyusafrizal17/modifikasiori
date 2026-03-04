@extends('layouts.speedshop')

@section('title', 'Pembelian MO')

@section('content')
@php
    $config = $config ?? [
        'pageTitle' => 'Pembelian MO',
        'indexRoute' => 'speedshop.stock-in.pembelian-mo.index',
        'description' => 'Cari dan lihat mutasi yang dikirim ke Speedshop Anda berdasarkan No. Surat Jalan',
        'searchPlaceholder' => 'Cari No. Surat Jalan...',
    ];
@endphp
<div class="space-y-8">
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('speedshop.dashboard') }}" class="transition hover:text-gray-700">Home</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="font-medium text-gray-900">Pembelian MO</span>
    </nav>

    <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm sm:p-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">{{ $config['pageTitle'] }}</h1>
                <p class="mt-1 text-sm text-gray-500">{{ $config['description'] }}</p>
            </div>
            <form method="GET" action="{{ route($config['indexRoute']) }}" class="flex flex-wrap items-center gap-2">
            <select name="status" onchange="this.form.submit()" class="h-11 rounded-xl border border-gray-200 bg-gray-50 px-3 text-sm text-gray-700 transition focus:border-red-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-400/20">
                <option value="">Semua Status</option>
                <option value="dikirim" {{ request('status') === 'dikirim' ? 'selected' : '' }}>Belum Diverifikasi</option>
                <option value="diterima" {{ request('status') === 'diterima' ? 'selected' : '' }}>Sudah Diverifikasi</option>
            </select>
            <div class="relative">
                <svg class="absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ $config['searchPlaceholder'] }}" class="h-11 w-full min-w-[200px] rounded-xl border border-gray-200 bg-gray-50 py-2.5 pl-10 pr-4 text-sm text-gray-900 placeholder-gray-400 transition focus:border-red-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-400/20">
            </div>
            <button type="submit" class="h-11 rounded-xl bg-gray-900 px-4 text-sm font-medium text-white transition hover:bg-gray-800">Cari</button>
            </form>
        </div>

        <div class="mt-8 overflow-x-auto rounded-xl border border-gray-100">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">No.</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Kode</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">No. Surat Jalan</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Tanggal</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Dari</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 text-center">Produk</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 text-center">Total Unit</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Status</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($mutasis as $i => $m)
                    @php $isVerified = $m->status === 'diterima'; @endphp
                    <tr class="transition hover:bg-gray-50">
                        <td class="px-5 py-4 text-gray-500">{{ $mutasis->firstItem() + $i }}</td>
                        <td class="px-5 py-4 font-medium text-gray-900">{{ $m->kode }}</td>
                        <td class="px-5 py-4 text-gray-700">{{ $m->nomor_surat_jalan }}</td>
                        <td class="px-5 py-4 text-gray-600">{{ $m->tanggal->format('d M Y') }}</td>
                        <td class="px-5 py-4">
                            <span class="text-gray-700">{{ $m->user->name ?? '-' }}</span>
                            @if($m->isFromWarehouse())
                                <span class="ml-1 inline-flex items-center rounded-full bg-blue-100 px-1.5 py-0.5 text-[10px] font-semibold text-blue-700">Dari Warehouse</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-center text-gray-600">{{ $m->items->count() }}</td>
                        <td class="px-5 py-4 text-center font-semibold text-gray-900">{{ $m->items->sum('quantity') }}</td>
                        <td class="px-5 py-4">
                            @if($isVerified)
                                <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">Sudah Diverifikasi</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700">Belum Diverifikasi</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <a href="{{ route('speedshop.stock-in.pembelian-mo.show-mutasi', $m) }}" class="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-500 text-white shadow-sm transition hover:bg-gray-600" title="Lihat Detail">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-5 py-12 text-center text-gray-400">
                            <svg class="mx-auto h-10 w-10 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                            <p class="mt-3 text-sm font-medium">Belum ada mutasi masuk</p>
                            <p class="mt-1 text-xs">Gunakan pencarian di atas untuk mencari No. Surat Jalan</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($mutasis->hasPages())
        <div class="mt-8 pt-4">{{ $mutasis->links() }}</div>
        @endif
    </div>
</div>
@endsection
