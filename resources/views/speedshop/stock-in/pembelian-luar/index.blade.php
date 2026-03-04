@extends('layouts.speedshop')

@section('title', 'Pembelian Luar')

@section('content')
<div class="space-y-6">
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('speedshop.dashboard') }}" class="transition hover:text-gray-700">Home</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="font-medium text-gray-900">Pembelian Luar</span>
    </nav>

    @include('partials.flash')

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
        <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Total Transaksi</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($totalAll) }}</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-500">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Disetujui</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($totalApproved) }}</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-50 text-green-500">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Ditolak</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($totalRejected) }}</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-red-50 text-red-500">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Pembelian Luar</h2>
                <p class="mt-1 text-sm text-gray-500">Input stok masuk dari supplier luar (perlu verifikasi Manager Speedshop)</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <form method="GET" class="flex items-center gap-2">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode..." class="h-10 w-48 rounded-xl border border-gray-200 bg-gray-50 pl-10 pr-4 text-sm text-gray-700 placeholder-gray-400 transition focus:border-red-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-400/20">
                    </div>
                    <select name="status" onchange="this.form.submit()" class="h-10 rounded-xl border border-gray-200 bg-gray-50 px-3 text-sm text-gray-700 focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </form>
                <a href="{{ route('speedshop.stock-in.pembelian-luar.create') }}" class="inline-flex h-10 items-center gap-2 rounded-xl bg-red-500 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-600 active:scale-[0.98]">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Input Transaksi
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
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Diajukan Oleh</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 text-center">Jumlah Item</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Status</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($transaksis as $i => $t)
                    <tr class="transition hover:bg-gray-50">
                        <td class="px-5 py-4 text-gray-500">{{ $transaksis->firstItem() + $i }}</td>
                        <td class="px-5 py-4 font-medium text-gray-900">{{ $t->kode }}</td>
                        <td class="px-5 py-4 text-gray-600">{{ $t->created_at->format('d M Y H:i') }}</td>
                        <td class="px-5 py-4 text-gray-600">{{ $t->user->name }}</td>
                        <td class="px-5 py-4 text-center text-gray-600">{{ $t->items->count() }}</td>
                        <td class="px-5 py-4">
                            @if($t->status === 'pending')
                                <span class="inline-flex items-center rounded-lg bg-yellow-100 px-2.5 py-1 text-xs font-semibold text-yellow-700">Pending</span>
                            @elseif($t->status === 'approved')
                                <span class="inline-flex items-center rounded-lg bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700">Approved</span>
                            @else
                                <span class="inline-flex items-center rounded-lg bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700">Rejected</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <a href="{{ route('speedshop.stock-in.pembelian-luar.show', $t) }}" class="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-500 text-white shadow-sm transition hover:bg-gray-600" title="Detail">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-5 py-12 text-center text-gray-400">Belum ada data transaksi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transaksis->hasPages())
        <div class="mt-5">{{ $transaksis->links() }}</div>
        @endif
    </div>
</div>
@endsection
