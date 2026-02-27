@extends('layouts.admin')

@section('title', 'Terima Stok')

@section('content')
<div class="space-y-6">
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.dashboard') }}" class="transition hover:text-gray-700">Home</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="font-medium text-gray-900">Terima Stok</span>
    </nav>

    @include('partials.flash')

    {{-- Input Kode Surat Jalan --}}
    <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
        <h2 class="text-xl font-bold text-gray-900">Terima Stok dari Surat Jalan</h2>
        <p class="mt-1 text-sm text-gray-500">Masukkan kode surat jalan / nomor mutasi dari warehouse untuk menerima stok ke bengkel Anda.</p>

        <form action="{{ route('admin.stock-receipt.receive') }}" method="POST" class="mt-5">
            @csrf
            <div class="flex items-end gap-3">
                <div class="flex-1 max-w-md">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Kode Surat Jalan / Mutasi <span class="text-red-500">*</span></label>
                    <input type="text" name="kode_mutasi" value="{{ old('kode_mutasi') }}" placeholder="Contoh: MUT-20260227-0001" autofocus class="h-12 w-full rounded-xl border border-gray-200 bg-gray-50 px-4 text-sm font-mono tracking-wide outline-none transition focus:border-red-400 focus:bg-white focus:ring-2 focus:ring-red-400/20">
                </div>
                <button type="submit" class="inline-flex h-12 items-center gap-2 rounded-xl bg-red-500 px-6 text-sm font-semibold text-white shadow-sm transition hover:bg-red-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Terima Stok
                </button>
            </div>
        </form>
    </div>

    {{-- Pending (Menunggu Diterima) --}}
    @if($pending->count() > 0)
    <div class="rounded-xl border border-yellow-200 bg-yellow-50 p-6 shadow-sm">
        <div class="flex items-center gap-3">
            <svg class="h-6 w-6 text-yellow-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <h3 class="text-lg font-bold text-yellow-800">Menunggu Diterima ({{ $pending->count() }})</h3>
        </div>
        <p class="mt-1 text-sm text-yellow-700">Surat jalan berikut sudah dikirim ke bengkel Anda. Masukkan kode di atas untuk menerima stok.</p>

        <div class="mt-4 overflow-x-auto rounded-xl border border-yellow-200">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-yellow-100">
                        <th class="px-5 py-3 text-xs font-bold uppercase tracking-wider text-yellow-800">Kode Mutasi</th>
                        <th class="px-5 py-3 text-xs font-bold uppercase tracking-wider text-yellow-800">Dari Gudang</th>
                        <th class="px-5 py-3 text-xs font-bold uppercase tracking-wider text-yellow-800">Tanggal</th>
                        <th class="px-5 py-3 text-xs font-bold uppercase tracking-wider text-yellow-800">Items</th>
                        <th class="px-5 py-3 text-xs font-bold uppercase tracking-wider text-yellow-800">Dibuat oleh</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-yellow-100">
                    @foreach($pending as $m)
                    <tr class="transition hover:bg-yellow-50/50">
                        <td class="px-5 py-3 font-mono font-semibold text-yellow-900">{{ $m->kode_mutasi }}</td>
                        <td class="px-5 py-3 text-yellow-800">{{ $m->fromWarehouse->nama }}</td>
                        <td class="px-5 py-3 text-yellow-800">{{ $m->tanggal->format('d/m/Y') }}</td>
                        <td class="px-5 py-3 text-yellow-800">{{ $m->items->sum('qty') }} unit ({{ $m->items->count() }} produk)</td>
                        <td class="px-5 py-3 text-yellow-800">{{ $m->user->name ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Riwayat Diterima --}}
    <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
        <h3 class="text-lg font-bold text-gray-900">Riwayat Stok Diterima</h3>

        <div class="mt-4 overflow-x-auto rounded-xl border border-gray-100">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Kode Mutasi</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Dari Gudang</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Tanggal</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Items</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Diterima</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($received as $m)
                    <tr class="transition hover:bg-gray-50">
                        <td class="px-5 py-4 font-mono font-medium text-gray-900">{{ $m->kode_mutasi }}</td>
                        <td class="px-5 py-4 text-gray-600">{{ $m->fromWarehouse->nama }}</td>
                        <td class="px-5 py-4 text-gray-600">{{ $m->tanggal->format('d/m/Y') }}</td>
                        <td class="px-5 py-4 text-gray-600">{{ $m->items->sum('qty') }} unit ({{ $m->items->count() }} produk)</td>
                        <td class="px-5 py-4 text-gray-500">{{ $m->updated_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-5 py-12 text-center text-gray-400">Belum ada riwayat penerimaan stok.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
