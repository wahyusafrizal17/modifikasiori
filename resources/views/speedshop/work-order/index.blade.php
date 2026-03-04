@extends('layouts.speedshop')

@section('title', request()->routeIs('speedshop.wip') ? 'Work In Progress' : 'Work Order')

@section('content')
<div class="space-y-6">
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('speedshop.dashboard') }}" class="transition hover:text-gray-700">Home</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="font-medium text-gray-900">{{ request()->routeIs('speedshop.wip') ? 'Work In Progress' : 'Work Order' }}</span>
    </nav>

    @include('partials.flash')

    <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ request()->routeIs('speedshop.wip') ? 'Work In Progress' : 'Work Order' }}</h2>
                <p class="mt-1 text-sm text-gray-500">Daftar work order (service order) speedshop</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <form method="GET" class="flex items-center gap-2">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode / pelanggan / no polisi..." class="h-10 w-64 rounded-xl border border-gray-200 bg-gray-50 pl-10 pr-4 text-sm text-gray-700 placeholder-gray-400 transition focus:border-red-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-400/20">
                    </div>
                    <select name="status" onchange="this.form.submit()" class="h-10 rounded-xl border border-gray-200 bg-gray-50 px-3 text-sm text-gray-700 focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20">
                        <option value="">Semua Status</option>
                        <option value="antri" {{ request('status') === 'antri' ? 'selected' : '' }}>Antri</option>
                        <option value="proses" {{ request('status') === 'proses' ? 'selected' : '' }}>Proses</option>
                        <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="dibatalkan" {{ request('status') === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                    <select name="kategori_service" onchange="this.form.submit()" class="h-10 rounded-xl border border-gray-200 bg-gray-50 px-3 text-sm text-gray-700 focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20">
                        <option value="">Semua Kategori</option>
                        @foreach(\App\Models\ServiceOrder::KATEGORI_SERVICE as $val => $label)
                        <option value="{{ $val }}" {{ request('kategori_service') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="h-10 rounded-xl bg-gray-100 px-4 text-sm font-medium text-gray-700 transition hover:bg-gray-200">Cari</button>
                </form>
                <a href="{{ route('speedshop.work-orders.create') }}" class="inline-flex h-10 items-center gap-2 rounded-xl bg-red-500 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-600 active:scale-[0.98]">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Buat Work Order
                </a>
            </div>
        </div>

        <div class="mt-6 overflow-x-auto rounded-xl border border-gray-100">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">No.</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Kode</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Tanggal</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Pelanggan</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">No Polisi</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Sumber</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Kategori</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Status</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($orders as $i => $o)
                    <tr class="transition hover:bg-gray-50">
                        <td class="px-5 py-4 text-gray-500">{{ $orders->firstItem() + $i }}</td>
                        <td class="px-5 py-4 font-medium text-gray-900">{{ $o->kode_servis }}</td>
                        <td class="px-5 py-4 text-gray-600">{{ $o->tanggal_masuk->format('d M Y') }}</td>
                        <td class="px-5 py-4 text-gray-700">{{ $o->pelanggan->nama ?? '-' }}</td>
                        <td class="px-5 py-4 text-gray-600">{{ $o->kendaraan->nomor_polisi ?? '-' }}</td>
                        <td class="px-5 py-4 text-gray-600">{{ $o->sumber_kedatangan ? (\App\Models\ServiceOrder::SUMBER_KEDATANGAN[$o->sumber_kedatangan] ?? $o->sumber_kedatangan) : '-' }}</td>
                        <td class="px-5 py-4 text-gray-600">{{ $o->kategori_service ? (\App\Models\ServiceOrder::KATEGORI_SERVICE[$o->kategori_service] ?? $o->kategori_service) : '-' }}</td>
                        <td class="px-5 py-4">
                            <span class="inline-flex rounded-lg px-2.5 py-1 text-xs font-semibold {{ $o->status_badge }}">{{ ucfirst($o->status) }}</span>
                        </td>
                        <td class="px-5 py-4">
                            <a href="{{ route('speedshop.wip.show', $o) }}" class="inline-flex items-center rounded-lg bg-red-500 px-3 py-1.5 text-xs font-medium text-white transition hover:bg-red-600">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="px-5 py-12 text-center text-gray-400">Belum ada work order.</td></tr>
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
