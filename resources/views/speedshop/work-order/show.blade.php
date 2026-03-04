@extends('layouts.speedshop')

@section('title', 'Detail Work Order - ' . $service_order->kode_servis)

@section('content')
<div x-data="workOrderShow()" class="space-y-6">
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('speedshop.dashboard') }}" class="transition hover:text-gray-700">Home</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('speedshop.wip') }}" class="transition hover:text-gray-700">Work In Progress</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="font-medium text-gray-900">{{ $service_order->kode_servis }}</span>
    </nav>

    @include('partials.flash')

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Left: Content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Informasi Work Order --}}
            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Informasi Work Order</h2>
                        <p class="mt-1 text-sm text-gray-500">Data pelanggan, kendaraan, dan detail service</p>
                    </div>
                    <span class="inline-flex shrink-0 items-center rounded-lg px-3 py-1.5 text-xs font-semibold {{ $service_order->status_badge }}">{{ ucfirst($service_order->status) }}</span>
                </div>

                <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-gray-600">Pelanggan</label>
                        <div class="rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm font-medium text-gray-900">{{ $service_order->pelanggan->nama ?? '-' }}</div>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-gray-600">No HP</label>
                        <div class="rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-700">{{ $service_order->pelanggan->no_hp ?? '-' }}</div>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="mb-1.5 block text-xs font-semibold text-gray-600">Alamat</label>
                        <div class="rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-700">{{ $service_order->pelanggan->alamat ?? '-' }}</div>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-gray-600">No Polisi</label>
                        <div class="rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm font-medium text-gray-900">{{ $service_order->kendaraan->nomor_polisi ?? '-' }}</div>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-gray-600">Merk / Tipe</label>
                        <div class="rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-700">{{ trim(($service_order->kendaraan->merk ?? '') . ' ' . ($service_order->kendaraan->tipe ?? '')) ?: '-' }}</div>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-gray-600">Mekanik</label>
                        <div class="rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-700">{{ $service_order->mekanik->nama ?? '-' }}</div>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-gray-600">Sumber Kedatangan</label>
                        <div class="rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-700">{{ $service_order->sumber_kedatangan ? (\App\Models\ServiceOrder::SUMBER_KEDATANGAN[$service_order->sumber_kedatangan] ?? $service_order->sumber_kedatangan) : '-' }}</div>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-gray-600">Kategori Service</label>
                        <div class="rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-700">{{ $service_order->kategori_service ? (\App\Models\ServiceOrder::KATEGORI_SERVICE[$service_order->kategori_service] ?? $service_order->kategori_service) : '-' }}</div>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-gray-600">Tanggal Masuk</label>
                        <div class="rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-700">{{ $service_order->tanggal_masuk?->format('d M Y') ?? '-' }}</div>
                    </div>
                </div>

                @if($service_order->keluhan)
                <div class="mt-4">
                    <label class="mb-1.5 block text-xs font-semibold text-gray-600">Keluhan</label>
                    <div class="rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-700">{{ $service_order->keluhan }}</div>
                </div>
                @endif
            </div>

            {{-- Daftar Jasa Servis --}}
            @if($service_order->jasaServis->isNotEmpty())
            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-900">Daftar Jasa Servis</h2>
                    <span class="inline-flex items-center rounded-lg bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600">{{ $service_order->jasaServis->count() }} jasa</span>
                </div>
                <div class="mt-4 overflow-x-auto rounded-xl border border-gray-100">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">No.</th>
                                <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Nama Jasa</th>
                                <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 text-right">Biaya</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($service_order->jasaServis as $i => $j)
                            <tr class="transition hover:bg-gray-50">
                                <td class="px-5 py-4 text-gray-500">{{ $i + 1 }}</td>
                                <td class="px-5 py-4 font-medium text-gray-900">{{ $j->nama }}</td>
                                <td class="px-5 py-4 text-right text-gray-700">Rp {{ number_format($j->pivot->biaya, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            {{-- Daftar Sparepart --}}
            @if($service_order->products->isNotEmpty())
            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-900">Daftar Sparepart</h2>
                    <span class="inline-flex items-center rounded-lg bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600">{{ $service_order->products->count() }} item</span>
                </div>
                <div class="mt-4 overflow-x-auto rounded-xl border border-gray-100">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">No.</th>
                                <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Kode</th>
                                <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Nama</th>
                                <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 text-center">Qty</th>
                                <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 text-right">Harga</th>
                                <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($service_order->products as $i => $p)
                            <tr class="transition hover:bg-gray-50">
                                <td class="px-5 py-4 text-gray-500">{{ $i + 1 }}</td>
                                <td class="px-5 py-4 font-medium text-gray-900">{{ $p->kode_produk }}</td>
                                <td class="px-5 py-4 text-gray-700">{{ $p->nama_produk }}</td>
                                <td class="px-5 py-4 text-center">{{ $p->pivot->qty }}</td>
                                <td class="px-5 py-4 text-right">Rp {{ number_format($p->pivot->harga, 0, ',', '.') }}</td>
                                <td class="px-5 py-4 text-right font-medium text-gray-900">Rp {{ number_format($p->pivot->qty * $p->pivot->harga, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>

        {{-- Right: Ringkasan & Aksi --}}
        <div class="space-y-6">
            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900">Ringkasan</h2>
                <div class="mt-4 space-y-3">
                    <div class="flex items-center justify-between rounded-lg bg-blue-50 px-4 py-3">
                        <span class="text-sm font-medium text-blue-700">Total Jasa</span>
                        <span class="text-sm font-bold text-blue-800">Rp {{ number_format($service_order->total_jasa, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between rounded-lg bg-gray-50 px-4 py-3">
                        <span class="text-sm font-medium text-gray-700">Total Sparepart</span>
                        <span class="text-sm font-bold text-gray-900">Rp {{ number_format($service_order->total_sparepart, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between rounded-lg bg-emerald-50 px-4 py-3">
                        <span class="text-sm font-medium text-emerald-700">Estimasi Total</span>
                        <span class="text-sm font-bold text-emerald-800">Rp {{ number_format($service_order->grand_total, 0, ',', '.') }}</span>
                    </div>
                </div>

                @if($service_order->status === 'antri')
                <div class="mt-5 space-y-3">
                    <button type="button" @click="$refs.serahkanModal.showModal()" class="flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-500 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-600">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Serahkan ke Mekanik
                    </button>
                    <p class="text-center text-xs text-gray-500">Ubah status ke Proses dan pilih mekanik.</p>
                </div>
                @endif

                @if($service_order->status === 'proses')
                <div class="mt-5 space-y-3">
                    <form id="form-selesai" action="{{ route('speedshop.work-orders.complete', $service_order) }}" method="POST" @submit.prevent="confirmSelesai">
                        @csrf
                        <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-500 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-600">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Tandai Selesai
                        </button>
                    </form>
                    <p class="text-center text-xs text-gray-500">Ubah status work order ke Selesai.</p>
                </div>
                @endif

                <div class="mt-5">
                    <a href="{{ route('speedshop.wip') }}" class="flex w-full items-center justify-center gap-2 rounded-xl border border-gray-200 px-5 py-3 text-sm font-medium text-gray-600 transition hover:bg-gray-50">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Kembali ke Daftar
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Serahkan ke Mekanik --}}
    @if($service_order->status === 'antri')
    <dialog x-ref="serahkanModal" class="fixed left-1/2 top-1/2 w-full max-w-md -translate-x-1/2 -translate-y-1/2 rounded-xl border border-gray-100 bg-white p-0 shadow-xl backdrop:bg-black/50">
        <div class="p-6">
            <h3 class="text-lg font-bold text-gray-900">Serahkan ke Mekanik</h3>
            <p class="mt-1 text-sm text-gray-500">Pilih mekanik yang akan menangani work order ini. Status akan diubah ke Proses.</p>
            <form id="form-serahkan-mekanik" action="{{ route('speedshop.work-orders.start', $service_order) }}" method="POST" class="mt-5" @submit.prevent="confirmSerahkan">
                @csrf
                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-gray-600">Mekanik *</label>
                    <select name="mekanik_id" required class="w-full rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20">
                        <option value="">- Pilih Mekanik -</option>
                        @foreach($mekaniks as $m)
                        <option value="{{ $m->id }}">{{ $m->nama }}{{ $m->spesialisasi ? " ({$m->spesialisasi})" : '' }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mt-5 flex gap-3">
                    <button type="submit" class="flex-1 rounded-xl bg-emerald-500 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-600">Serahkan & Proses</button>
                    <button type="button" onclick="this.closest('dialog').close()" class="rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-600 transition hover:bg-gray-50">Batal</button>
                </div>
            </form>
        </div>
    </dialog>
    @endif
</div>

@push('scripts')
<script>
function workOrderShow() {
    return {
        async confirmSerahkan() {
            const form = document.getElementById('form-serahkan-mekanik');
            if (!form) return;
            const mekanikSelect = form.querySelector('[name="mekanik_id"]');
            if (!mekanikSelect?.value) {
                Swal.fire('Perhatian', 'Pilih mekanik terlebih dahulu.', 'warning');
                return;
            }
            const result = await Swal.fire({
                title: 'Serahkan ke Mekanik?',
                text: 'Work order akan diserahkan dan status diubah ke Proses. Lanjutkan?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, serahkan',
                cancelButtonText: 'Batal'
            });
            if (result.isConfirmed) form.submit();
        },

        async confirmSelesai() {
            const form = document.getElementById('form-selesai');
            if (!form) return;
            const result = await Swal.fire({
                title: 'Tandai Selesai?',
                text: 'Work order akan diubah status ke Selesai. Lanjutkan?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, selesai',
                cancelButtonText: 'Batal'
            });
            if (result.isConfirmed) form.submit();
        }
    };
}
</script>
@endpush
@endsection
