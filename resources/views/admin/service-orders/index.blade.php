@extends('layouts.admin')

@section('title', 'Work Order')

@section('content')
<div class="space-y-6">
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.dashboard') }}" class="transition hover:text-gray-700">Home</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="font-medium text-gray-900">Work Order</span>
    </nav>

    @include('partials.flash')

    <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-xl font-bold text-gray-900">Work Order</h2>
            <div class="flex items-center gap-3">
                <form method="GET" class="flex items-center gap-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode/pelanggan..."
                           class="h-10 w-56 rounded-xl border border-gray-200 bg-gray-50 px-4 text-sm text-gray-700 outline-none transition focus:border-red-300 focus:ring-2 focus:ring-red-100">
                    <select name="status" onchange="this.form.submit()" class="h-10 rounded-xl border border-gray-200 bg-gray-50 px-3 text-sm text-gray-700 outline-none transition focus:border-red-300 focus:ring-2 focus:ring-red-100">
                        <option value="">Semua Status</option>
                        @foreach(['antri','proses','selesai','dibatalkan'] as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="flex h-10 w-10 items-center justify-center rounded-xl bg-gray-100 text-gray-500 transition hover:bg-gray-200">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </button>
                </form>
                <a href="{{ route('admin.service-orders.create') }}" class="inline-flex h-10 items-center gap-2 rounded-xl bg-red-500 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-600 active:scale-[0.98]">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Buat Work Order
                </a>
            </div>
        </div>

        <div class="mt-6 overflow-x-auto rounded-xl border border-gray-100">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Kode</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Pelanggan</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Kendaraan</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Mekanik</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Tanggal</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 text-center">Status</th>
                        <th class="px-5 py-3.5 text-right text-xs font-bold uppercase tracking-wider text-gray-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($orders as $order)
                    <tr class="transition hover:bg-gray-50">
                        <td class="px-5 py-4"><a href="{{ route('admin.service-orders.show', $order) }}" class="font-medium text-blue-600 hover:underline">{{ $order->kode_servis }}</a></td>
                        <td class="px-5 py-4 text-gray-700">{{ $order->pelanggan->nama ?? '-' }}</td>
                        <td class="px-5 py-4 text-gray-600">{{ $order->kendaraan->nomor_polisi ?? '-' }} {{ $order->kendaraan->merk ?? '' }}</td>
                        <td class="px-5 py-4 text-gray-600">{{ $order->mekanik->nama ?? '-' }}</td>
                        <td class="px-5 py-4 text-gray-600">{{ $order->tanggal_masuk->format('d M Y') }}</td>
                        <td class="px-5 py-4 text-center"><span class="inline-flex rounded-lg px-2.5 py-1 text-xs font-semibold {{ $order->status_badge }}">{{ ucfirst($order->status) }}</span></td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.service-orders.show', $order) }}" class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-500 text-white shadow-sm transition hover:bg-blue-600" title="Detail">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                @if(!in_array($order->status, ['selesai','dibatalkan']))
                                <a href="{{ route('admin.service-orders.edit', $order) }}" class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-500 text-white shadow-sm transition hover:bg-red-600" title="Edit">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-5 py-12 text-center text-gray-400">Belum ada work order.</td></tr>
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
