@extends('layouts.admin')

@section('title', 'Detail Mutasi Stok')

@section('content')
<div x-data="mutationShow()" class="space-y-6">
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.dashboard') }}" class="transition hover:text-gray-700">Home</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('admin.stock-mutations.index') }}" class="transition hover:text-gray-700">Mutasi Stok</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="font-medium text-gray-900">Detail</span>
    </nav>

    @include('partials.flash')

    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-900">{{ $stockMutation->kode_mutasi }}</h2>
            <div class="mt-1">{!! $stockMutation->status_badge !!}</div>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            @if($stockMutation->status === 'draft')
            <button @click="confirmStatus('in_transit')" :disabled="loading" class="inline-flex h-10 items-center gap-2 rounded-xl bg-yellow-500 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-yellow-600 disabled:opacity-50">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                Kirim
            </button>
            @endif

            @if($stockMutation->status === 'in_transit')
            <button @click="confirmStatus('received')" :disabled="loading" class="inline-flex h-10 items-center gap-2 rounded-xl bg-red-500 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-600 disabled:opacity-50">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Terima Barang
            </button>
            @endif

            @if($stockMutation->status === 'draft')
            <button @click="destroy()" :disabled="loading" class="inline-flex h-10 items-center gap-2 rounded-xl bg-red-500 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-600 disabled:opacity-50">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Hapus
            </button>
            @endif

            <a href="{{ route('admin.stock-mutations.index') }}" class="inline-flex h-10 items-center gap-2 rounded-xl border border-gray-200 px-5 text-sm font-medium text-gray-600 transition hover:bg-gray-50">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Info Card --}}
        <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
            <h3 class="text-base font-bold text-gray-900">Informasi Mutasi</h3>
            <dl class="mt-4 space-y-3 text-sm">
                <div>
                    <dt class="text-gray-400">Kode Mutasi</dt>
                    <dd class="mt-0.5 font-medium text-gray-900">{{ $stockMutation->kode_mutasi }}</dd>
                </div>
                <div>
                    <dt class="text-gray-400">Gudang Asal</dt>
                    <dd class="mt-0.5 font-medium text-gray-900">{{ $stockMutation->fromWarehouse->nama }}</dd>
                </div>
                <div>
                    <dt class="text-gray-400">Gudang Tujuan</dt>
                    <dd class="mt-0.5 font-medium text-gray-900">{{ $stockMutation->toWarehouse->nama }}</dd>
                </div>
                <div>
                    <dt class="text-gray-400">Tanggal</dt>
                    <dd class="mt-0.5 text-gray-700">{{ $stockMutation->tanggal->format('d M Y') }}</dd>
                </div>
                <div>
                    <dt class="text-gray-400">Dibuat oleh</dt>
                    <dd class="mt-0.5 text-gray-700">{{ $stockMutation->user->name ?? '-' }}</dd>
                </div>
                @if($stockMutation->catatan)
                <div>
                    <dt class="text-gray-400">Catatan</dt>
                    <dd class="mt-0.5 text-gray-700 leading-relaxed">{{ $stockMutation->catatan }}</dd>
                </div>
                @endif
            </dl>
        </div>

        {{-- Items Table --}}
        <div class="space-y-6 lg:col-span-2">
            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                <h3 class="text-base font-bold text-gray-900">Item Mutasi</h3>
                <div class="mt-4 overflow-x-auto rounded-xl border border-gray-100">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 w-16">No.</th>
                                <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Kode Produk</th>
                                <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Nama Produk</th>
                                <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 text-center w-28">Qty</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($stockMutation->items as $i => $item)
                            <tr class="transition hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-500">{{ $i + 1 }}</td>
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $item->product->kode_produk }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $item->product->nama_produk }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center rounded-lg bg-blue-100 px-2.5 py-1 text-xs font-semibold text-blue-700">{{ $item->qty }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">Tidak ada item.</td></tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-50">
                                <td colspan="3" class="px-4 py-3 font-bold text-gray-900">Total</td>
                                <td class="px-4 py-3 text-center font-bold text-gray-900">{{ $stockMutation->items->sum('qty') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Surat Mutasi --}}
            <div class="rounded-xl border border-gray-100 bg-white shadow-sm" id="printArea">
                <div class="border-b border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">SURAT MUTASI BARANG</h3>
                            <p class="mt-1 text-sm text-gray-500">{{ $stockMutation->kode_mutasi }}</p>
                        </div>
                        <button onclick="printMutation()" class="inline-flex items-center gap-2 rounded-xl border border-gray-200 px-4 py-2 text-sm font-medium text-gray-600 transition hover:bg-gray-50 print:hidden">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            Cetak
                        </button>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-6 text-sm">
                        <div class="space-y-2">
                            <div class="flex"><span class="w-32 text-gray-500">No. Mutasi</span><span class="font-medium text-gray-900">: {{ $stockMutation->kode_mutasi }}</span></div>
                            <div class="flex"><span class="w-32 text-gray-500">Tanggal</span><span class="text-gray-700">: {{ $stockMutation->tanggal->format('d M Y') }}</span></div>
                            <div class="flex"><span class="w-32 text-gray-500">Status</span><span>: {!! $stockMutation->status_badge !!}</span></div>
                        </div>
                        <div class="space-y-2">
                            <div class="flex"><span class="w-32 text-gray-500">Gudang Asal</span><span class="font-medium text-gray-900">: {{ $stockMutation->fromWarehouse->nama }}</span></div>
                            <div class="flex"><span class="w-32 text-gray-500">Gudang Tujuan</span><span class="font-medium text-gray-900">: {{ $stockMutation->toWarehouse->nama }}</span></div>
                            <div class="flex"><span class="w-32 text-gray-500">Dibuat oleh</span><span class="text-gray-700">: {{ $stockMutation->user->name ?? '-' }}</span></div>
                        </div>
                    </div>

                    <div class="mt-6 overflow-x-auto rounded-lg border border-gray-200">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-gray-500 w-12">No.</th>
                                    <th class="px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-gray-500">Kode Produk</th>
                                    <th class="px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-gray-500">Nama Produk</th>
                                    <th class="px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-gray-500 text-center w-24">Qty</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($stockMutation->items as $i => $item)
                                <tr>
                                    <td class="px-4 py-2.5 text-gray-500">{{ $i + 1 }}</td>
                                    <td class="px-4 py-2.5 font-medium text-gray-900">{{ $item->product->kode_produk }}</td>
                                    <td class="px-4 py-2.5 text-gray-700">{{ $item->product->nama_produk }}</td>
                                    <td class="px-4 py-2.5 text-center font-semibold text-gray-900">{{ $item->qty }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-gray-50 font-bold">
                                    <td colspan="3" class="px-4 py-2.5 text-gray-900">Total Item</td>
                                    <td class="px-4 py-2.5 text-center text-gray-900">{{ $stockMutation->items->sum('qty') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    @if($stockMutation->catatan)
                    <div class="mt-4">
                        <p class="text-xs font-semibold uppercase text-gray-400">Catatan</p>
                        <p class="mt-1 text-sm text-gray-700">{{ $stockMutation->catatan }}</p>
                    </div>
                    @endif

                    <div class="mt-8 grid grid-cols-3 gap-6 text-center text-sm print:mt-16">
                        <div>
                            <p class="font-medium text-gray-700">Dibuat oleh</p>
                            <div class="mt-16 border-t border-gray-300 pt-2">
                                <p class="text-gray-600">{{ $stockMutation->user->name ?? '________________' }}</p>
                            </div>
                        </div>
                        <div>
                            <p class="font-medium text-gray-700">Pengirim</p>
                            <div class="mt-16 border-t border-gray-300 pt-2">
                                <p class="text-gray-600">________________</p>
                            </div>
                        </div>
                        <div>
                            <p class="font-medium text-gray-700">Penerima</p>
                            <div class="mt-16 border-t border-gray-300 pt-2">
                                <p class="text-gray-600">________________</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function mutationShow() {
    return {
        loading: false,

        async confirmStatus(status) {
            const labels = {
                'in_transit': 'Kirim mutasi ini?',
                'received': 'Terima barang mutasi ini?'
            };
            const descriptions = {
                'in_transit': 'Status akan diubah menjadi "Dalam Pengiriman". Stok akan dikurangi dari gudang asal.',
                'received': 'Status akan diubah menjadi "Diterima". Stok akan ditambahkan ke gudang tujuan.'
            };
            const colors = {
                'in_transit': '#eab308',
                'received': '#22c55e'
            };

            const result = await Swal.fire({
                title: labels[status] || 'Ubah Status?',
                text: descriptions[status] || 'Status mutasi akan diubah.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: colors[status] || '#3b82f6',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, lanjutkan!',
                cancelButtonText: 'Batal'
            });

            if (!result.isConfirmed) return;

            this.loading = true;
            try {
                const res = await fetch('{{ route("admin.stock-mutations.update-status", $stockMutation) }}', {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ status })
                });

                if (res.ok) {
                    await Swal.fire({ title: 'Berhasil!', text: 'Status mutasi berhasil diubah.', icon: 'success', confirmButtonColor: '#ef4444' });
                    window.location.reload();
                } else {
                    const data = await res.json();
                    await Swal.fire({ title: 'Gagal', text: data.message || 'Terjadi kesalahan.', icon: 'error', confirmButtonColor: '#ef4444' });
                }
            } catch (e) {
                await Swal.fire({ title: 'Error', text: 'Terjadi kesalahan jaringan.', icon: 'error', confirmButtonColor: '#ef4444' });
            }
            this.loading = false;
        },

        async destroy() {
            const result = await Swal.fire({
                title: 'Hapus mutasi stok?',
                text: 'Data yang dihapus tidak dapat dikembalikan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            });
            if (!result.isConfirmed) return;

            this.loading = true;
            await fetch('{{ route("admin.stock-mutations.destroy", $stockMutation) }}', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });
            window.location.href = '{{ route("admin.stock-mutations.index") }}';
        }
    }
}

function printMutation() {
    const content = document.getElementById('printArea').innerHTML;
    const w = window.open('', '_blank');
    w.document.write(`<!DOCTYPE html><html><head><title>Surat Mutasi - {{ $stockMutation->kode_mutasi }}</title>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { font-family: 'Inter', -apple-system, sans-serif; padding: 40px; color: #111827; font-size: 13px; }
            .print-hidden { display: none !important; }
            table { width: 100%; border-collapse: collapse; }
            th, td { padding: 8px 12px; text-align: left; border: 1px solid #e5e7eb; }
            th { background: #f9fafb; font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700; color: #6b7280; }
            .text-center { text-align: center; }
            .font-bold { font-weight: 700; }
            .font-medium { font-weight: 500; }
            .text-gray-500 { color: #6b7280; }
            .text-gray-700 { color: #374151; }
            .text-gray-900 { color: #111827; }
            .mt-4 { margin-top: 16px; }
            .mt-6 { margin-top: 24px; }
            .mt-8 { margin-top: 32px; }
            .mt-16 { margin-top: 64px; }
            .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
            .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 24px; text-align: center; }
            .info-row { display: flex; margin-bottom: 6px; }
            .info-label { width: 120px; color: #6b7280; }
            .border-t { border-top: 1px solid #d1d5db; padding-top: 8px; }
            .badge { display: inline-block; padding: 2px 8px; border-radius: 6px; font-size: 11px; font-weight: 600; }
            @media print { body { padding: 20px; } @page { margin: 15mm; } }
        </style>
    </head><body>`);
    w.document.write(`<h2 style="font-size:18px;font-weight:700;margin-bottom:4px;">SURAT MUTASI BARANG</h2>`);
    w.document.write(`<p style="color:#6b7280;margin-bottom:24px;">{{ $stockMutation->kode_mutasi }}</p>`);
    w.document.write(`<div class="grid-2">
        <div>
            <div class="info-row"><span class="info-label">No. Mutasi</span><span class="font-medium text-gray-900">: {{ $stockMutation->kode_mutasi }}</span></div>
            <div class="info-row"><span class="info-label">Tanggal</span><span class="text-gray-700">: {{ $stockMutation->tanggal->format('d M Y') }}</span></div>
            <div class="info-row"><span class="info-label">Status</span><span>: {{ ucfirst(str_replace('_', ' ', $stockMutation->status)) }}</span></div>
        </div>
        <div>
            <div class="info-row"><span class="info-label">Gudang Asal</span><span class="font-medium text-gray-900">: {{ $stockMutation->fromWarehouse->nama }}</span></div>
            <div class="info-row"><span class="info-label">Gudang Tujuan</span><span class="font-medium text-gray-900">: {{ $stockMutation->toWarehouse->nama }}</span></div>
            <div class="info-row"><span class="info-label">Dibuat oleh</span><span class="text-gray-700">: {{ $stockMutation->user->name ?? '-' }}</span></div>
        </div>
    </div>`);
    w.document.write(`<table class="mt-6"><thead><tr>
        <th style="width:40px">No.</th><th>Kode Produk</th><th>Nama Produk</th><th class="text-center" style="width:80px">Qty</th>
    </tr></thead><tbody>`);
    @foreach($stockMutation->items as $i => $item)
    w.document.write(`<tr><td>{{ $i + 1 }}</td><td class="font-medium">{{ $item->product->kode_produk }}</td><td>{{ $item->product->nama_produk }}</td><td class="text-center font-bold">{{ $item->qty }}</td></tr>`);
    @endforeach
    w.document.write(`</tbody><tfoot><tr style="background:#f9fafb"><td colspan="3" class="font-bold">Total Item</td><td class="text-center font-bold">{{ $stockMutation->items->sum('qty') }}</td></tr></tfoot></table>`);
    @if($stockMutation->catatan)
    w.document.write(`<div class="mt-4"><p style="font-size:11px;font-weight:600;text-transform:uppercase;color:#9ca3af;">Catatan</p><p class="text-gray-700" style="margin-top:4px;">{{ $stockMutation->catatan }}</p></div>`);
    @endif
    w.document.write(`<div class="grid-3 mt-8">
        <div><p class="font-medium text-gray-700">Dibuat oleh</p><div class="mt-16 border-t"><p class="text-gray-700">{{ $stockMutation->user->name ?? '________________' }}</p></div></div>
        <div><p class="font-medium text-gray-700">Pengirim</p><div class="mt-16 border-t"><p class="text-gray-700">________________</p></div></div>
        <div><p class="font-medium text-gray-700">Penerima</p><div class="mt-16 border-t"><p class="text-gray-700">________________</p></div></div>
    </div>`);
    w.document.write('</body></html>');
    w.document.close();
    w.onload = () => { w.print(); };
}
</script>
@endpush
