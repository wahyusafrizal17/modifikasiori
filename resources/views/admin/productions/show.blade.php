@extends('layouts.admin')

@section('title', 'Detail Produksi')

@section('content')
<div x-data="productionShow()" class="space-y-6">
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.dashboard') }}" class="transition hover:text-gray-700">Home</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('admin.productions.index') }}" class="transition hover:text-gray-700">Produksi</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="font-medium text-gray-900">Detail</span>
    </nav>

    @include('partials.flash')

    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-900">{{ $production->kode_produksi }}</h2>
            <div class="mt-1">{!! $production->status_badge !!}</div>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            @if($production->status === 'proses')
            <button @click="confirmStatus('qc')" :disabled="loading" class="inline-flex h-10 items-center gap-2 rounded-xl bg-yellow-500 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-yellow-600 disabled:opacity-50">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Mulai QC
            </button>
            @endif

            @if($production->status === 'qc')
            <button @click="confirmStatus('selesai')" :disabled="loading" class="inline-flex h-10 items-center gap-2 rounded-xl bg-red-500 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-600 disabled:opacity-50">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Lolos QC
            </button>
            <button @click="confirmStatus('gagal')" :disabled="loading" class="inline-flex h-10 items-center gap-2 rounded-xl bg-red-500 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-600 disabled:opacity-50">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                Gagal QC
            </button>
            @endif

            @if($production->status !== 'selesai')
            <button @click="destroy()" :disabled="loading" class="inline-flex h-10 items-center gap-2 rounded-xl border-2 border-red-500 bg-white px-5 text-sm font-semibold text-red-600 shadow-sm transition hover:bg-red-50 disabled:opacity-50">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Hapus
            </button>
            @endif

            <a href="{{ route('admin.productions.index') }}" class="inline-flex h-10 items-center gap-2 rounded-xl border border-gray-200 px-5 text-sm font-medium text-gray-600 transition hover:bg-gray-50">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali
            </a>
        </div>
    </div>

    {{-- Info Grid --}}
    <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
        <h3 class="text-base font-bold text-gray-900">Informasi Produksi</h3>
        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div>
                <dt class="text-xs font-medium uppercase tracking-wider text-gray-400">Tanggal</dt>
                <dd class="mt-1 text-sm font-medium text-gray-900">{{ $production->tanggal->format('d/m/Y') }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium uppercase tracking-wider text-gray-400">Status</dt>
                <dd class="mt-1">{!! $production->status_badge !!}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium uppercase tracking-wider text-gray-400">Gudang</dt>
                <dd class="mt-1 text-sm font-medium text-gray-900">{{ $production->warehouse->nama ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium uppercase tracking-wider text-gray-400">Dibuat oleh</dt>
                <dd class="mt-1 text-sm font-medium text-gray-900">{{ $production->user->name ?? '-' }}</dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="text-xs font-medium uppercase tracking-wider text-gray-400">Catatan</dt>
                <dd class="mt-1 text-sm text-gray-700">{{ $production->catatan ?? '-' }}</dd>
            </div>
        </div>
    </div>

    {{-- Bahan Baku Table --}}
    <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
        <h3 class="text-base font-bold text-gray-900">Bahan Baku</h3>
        <div class="mt-4 overflow-x-auto rounded-xl border border-gray-100">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 w-16">No.</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Kode Produk</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Nama Produk</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 text-center">Qty Digunakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($production->materials as $i => $material)
                    <tr class="transition hover:bg-gray-50">
                        <td class="px-5 py-4 text-gray-500">{{ $i + 1 }}</td>
                        <td class="px-5 py-4 font-medium text-gray-900">{{ $material->product->kode_produk }}</td>
                        <td class="px-5 py-4 text-gray-700">{{ $material->product->nama_produk }}</td>
                        <td class="px-5 py-4 text-center font-semibold text-gray-900">{{ $material->qty }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-5 py-8 text-center text-gray-400">Tidak ada data bahan baku.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Hasil Produksi Table --}}
    <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
        <h3 class="text-base font-bold text-gray-900">Hasil Produksi</h3>
        <div class="mt-4 overflow-x-auto rounded-xl border border-gray-100">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 w-16">No.</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Kode Produk</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Nama Produk</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 text-center">Qty</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 text-center">Status QC</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($production->results as $i => $result)
                    <tr class="transition hover:bg-gray-50">
                        <td class="px-5 py-4 text-gray-500">{{ $i + 1 }}</td>
                        <td class="px-5 py-4 font-medium text-gray-900">{{ $result->product->kode_produk }}</td>
                        <td class="px-5 py-4 text-gray-700">{{ $result->product->nama_produk }}</td>
                        <td class="px-5 py-4 text-center font-semibold text-gray-900">{{ $result->qty }}</td>
                        <td class="px-5 py-4 text-center">
                            @php
                                $qcStatus = $result->qc_status ?? 'pending';
                            @endphp
                            @if($qcStatus === 'passed')
                                <span class="inline-flex items-center rounded-lg bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700">Passed</span>
                            @elseif($qcStatus === 'failed')
                                <span class="inline-flex items-center rounded-lg bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700">Failed</span>
                            @else
                                <span class="inline-flex items-center rounded-lg bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-600">Pending</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-5 py-8 text-center text-gray-400">Tidak ada data hasil produksi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function productionShow() {
    return {
        loading: false,

        async confirmStatus(status) {
            if (status === 'gagal') {
                const result = await Swal.fire({
                    title: 'Gagal QC',
                    text: 'Masukkan catatan alasan gagal QC:',
                    input: 'textarea',
                    inputPlaceholder: 'Tuliskan alasan gagal QC...',
                    inputAttributes: { 'aria-label': 'Catatan QC' },
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Gagal QC!',
                    cancelButtonText: 'Batal',
                    inputValidator: (value) => {
                        if (!value) return 'Catatan QC wajib diisi.';
                    }
                });
                if (!result.isConfirmed) return;
                this.updateStatus(status, result.value);
                return;
            }

            let title, text, confirmText, confirmColor;
            if (status === 'qc') {
                title = 'Mulai QC?';
                text = 'Status produksi akan diubah menjadi QC.';
                confirmText = 'Ya, Mulai QC!';
                confirmColor = '#eab308';
            } else if (status === 'selesai') {
                title = 'Lolos QC?';
                text = 'Produksi akan ditandai selesai dan stok hasil produksi akan ditambahkan.';
                confirmText = 'Ya, Lolos QC!';
                confirmColor = '#22c55e';
            }

            const result = await Swal.fire({
                title,
                text,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: confirmColor,
                cancelButtonColor: '#6b7280',
                confirmButtonText: confirmText,
                cancelButtonText: 'Batal'
            });
            if (!result.isConfirmed) return;
            this.updateStatus(status);
        },

        async updateStatus(status, qcNotes = null) {
            this.loading = true;
            const body = { status };
            if (qcNotes) body.qc_notes = qcNotes;

            try {
                const res = await fetch('{{ route("admin.productions.update-status", $production) }}', {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(body)
                });
                if (res.ok) {
                    await Swal.fire({ title: 'Berhasil!', text: 'Status produksi berhasil diperbarui.', icon: 'success', confirmButtonColor: '#ef4444' });
                    window.location.reload();
                } else {
                    const data = await res.json();
                    await Swal.fire({ title: 'Gagal', text: data.message || 'Terjadi kesalahan.', icon: 'error', confirmButtonColor: '#ef4444' });
                }
            } catch (e) {
                await Swal.fire({ title: 'Gagal', text: 'Terjadi kesalahan jaringan.', icon: 'error', confirmButtonColor: '#ef4444' });
            }
            this.loading = false;
        },

        async destroy() {
            const result = await Swal.fire({
                title: 'Hapus produksi?',
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
            try {
                const res = await fetch('{{ route("admin.productions.destroy", $production) }}', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                if (res.ok) {
                    await Swal.fire({ title: 'Berhasil!', text: 'Data produksi berhasil dihapus.', icon: 'success', confirmButtonColor: '#ef4444' });
                    window.location.href = '{{ route("admin.productions.index") }}';
                } else {
                    const data = await res.json();
                    await Swal.fire({ title: 'Gagal', text: data.message || 'Terjadi kesalahan.', icon: 'error', confirmButtonColor: '#ef4444' });
                }
            } catch (e) {
                await Swal.fire({ title: 'Gagal', text: 'Terjadi kesalahan jaringan.', icon: 'error', confirmButtonColor: '#ef4444' });
            }
            this.loading = false;
        }
    }
}
</script>
@endpush
