@extends('layouts.admin')

@section('title', 'Detail Pembelian')

@section('content')
<div x-data="purchaseShow()" class="space-y-6">
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.dashboard') }}" class="transition hover:text-gray-700">Home</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('admin.purchases.index') }}" class="transition hover:text-gray-700">Pembelian</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="font-medium text-gray-900">Detail</span>
    </nav>

    @include('partials.flash')

    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-900">{{ $purchase->kode_pembelian }}</h2>
            <div class="mt-1">{!! $purchase->status_badge !!}</div>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            @if($purchase->status === 'draft')
            <button @click="confirmStatus('confirmed')" :disabled="loading" class="inline-flex h-10 items-center gap-2 rounded-xl bg-blue-500 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-600 disabled:opacity-50">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Konfirmasi
            </button>
            <button @click="confirmStatus('received')" :disabled="loading" class="inline-flex h-10 items-center gap-2 rounded-xl bg-red-500 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-600 disabled:opacity-50">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                Terima Barang
            </button>
            @endif

            @if($purchase->status === 'confirmed')
            <button @click="confirmStatus('received')" :disabled="loading" class="inline-flex h-10 items-center gap-2 rounded-xl bg-red-500 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-600 disabled:opacity-50">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                Terima Barang
            </button>
            @endif

            @if($purchase->status !== 'received')
            <button @click="destroy()" :disabled="loading" class="inline-flex h-10 items-center gap-2 rounded-xl bg-red-500 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-600 disabled:opacity-50">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Hapus
            </button>
            @endif

            <a href="{{ route('admin.purchases.index') }}" class="inline-flex h-10 items-center gap-2 rounded-xl border border-gray-200 px-5 text-sm font-medium text-gray-600 transition hover:bg-gray-50">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali
            </a>
        </div>
    </div>

    {{-- Info Grid --}}
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
        <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
            <h3 class="text-base font-bold text-gray-900">Informasi Pembelian</h3>
            <dl class="mt-4 space-y-3 text-sm">
                <div>
                    <dt class="text-gray-400">Supplier</dt>
                    <dd class="mt-0.5 font-medium text-gray-900">{{ $purchase->supplier->nama ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-400">Tanggal</dt>
                    <dd class="mt-0.5 text-gray-700">{{ $purchase->tanggal->format('d M Y') }}</dd>
                </div>
            </dl>
        </div>
        <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
            <h3 class="text-base font-bold text-gray-900">Informasi Tambahan</h3>
            <dl class="mt-4 space-y-3 text-sm">
                <div>
                    <dt class="text-gray-400">Dibuat oleh</dt>
                    <dd class="mt-0.5 text-gray-700">{{ $purchase->user->name ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-400">Catatan</dt>
                    <dd class="mt-0.5 text-gray-700">{{ $purchase->catatan ?? '-' }}</dd>
                </div>
            </dl>
        </div>
    </div>

    {{-- Items Table --}}
    <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
        <h3 class="text-base font-bold text-gray-900">Item Pembelian</h3>
        <div class="mt-4 overflow-x-auto rounded-xl border border-gray-100">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 w-16">No.</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Kode Produk</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Nama Produk</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 text-center">Qty</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 text-right">Harga Satuan</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($purchase->items as $i => $item)
                    <tr class="transition hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-500">{{ $i + 1 }}</td>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $item->product->kode_produk ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $item->product->nama_produk ?? '-' }}</td>
                        <td class="px-4 py-3 text-center text-gray-600">{{ $item->qty }}</td>
                        <td class="px-4 py-3 text-right text-gray-600">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right font-medium text-gray-900">Rp {{ number_format($item->qty * $item->harga_satuan, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Tidak ada item.</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="bg-gray-50">
                        <td colspan="5" class="px-4 py-3 text-right font-bold text-gray-900">Total</td>
                        <td class="px-4 py-3 text-right font-bold text-red-600">Rp {{ number_format($purchase->total, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function purchaseShow() {
    return {
        loading: false,

        async confirmStatus(status) {
            const labels = {
                confirmed: 'Dikonfirmasi',
                received: 'Diterima',
            };
            const result = await Swal.fire({
                title: 'Ubah Status?',
                text: `Status akan diubah menjadi "${labels[status] || status}".`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3b82f6',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, ubah!',
                cancelButtonText: 'Batal',
            });
            if (!result.isConfirmed) return;

            this.loading = true;
            const res = await fetch('{{ route("admin.purchases.update-status", $purchase) }}', {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ status }),
            });

            if (res.ok) {
                await Swal.fire({ title: 'Berhasil!', text: 'Status berhasil diubah.', icon: 'success', confirmButtonColor: '#ef4444' });
                window.location.reload();
            } else {
                const data = await res.json().catch(() => ({}));
                await Swal.fire({ title: 'Gagal', text: data.message || 'Terjadi kesalahan.', icon: 'error', confirmButtonColor: '#ef4444' });
                this.loading = false;
            }
        },

        async destroy() {
            const result = await Swal.fire({
                title: 'Hapus Pembelian?',
                text: 'Data yang dihapus tidak dapat dikembalikan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
            });
            if (!result.isConfirmed) return;

            this.loading = true;
            const res = await fetch('{{ route("admin.purchases.destroy", $purchase) }}', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            });

            if (res.ok) {
                await Swal.fire({ title: 'Terhapus!', text: 'Data pembelian berhasil dihapus.', icon: 'success', confirmButtonColor: '#ef4444' });
                window.location.href = '{{ route("admin.purchases.index") }}';
            } else {
                const data = await res.json().catch(() => ({}));
                await Swal.fire({ title: 'Gagal', text: data.message || 'Terjadi kesalahan.', icon: 'error', confirmButtonColor: '#ef4444' });
                this.loading = false;
            }
        },
    }
}
</script>
@endpush
