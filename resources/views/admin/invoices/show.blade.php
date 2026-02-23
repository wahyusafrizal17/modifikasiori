@extends('layouts.admin')

@section('title', 'Detail Invoice')

@section('content')
<div class="space-y-6">
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.dashboard') }}" class="transition hover:text-gray-700">Home</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('admin.invoices.index') }}" class="transition hover:text-gray-700">Transaksi</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="font-medium text-gray-900">{{ $invoice->kode_invoice }}</span>
    </nav>

    @include('partials.flash')

    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-gray-900">{{ $invoice->kode_invoice }}</h2>
        <a href="{{ route('admin.invoices.print', $invoice) }}" target="_blank" class="inline-flex h-10 items-center gap-2 rounded-xl bg-gray-700 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-800">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Cetak Invoice
        </a>
    </div>

    @php $order = $invoice->serviceOrder; @endphp

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
            <h3 class="text-base font-bold text-gray-900">Informasi Invoice</h3>
            <dl class="mt-4 space-y-3 text-sm">
                <div><dt class="text-gray-400">No. Invoice</dt><dd class="mt-0.5 font-medium text-gray-900">{{ $invoice->kode_invoice }}</dd></div>
                <div><dt class="text-gray-400">Tanggal</dt><dd class="mt-0.5 text-gray-700">{{ $invoice->tanggal->format('d M Y') }}</dd></div>
                <div><dt class="text-gray-400">Work Order</dt><dd class="mt-0.5"><a href="{{ route('admin.service-orders.show', $order) }}" class="font-medium text-blue-600 hover:underline">{{ $order->kode_servis }}</a></dd></div>
                <div><dt class="text-gray-400">Metode Bayar</dt><dd class="mt-0.5"><span class="inline-flex rounded-lg px-2.5 py-1 text-xs font-semibold {{ $invoice->metode_pembayaran === 'cash' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">{{ ucfirst($invoice->metode_pembayaran) }}</span></dd></div>
                @if($invoice->catatan)
                <div><dt class="text-gray-400">Catatan</dt><dd class="mt-0.5 text-gray-700">{{ $invoice->catatan }}</dd></div>
                @endif
            </dl>
        </div>

        <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
            <h3 class="text-base font-bold text-gray-900">Pelanggan</h3>
            <dl class="mt-4 space-y-3 text-sm">
                <div><dt class="text-gray-400">Nama</dt><dd class="mt-0.5 font-medium text-gray-900">{{ $order->pelanggan->nama }}</dd></div>
                <div><dt class="text-gray-400">No. HP</dt><dd class="mt-0.5 text-gray-700">{{ $order->pelanggan->no_hp ?? '-' }}</dd></div>
                <div><dt class="text-gray-400">Alamat</dt><dd class="mt-0.5 text-gray-700">{{ $order->pelanggan->alamat ?? '-' }}</dd></div>
            </dl>
        </div>

        <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
            <h3 class="text-base font-bold text-gray-900">Kendaraan</h3>
            <dl class="mt-4 space-y-3 text-sm">
                <div><dt class="text-gray-400">No. Polisi</dt><dd class="mt-0.5 font-medium text-gray-900">{{ $order->kendaraan->nomor_polisi }}</dd></div>
                <div><dt class="text-gray-400">Merk/Tipe</dt><dd class="mt-0.5 text-gray-700">{{ $order->kendaraan->merk }} {{ $order->kendaraan->tipe }}</dd></div>
                <div><dt class="text-gray-400">Mekanik</dt><dd class="mt-0.5 text-gray-700">{{ $order->mekanik->nama ?? '-' }}</dd></div>
            </dl>
        </div>
    </div>

    {{-- Breakdown --}}
    <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
        <h3 class="text-base font-bold text-gray-900">Rincian Biaya</h3>
        <div class="mt-4 space-y-6">
            {{-- Jasa --}}
            <div class="overflow-x-auto rounded-xl border border-gray-100">
                <table class="w-full text-left text-sm">
                    <thead><tr class="bg-gray-50"><th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500" colspan="2">Jasa Servis</th></tr></thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($order->jasaServis as $j)
                        <tr><td class="px-4 py-3 text-gray-700">{{ $j->nama }}</td><td class="px-4 py-3 text-right font-medium">Rp {{ number_format($j->pivot->biaya, 0, ',', '.') }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Products --}}
            <div class="overflow-x-auto rounded-xl border border-gray-100">
                <table class="w-full text-left text-sm">
                    <thead><tr class="bg-gray-50">
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Sparepart</th>
                        <th class="px-4 py-3 text-center text-xs font-bold uppercase tracking-wider text-gray-500">Qty</th>
                        <th class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wider text-gray-500">Harga</th>
                        <th class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wider text-gray-500">Subtotal</th>
                    </tr></thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($order->products as $p)
                        <tr>
                            <td class="px-4 py-3 text-gray-700">{{ $p->nama_produk }}</td>
                            <td class="px-4 py-3 text-center text-gray-600">{{ $p->pivot->qty }}</td>
                            <td class="px-4 py-3 text-right text-gray-600">Rp {{ number_format($p->pivot->harga, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-medium">Rp {{ number_format($p->pivot->qty * $p->pivot->harga, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Summary --}}
            <div class="rounded-xl border border-gray-100 bg-gray-50 p-5">
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between"><span class="text-gray-500">Total Jasa</span><span class="font-medium">Rp {{ number_format($invoice->total_jasa, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Total Sparepart</span><span class="font-medium">Rp {{ number_format($invoice->total_sparepart, 0, ',', '.') }}</span></div>
                    @if($invoice->diskon > 0)
                    <div class="flex justify-between"><span class="text-gray-500">Diskon</span><span class="font-medium text-green-600">- Rp {{ number_format($invoice->diskon, 0, ',', '.') }}</span></div>
                    @endif
                    <hr class="border-gray-200">
                    <div class="flex justify-between text-lg"><span class="font-bold text-gray-900">Grand Total</span><span class="font-bold text-red-600">Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}</span></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
