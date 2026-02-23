@extends('layouts.admin')

@section('title', 'Work In Progress (WIP)')

@section('content')
<div x-data="wipPage()" class="space-y-6">
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.dashboard') }}" class="transition hover:text-gray-700">Home</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="font-medium text-gray-900">Work In Progress (WIP)</span>
    </nav>

    @include('partials.flash')

    {{-- Header & Add Button --}}
    <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Produksi (WIP)</h2>
                <p class="mt-1 text-xs text-gray-400">Kelola produk yang sedang dalam proses produksi</p>
            </div>
            <div class="flex items-center gap-3">
                <form method="GET" class="flex items-center gap-2">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode/produk..." class="h-10 w-48 rounded-xl border border-gray-200 bg-gray-50 pl-10 pr-4 text-sm outline-none transition focus:border-red-400 focus:bg-white focus:ring-2 focus:ring-red-400/20">
                    </div>
                    <select name="status" onchange="this.form.submit()" class="h-10 rounded-xl border border-gray-200 bg-gray-50 px-3 text-sm text-gray-700 focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20">
                        <option value="">Semua Status</option>
                        <option value="proses" {{ request('status') === 'proses' ? 'selected' : '' }}>Proses</option>
                        <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="dibatalkan" {{ request('status') === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </form>
                <button @click="openCreate()" class="inline-flex h-10 items-center gap-2 rounded-xl bg-green-500 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-green-600 active:scale-[0.98]">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Tambah WIP
                </button>
            </div>
        </div>

        <div class="mt-6 overflow-x-auto rounded-xl border border-gray-100">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">No.</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Kode WIP</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Produk</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 text-center">Qty</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Status</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Tgl Mulai</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Tgl Selesai</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Keterangan</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($wips as $i => $wip)
                    <tr class="transition hover:bg-gray-50">
                        <td class="px-5 py-4 text-gray-500">{{ $wips->firstItem() + $i }}</td>
                        <td class="px-5 py-4 font-medium text-gray-900">{{ $wip->kode_wip }}</td>
                        <td class="px-5 py-4">
                            <p class="font-medium text-gray-900">{{ $wip->product->kode_produk }}</p>
                            <p class="text-xs text-gray-500">{{ $wip->product->nama_produk }}</p>
                        </td>
                        <td class="px-5 py-4 text-center font-semibold text-gray-900">{{ $wip->qty }}</td>
                        <td class="px-5 py-4">{!! $wip->status_badge !!}</td>
                        <td class="px-5 py-4 text-gray-600 whitespace-nowrap">{{ $wip->tanggal_mulai->format('d M Y') }}</td>
                        <td class="px-5 py-4 text-gray-600 whitespace-nowrap">{{ $wip->tanggal_selesai?->format('d M Y') ?? '-' }}</td>
                        <td class="px-5 py-4 text-gray-600 text-xs">{{ $wip->keterangan ?? '-' }}</td>
                        <td class="px-5 py-4">
                            @if($wip->status === 'proses')
                            <div class="flex items-center justify-end gap-2">
                                <button @click="markSelesai({{ $wip->id }}, '{{ $wip->kode_wip }}', {{ $wip->qty }})" class="flex h-8 w-8 items-center justify-center rounded-lg bg-green-500 text-white shadow-sm transition hover:bg-green-600" title="Selesai">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                </button>
                                <button @click="markBatal({{ $wip->id }}, '{{ $wip->kode_wip }}')" class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-500 text-white shadow-sm transition hover:bg-red-600" title="Batalkan">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="px-5 py-12 text-center text-gray-400">Belum ada data WIP.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($wips->hasPages())
        <div class="mt-5">{{ $wips->links() }}</div>
        @endif
    </div>

    {{-- Create Modal --}}
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" @keydown.escape.window="showModal = false">
        <div x-show="showModal" x-transition.opacity class="fixed inset-0 bg-black/50" @click="showModal = false"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div x-show="showModal" x-transition class="relative w-full max-w-lg rounded-2xl bg-white p-8 shadow-xl" @click.stop>
                <h3 class="text-lg font-bold text-gray-900">Tambah WIP Baru</h3>
                <p class="mt-1 text-sm text-gray-500">Tambahkan produk yang akan diproduksi</p>
                <div class="mt-6 space-y-4">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Produk <span class="text-red-500">*</span></label>
                        <select x-ref="productSelect" class="w-full">
                            <option value="">Pilih produk...</option>
                            @foreach($products as $prod)
                            <option value="{{ $prod->id }}">{{ $prod->kode_produk }} — {{ $prod->nama_produk }}</option>
                            @endforeach
                        </select>
                        <template x-if="errors.product_id"><p class="mt-1 text-xs text-red-500" x-text="errors.product_id[0]"></p></template>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">Jumlah Produksi <span class="text-red-500">*</span></label>
                            <input type="number" x-model.number="form.qty" min="1" class="h-10 w-full rounded-xl border border-gray-200 bg-gray-50 px-4 text-sm outline-none transition focus:border-red-300 focus:ring-2 focus:ring-red-100">
                            <template x-if="errors.qty"><p class="mt-1 text-xs text-red-500" x-text="errors.qty[0]"></p></template>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">Tanggal Mulai <span class="text-red-500">*</span></label>
                            <input type="date" x-model="form.tanggal_mulai" class="h-10 w-full rounded-xl border border-gray-200 bg-gray-50 px-4 text-sm outline-none transition focus:border-red-300 focus:ring-2 focus:ring-red-100">
                        </div>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Keterangan</label>
                        <input type="text" x-model="form.keterangan" placeholder="Contoh: Batch produksi minggu ke-3" class="h-10 w-full rounded-xl border border-gray-200 bg-gray-50 px-4 text-sm outline-none transition focus:border-red-300 focus:ring-2 focus:ring-red-100">
                    </div>
                </div>
                <div class="mt-8 flex items-center gap-3">
                    <button @click="save()" :disabled="loading" class="rounded-xl bg-red-500 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-600 disabled:opacity-50">
                        <span x-show="!loading">Mulai Produksi</span>
                        <span x-show="loading">Menyimpan...</span>
                    </button>
                    <button @click="showModal = false" class="rounded-xl border border-gray-200 px-6 py-2.5 text-sm font-medium text-gray-600 transition hover:bg-gray-50">Batal</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function wipPage() {
    return {
        showModal: false, loading: false, errors: {},
        form: { product_id: '', qty: 1, tanggal_mulai: new Date().toISOString().split('T')[0], keterangan: '' },
        _tomSelect: null,

        openCreate() {
            this.errors = {};
            this.form = { product_id: '', qty: 1, tanggal_mulai: new Date().toISOString().split('T')[0], keterangan: '' };
            this.showModal = true;
            this.$nextTick(() => {
                if (!this._tomSelect) {
                    this._tomSelect = new TomSelect(this.$refs.productSelect, {
                        placeholder: 'Ketik untuk mencari produk...',
                        allowEmptyOption: true,
                        onChange: (value) => { this.form.product_id = value; }
                    });
                } else {
                    this._tomSelect.clear();
                }
            });
        },

        async save() {
            this.loading = true; this.errors = {};
            const res = await fetch('/admin/wip', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                body: JSON.stringify(this.form)
            });
            if (!res.ok) { const d = await res.json(); this.errors = d.errors || {}; this.loading = false; return; }
            window.location.reload();
        },

        async markSelesai(id, kode, qty) {
            const result = await Swal.fire({
                title: 'Produksi Selesai?',
                html: `<b>${kode}</b> akan ditandai selesai.<br>Stok akan bertambah <b>${qty}</b> unit.`,
                icon: 'question',
                showCancelButton: true, confirmButtonColor: '#22c55e', cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, selesai!', cancelButtonText: 'Batal'
            });
            if (!result.isConfirmed) return;
            await fetch(`/admin/wip/${id}/status`, {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                body: JSON.stringify({ status: 'selesai' })
            });
            window.location.reload();
        },

        async markBatal(id, kode) {
            const result = await Swal.fire({
                title: 'Batalkan Produksi?',
                html: `<b>${kode}</b> akan dibatalkan. Stok tidak akan bertambah.`,
                icon: 'warning',
                showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, batalkan!', cancelButtonText: 'Kembali'
            });
            if (!result.isConfirmed) return;
            await fetch(`/admin/wip/${id}/status`, {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                body: JSON.stringify({ status: 'dibatalkan' })
            });
            window.location.reload();
        }
    }
}
</script>
@endpush
