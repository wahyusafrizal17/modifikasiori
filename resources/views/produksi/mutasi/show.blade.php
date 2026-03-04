@extends('layouts.produksi')

@section('title', 'Detail Mutasi - ' . $mutasi->kode)

@section('content')
<div class="space-y-6">
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('produksi.dashboard') }}" class="transition hover:text-gray-700">Home</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('produksi.mutasi.index') }}" class="transition hover:text-gray-700">Mutasi</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="font-medium text-gray-900">{{ $mutasi->kode }}</span>
    </nav>

    @include('partials.flash')

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6">
            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900">Informasi Mutasi</h2>
                <p class="mt-1 text-sm text-gray-500">Detail pengiriman produk ke Warehouse</p>

                <div class="mt-5 flex flex-wrap items-end gap-3">
                    <div class="w-48">
                        <label class="mb-1.5 block text-xs font-semibold text-gray-600">Kode Mutasi</label>
                        <div class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm font-medium text-gray-900">{{ $mutasi->kode }}</div>
                    </div>
                    <div class="flex-1 min-w-[180px]">
                        <label class="mb-1.5 block text-xs font-semibold text-gray-600">No. Surat Jalan</label>
                        <div class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm font-medium text-gray-900">{{ $mutasi->nomor_surat_jalan }}</div>
                    </div>
                    <div class="w-44">
                        <label class="mb-1.5 block text-xs font-semibold text-gray-600">Tanggal</label>
                        <div class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-700">{{ $mutasi->tanggal->format('d M Y') }}</div>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="mb-1.5 block text-xs font-semibold text-gray-600">Warehouse Tujuan</label>
                    <div class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm">
                        <span class="font-medium text-gray-900">{{ $mutasi->warehouse->nama ?? '-' }}</span>
                        @if($mutasi->warehouse?->alamat)
                            <span class="ml-1 text-gray-500">— {{ $mutasi->warehouse->alamat }}</span>
                        @endif
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap items-end gap-3">
                    <div class="flex-1 min-w-[180px]">
                        <label class="mb-1.5 block text-xs font-semibold text-gray-600">Dibuat oleh</label>
                        <div class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-700">{{ $mutasi->user->name }}</div>
                    </div>
                    <div class="w-44">
                        <label class="mb-1.5 block text-xs font-semibold text-gray-600">Status</label>
                        <div class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm">
                            @if($mutasi->isDikirim())
                                <span class="inline-flex items-center rounded-lg bg-yellow-100 px-2.5 py-0.5 text-xs font-semibold text-yellow-700">Dikirim</span>
                            @else
                                <span class="inline-flex items-center rounded-lg bg-green-100 px-2.5 py-0.5 text-xs font-semibold text-green-700">Diterima</span>
                            @endif
                        </div>
                    </div>
                </div>

                @if($mutasi->catatan)
                <div class="mt-4">
                    <label class="mb-1.5 block text-xs font-semibold text-gray-600">Catatan</label>
                    <div class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-700">{{ $mutasi->catatan }}</div>
                </div>
                @endif
            </div>

            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-900">Daftar Produk</h2>
                    <span class="inline-flex items-center rounded-lg bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600">{{ $mutasi->items->count() }} produk</span>
                </div>

                <div class="mt-4 overflow-x-auto rounded-xl border border-gray-100">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">No.</th>
                                <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Kode</th>
                                <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Nama Produk</th>
                                <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 text-center">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($mutasi->items as $i => $item)
                            <tr class="transition hover:bg-gray-50">
                                <td class="px-5 py-4 text-gray-500">{{ $i + 1 }}</td>
                                <td class="px-5 py-4 font-medium text-gray-900">{{ $item->product->kode_produk ?? '-' }}</td>
                                <td class="px-5 py-4 text-gray-700">{{ $item->product->nama_produk ?? '-' }}</td>
                                <td class="px-5 py-4 text-center font-semibold text-gray-900">{{ $item->quantity }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Right --}}
        <div class="space-y-6">
            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900">Ringkasan</h2>

                <div class="mt-4 space-y-3">
                    <div class="flex items-center justify-between rounded-lg bg-blue-50 px-4 py-3">
                        <span class="text-sm font-medium text-blue-700">Total Produk</span>
                        <span class="text-sm font-bold text-blue-800">{{ $mutasi->items->count() }} item</span>
                    </div>
                    <div class="flex items-center justify-between rounded-lg bg-green-50 px-4 py-3">
                        <span class="text-sm font-medium text-green-700">Total Unit</span>
                        <span class="text-sm font-bold text-green-800">{{ $mutasi->items->sum('quantity') }} unit</span>
                    </div>
                </div>

                {{-- Status Progress --}}
                <div class="mt-5">
                    <label class="mb-3 block text-sm font-semibold text-gray-700">Status Pengiriman</label>
                    @php
                        $steps = [
                            ['label' => 'Dibuat', 'done' => true],
                            ['label' => 'Dikirim', 'done' => true],
                            ['label' => 'Diterima', 'done' => $mutasi->isDiterima()],
                        ];
                        $currentStep = $mutasi->isDikirim() ? 1 : 2;
                    @endphp
                    <div class="flex items-start justify-between">
                        @foreach($steps as $idx => $step)
                        <div class="flex flex-col items-center" style="width: {{ 100 / count($steps) }}%">
                            <div class="relative flex w-full items-center justify-center">
                                @if($idx > 0)
                                <div class="absolute right-1/2 h-0.5 w-full {{ $step['done'] ? 'bg-green-400' : 'bg-gray-200' }}"></div>
                                @endif
                                @if($idx < count($steps) - 1)
                                <div class="absolute left-1/2 h-0.5 w-full {{ $steps[$idx + 1]['done'] ? 'bg-green-400' : 'bg-gray-200' }}"></div>
                                @endif
                                @if($step['done'])
                                    <div class="relative z-10 flex h-8 w-8 items-center justify-center rounded-full bg-green-500 shadow-sm shadow-green-200">
                                        <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                @elseif($idx === $currentStep + 1)
                                    <div class="relative z-10 flex h-8 w-8 items-center justify-center rounded-full border-2 border-yellow-400 bg-yellow-50">
                                        <div class="h-2.5 w-2.5 rounded-full bg-yellow-400 animate-pulse"></div>
                                    </div>
                                @else
                                    <div class="relative z-10 flex h-8 w-8 items-center justify-center rounded-full bg-gray-200">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                @endif
                            </div>
                            <p class="mt-2 text-center text-xs font-medium {{ $step['done'] ? 'text-green-600' : 'text-gray-400' }}">{{ $step['label'] }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>

                @if($mutasi->isDikirim())
                <div class="mt-5 rounded-lg border border-amber-200 bg-amber-50 p-3">
                    <p class="text-xs font-semibold text-amber-800">Menunggu konfirmasi Warehouse</p>
                    <p class="mt-1 text-xs text-amber-700">Notifikasi telah dikirim. Produk belum masuk stok Warehouse.</p>
                </div>
                @else
                <div class="mt-5 rounded-lg border border-green-200 bg-green-50 p-3">
                    <p class="text-xs font-semibold text-green-800">Mutasi telah diterima</p>
                    <p class="mt-1 text-xs text-green-700">Produk telah dikonfirmasi oleh Warehouse.</p>
                </div>
                @endif

                <div class="mt-5">
                    <a href="{{ route('produksi.mutasi.index') }}" class="flex w-full items-center justify-center gap-2 rounded-xl border border-gray-200 px-5 py-3 text-sm font-medium text-gray-600 transition hover:bg-gray-50">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Kembali ke Daftar
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
