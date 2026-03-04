@extends('layouts.speedshop')

@section('title', 'Service Record')

@section('content')
<div class="space-y-6">
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('speedshop.dashboard') }}" class="transition hover:text-gray-700">Home</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="font-medium text-gray-900">Service Record</span>
    </nav>

    @include('partials.flash')

    <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Service Record</h2>
                <p class="mt-1 text-sm text-gray-500">Riwayat pelanggan berdasarkan frekuensi kunjungan dan total pengeluaran</p>
            </div>
        </div>

        <form method="GET" class="mt-6 flex flex-wrap items-end gap-4 rounded-xl border border-gray-100 bg-gray-50/50 p-4">
            <div>
                <label for="search" class="mb-1 block text-xs font-semibold text-gray-600">Cari Pelanggan</label>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" id="search" name="search" value="{{ request('search') }}"
                           placeholder="Nama / no HP pelanggan..."
                           class="h-10 w-64 rounded-xl border border-gray-200 bg-white pl-10 pr-4 text-sm text-gray-700 placeholder-gray-400 focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20">
                </div>
            </div>
            <div>
                <label for="sort" class="mb-1 block text-xs font-semibold text-gray-600">Urutkan</label>
                <select id="sort" name="sort" onchange="this.form.submit()"
                        class="h-10 rounded-xl border border-gray-200 bg-white px-3 text-sm text-gray-700 focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20">
                    <option value="kunjungan" {{ ($sort ?? 'kunjungan') === 'kunjungan' ? 'selected' : '' }}>Paling Sering Kunjung</option>
                    <option value="pengeluaran" {{ ($sort ?? '') === 'pengeluaran' ? 'selected' : '' }}>Paling Banyak Pengeluaran</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="h-10 rounded-xl bg-red-500 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-600">
                    Cari
                </button>
                <a href="{{ route('speedshop.service-record') }}" class="inline-flex h-10 items-center rounded-xl border border-gray-200 bg-white px-5 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                    Reset
                </a>
            </div>
        </form>

        <div class="mt-6 overflow-x-auto rounded-xl border border-gray-100">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Rank</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Pelanggan</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">No HP</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 text-center">Total Kunjungan</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 text-right">Total Pengeluaran</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($byPelanggan as $rank => $row)
                    <tr class="transition hover:bg-gray-50">
                        <td class="px-5 py-4">
                            @if($rank < 3)
                            <span class="inline-flex h-7 w-7 items-center justify-center rounded-full text-xs font-bold
                                {{ $rank === 0 ? 'bg-amber-100 text-amber-700' : ($rank === 1 ? 'bg-gray-200 text-gray-700' : 'bg-amber-50 text-amber-800') }}">
                                {{ $rank + 1 }}
                            </span>
                            @else
                            <span class="text-gray-500">{{ $rank + 1 }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 font-medium text-gray-900">{{ $row->pelanggan->nama ?? '-' }}</td>
                        <td class="px-5 py-4 text-gray-600">{{ $row->pelanggan->no_hp ?? '-' }}</td>
                        <td class="px-5 py-4 text-center">
                            <span class="inline-flex items-center rounded-lg bg-blue-100 px-3 py-1 text-sm font-semibold text-blue-700">
                                {{ number_format($row->total_kunjungan) }}x
                            </span>
                        </td>
                        <td class="px-5 py-4 text-right font-semibold text-gray-900">
                            Rp {{ number_format($row->total_pengeluaran, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center text-gray-400">
                            @if(request('search'))
                                Tidak ada pelanggan sesuai pencarian.
                            @else
                                Belum ada data service record. Data muncul setelah ada work order yang selesai.
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
