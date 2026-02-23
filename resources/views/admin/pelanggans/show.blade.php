@extends('layouts.admin')

@section('title', 'Detail Pelanggan')

@section('content')
<div class="space-y-6">
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.dashboard') }}" class="transition hover:text-gray-700">Home</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('admin.pelanggans.index') }}" class="transition hover:text-gray-700">Pelanggan</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="font-medium text-gray-900">{{ $pelanggan->nama }}</span>
    </nav>

    @include('partials.flash')

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Info --}}
        <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-bold text-gray-900">Informasi Pelanggan</h3>
            <dl class="mt-4 space-y-3 text-sm">
                <div><dt class="text-gray-400">Nama</dt><dd class="mt-0.5 font-medium text-gray-900">{{ $pelanggan->nama }}</dd></div>
                <div><dt class="text-gray-400">No. HP</dt><dd class="mt-0.5 text-gray-700">{{ $pelanggan->no_hp ?? '-' }}</dd></div>
                <div><dt class="text-gray-400">Alamat</dt><dd class="mt-0.5 text-gray-700">{{ $pelanggan->alamat ?? '-' }}</dd></div>
                <div><dt class="text-gray-400">Kota</dt><dd class="mt-0.5 text-gray-700">{{ $pelanggan->kota->nama ?? '-' }}</dd></div>
            </dl>
        </div>

        {{-- Kendaraan --}}
        <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm lg:col-span-2">
            <h3 class="text-lg font-bold text-gray-900">Kendaraan</h3>
            <div class="mt-4 overflow-x-auto rounded-xl border border-gray-100">
                <table class="w-full text-left text-sm">
                    <thead><tr class="bg-gray-50">
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">No. Polisi</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Merk</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Tipe</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Tahun</th>
                    </tr></thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($pelanggan->kendaraans as $k)
                        <tr class="transition hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $k->nomor_polisi }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $k->merk }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $k->tipe ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $k->tahun ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">Belum ada kendaraan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Riwayat Servis --}}
    <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
        <h3 class="text-lg font-bold text-gray-900">Riwayat Servis</h3>
        <div class="mt-4 overflow-x-auto rounded-xl border border-gray-100">
            <table class="w-full text-left text-sm">
                <thead><tr class="bg-gray-50">
                    <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Kode</th>
                    <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Kendaraan</th>
                    <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Mekanik</th>
                    <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Tanggal</th>
                    <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 text-center">Status</th>
                    <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 text-right">Invoice</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pelanggan->serviceOrders as $order)
                    <tr class="transition hover:bg-gray-50">
                        <td class="px-5 py-4"><a href="{{ route('admin.service-orders.show', $order) }}" class="font-medium text-blue-600 hover:underline">{{ $order->kode_servis }}</a></td>
                        <td class="px-5 py-4 text-gray-700">{{ $order->kendaraan->nomor_polisi ?? '-' }} — {{ $order->kendaraan->merk ?? '' }}</td>
                        <td class="px-5 py-4 text-gray-600">{{ $order->mekanik->nama ?? '-' }}</td>
                        <td class="px-5 py-4 text-gray-600">{{ $order->tanggal_masuk->format('d M Y') }}</td>
                        <td class="px-5 py-4 text-center"><span class="inline-flex rounded-lg px-2.5 py-1 text-xs font-semibold {{ $order->status_badge }}">{{ ucfirst($order->status) }}</span></td>
                        <td class="px-5 py-4 text-right">
                            @if($order->invoice)
                            <a href="{{ route('admin.invoices.show', $order->invoice) }}" class="text-sm font-medium text-green-600 hover:underline">{{ $order->invoice->kode_invoice }}</a>
                            @else
                            <span class="text-gray-400">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-5 py-12 text-center text-gray-400">Belum ada riwayat servis.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
