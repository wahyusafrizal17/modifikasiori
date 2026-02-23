@extends('layouts.admin')

@section('title', 'Laporan')

@section('content')
<div class="space-y-6">
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.dashboard') }}" class="transition hover:text-gray-700">Home</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="font-medium text-gray-900">Laporan</span>
    </nav>

    {{-- Filter --}}
    <form method="GET" class="flex flex-wrap items-end justify-between gap-4 rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
        <div class="flex flex-wrap items-end gap-4">
            <div>
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-400">Dari Tanggal</label>
                <input type="date" name="from" value="{{ $from }}" class="h-10 rounded-xl border border-gray-200 bg-gray-50 px-4 text-sm outline-none transition focus:border-red-300 focus:ring-2 focus:ring-red-100">
            </div>
            <div>
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-400">Sampai Tanggal</label>
                <input type="date" name="to" value="{{ $to }}" class="h-10 rounded-xl border border-gray-200 bg-gray-50 px-4 text-sm outline-none transition focus:border-red-300 focus:ring-2 focus:ring-red-100">
            </div>
            <div>
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-400">Metode Bayar</label>
                <select name="metode" class="h-10 rounded-xl border border-gray-200 bg-gray-50 px-4 pr-8 text-sm outline-none transition focus:border-red-300 focus:ring-2 focus:ring-red-100">
                    <option value="">Semua</option>
                    <option value="cash" {{ $metode === 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="transfer" {{ $metode === 'transfer' ? 'selected' : '' }}>Transfer</option>
                </select>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <button type="submit" class="inline-flex h-10 items-center gap-2 rounded-xl bg-red-500 px-6 text-sm font-semibold text-white shadow-sm transition hover:bg-red-600 active:scale-[0.98]">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                Filter
            </button>
            <a href="{{ route('admin.laporan.print', request()->query()) }}" target="_blank" class="inline-flex h-10 items-center gap-2 rounded-xl bg-red-500 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-600">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Download (PDF)
            </a>
            <a href="{{ route('admin.laporan.export-csv', request()->query()) }}" class="inline-flex h-10 items-center gap-2 rounded-xl bg-green-500 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-green-600">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Download (XLS)
            </a>
        </div>
    </form>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="flex items-center gap-4 rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
            <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-blue-500 text-white">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Unit Entry</p>
                <p class="mt-0.5 text-2xl font-bold text-gray-900">{{ number_format($unitEntry) }}</p>
            </div>
        </div>

        <div class="flex items-center gap-4 rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
            <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-teal-500 text-white">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Penjualan Parts</p>
                <p class="mt-0.5 text-2xl font-bold text-gray-900">Rp {{ number_format($totalParts, 0, ',', '.') }}</p>
                <p class="text-xs text-gray-400">({{ $countParts }} item)</p>
            </div>
        </div>

        <div class="flex items-center gap-4 rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
            <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-red-500 text-white">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Pendapatan Jasa</p>
                <p class="mt-0.5 text-2xl font-bold text-gray-900">Rp {{ number_format($totalJasa, 0, ',', '.') }}</p>
                <p class="text-xs text-gray-400">({{ $countJasa }} jasa)</p>
            </div>
        </div>

        <div class="flex items-center gap-4 rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
            <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-green-500 text-white">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Total Omset</p>
                <p class="mt-0.5 text-2xl font-bold text-gray-900">Rp {{ number_format($totalOmset, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    {{-- Export + Table --}}
    <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
        <div class="overflow-x-auto rounded-xl border border-gray-100">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">No.</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Kode Servis</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">No. Invoice</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Tgl. Invoice</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Pelanggan</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Mekanik</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">No. HP</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">No. Polisi</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Kendaraan</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Tahun</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 text-center">Metode</th>
                        <th class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wider text-gray-500">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($invoices as $i => $inv)
                    @php $order = $inv->serviceOrder; @endphp
                    <tr class="transition hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-500">{{ $i + 1 }}</td>
                        <td class="px-4 py-3"><a href="{{ route('admin.service-orders.show', $order) }}" class="font-medium text-blue-600 hover:underline">{{ $order->kode_servis }}</a></td>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $inv->kode_invoice }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $inv->tanggal->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $order->pelanggan->nama ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $order->mekanik->nama ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $order->pelanggan->no_hp ?? '-' }}</td>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $order->kendaraan->nomor_polisi ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $order->kendaraan->merk ?? '' }} {{ $order->kendaraan->tipe ?? '' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $order->kendaraan->tahun ?? '-' }}</td>
                        <td class="px-4 py-3 text-center"><span class="inline-flex rounded-lg px-2.5 py-1 text-xs font-semibold {{ $inv->metode_pembayaran === 'cash' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">{{ ucfirst($inv->metode_pembayaran) }}</span></td>
                        <td class="px-4 py-3 text-right font-semibold text-gray-900">Rp {{ number_format($inv->grand_total, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="12" class="px-4 py-12 text-center text-gray-400">Tidak ada data invoice pada periode ini.</td></tr>
                    @endforelse
                </tbody>
                @if($invoices->count())
                <tfoot>
                    <tr class="bg-gray-50">
                        <td colspan="11" class="px-4 py-3 text-right text-sm font-bold text-gray-900">Total Omset</td>
                        <td class="px-4 py-3 text-right text-sm font-bold text-red-600">Rp {{ number_format($totalOmset, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection
