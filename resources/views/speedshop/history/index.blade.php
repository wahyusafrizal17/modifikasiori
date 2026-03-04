@extends('layouts.speedshop')

@section('title', 'History')

@section('content')
<div class="space-y-6">
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('speedshop.dashboard') }}" class="transition hover:text-gray-700">Home</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="font-medium text-gray-900">History</span>
    </nav>

    @include('partials.flash')

    <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900">History Work Order</h2>
                <p class="mt-1 text-sm text-gray-500">Daftar work order yang sudah selesai</p>
            </div>
        </div>

        <form method="GET" class="mt-6 flex flex-wrap items-end gap-4 rounded-xl border border-gray-100 bg-gray-50/50 p-4">
            <div>
                <label for="dari_tanggal" class="mb-1 block text-xs font-semibold text-gray-600">Dari Tanggal</label>
                <input type="date" id="dari_tanggal" name="dari_tanggal" value="{{ request('dari_tanggal') }}"
                       class="h-10 rounded-xl border border-gray-200 bg-white px-3 text-sm text-gray-700 focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20">
            </div>
            <div>
                <label for="sampai_tanggal" class="mb-1 block text-xs font-semibold text-gray-600">Sampai Tanggal</label>
                <input type="date" id="sampai_tanggal" name="sampai_tanggal" value="{{ request('sampai_tanggal') }}"
                       class="h-10 rounded-xl border border-gray-200 bg-white px-3 text-sm text-gray-700 focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20">
            </div>
            <div>
                <label for="search" class="mb-1 block text-xs font-semibold text-gray-600">Cari</label>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" id="search" name="search" value="{{ request('search') }}"
                           placeholder="Kode / pelanggan / no polisi..."
                           class="h-10 w-64 rounded-xl border border-gray-200 bg-white pl-10 pr-4 text-sm text-gray-700 placeholder-gray-400 focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20">
                </div>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="h-10 rounded-xl bg-red-500 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-600">
                    Filter
                </button>
                <a href="{{ route('speedshop.history') }}" class="inline-flex h-10 items-center rounded-xl border border-gray-200 bg-white px-5 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                    Reset
                </a>
            </div>
        </form>

        <div class="mt-6 overflow-x-auto rounded-xl border border-gray-100">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">No.</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Kode</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Tanggal Masuk</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Tanggal Selesai</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Pelanggan</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">No Polisi</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Mekanik</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 text-right">Total</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($orders as $i => $o)
                    <tr class="transition hover:bg-gray-50">
                        <td class="px-5 py-4 text-gray-500">{{ $orders->firstItem() + $i }}</td>
                        <td class="px-5 py-4 font-medium text-gray-900">{{ $o->kode_servis }}</td>
                        <td class="px-5 py-4 text-gray-600">{{ $o->tanggal_masuk?->format('d M Y') ?? '-' }}</td>
                        <td class="px-5 py-4 text-gray-600">{{ $o->tanggal_selesai?->format('d M Y') ?? '-' }}</td>
                        <td class="px-5 py-4 text-gray-700">{{ $o->pelanggan->nama ?? '-' }}</td>
                        <td class="px-5 py-4 text-gray-600">{{ $o->kendaraan->nomor_polisi ?? '-' }}</td>
                        <td class="px-5 py-4 text-gray-600">{{ $o->mekanik->nama ?? '-' }}</td>
                        <td class="px-5 py-4 text-right font-medium text-gray-900">Rp {{ number_format($o->grand_total, 0, ',', '.') }}</td>
                        <td class="px-5 py-4">
                            <a href="{{ route('speedshop.wip.show', $o) }}" class="inline-flex items-center rounded-lg bg-red-500 px-3 py-1.5 text-xs font-medium text-white transition hover:bg-red-600">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-5 py-12 text-center text-gray-400">
                            @if(request()->hasAny(['dari_tanggal', 'sampai_tanggal', 'search']))
                                Tidak ada data history sesuai filter.
                            @else
                                Belum ada history work order selesai.
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
        <div class="mt-5">{{ $orders->links() }}</div>
        @endif
    </div>
</div>
@endsection
