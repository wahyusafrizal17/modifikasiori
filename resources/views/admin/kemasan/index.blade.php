@extends('layouts.admin')

@section('title', 'Kemasan')

@section('content')
<div x-data="kemasanCrud()" class="space-y-6">
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.dashboard') }}" class="transition hover:text-gray-700">Home</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="font-medium text-gray-900">Kemasan</span>
    </nav>

    @include('partials.flash')

    <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-xl font-bold text-gray-900">Kemasan</h2>
            <div class="flex items-center gap-3">
                <form method="GET" class="relative">
                    <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." class="h-10 w-56 rounded-xl border border-gray-200 bg-gray-50 pl-10 pr-4 text-sm text-gray-700 placeholder-gray-400 transition focus:border-red-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-400/20">
                </form>
                <button @click="openCreate()" class="inline-flex h-10 items-center gap-2 rounded-xl bg-red-500 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-600 active:scale-[0.98]">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Tambah Kemasan
                </button>
            </div>
        </div>

        <div class="mt-6 overflow-x-auto rounded-xl border border-gray-100">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 w-16">No.</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Kode</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Nama</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Supplier</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 text-right">Harga (Rp)</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 text-right">Stok</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 w-28"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($kemasans as $i => $kemasan)
                    <tr class="transition hover:bg-gray-50">
                        <td class="px-5 py-4 text-gray-500">{{ $kemasans->firstItem() + $i }}</td>
                        <td class="px-5 py-4 font-mono text-sm text-gray-700">{{ $kemasan->kode }}</td>
                        <td class="px-5 py-4 font-medium text-gray-900">{{ $kemasan->nama }}</td>
                        <td class="px-5 py-4 text-gray-600">{{ $kemasan->supplier->nama ?? '-' }}</td>
                        <td class="px-5 py-4 text-right text-gray-700">{{ number_format($kemasan->harga, 0, ',', '.') }}</td>
                        <td class="px-5 py-4 text-right font-semibold text-gray-900">{{ number_format($kemasan->stok) }}</td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <button @click="openEdit({{ $kemasan->id }})" class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-500 text-white shadow-sm transition hover:bg-red-600">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </button>
                                <button @click="destroy({{ $kemasan->id }})" class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-500 text-white shadow-sm transition hover:bg-red-600">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-5 py-12 text-center text-gray-400">Belum ada data kemasan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($kemasans->hasPages())
        <div class="mt-5">{{ $kemasans->links() }}</div>
        @endif
    </div>

    {{-- Modal --}}
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" @keydown.escape.window="showModal = false">
        <div x-show="showModal" x-transition.opacity class="fixed inset-0 bg-black/50" @click="showModal = false"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div x-show="showModal" x-transition class="relative w-full max-w-lg rounded-2xl bg-white p-8 shadow-xl" @click.stop>
                <h3 class="text-lg font-bold text-gray-900" x-text="isEdit ? 'Edit Kemasan' : 'Tambah Kemasan'"></h3>
                <div class="mt-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">Kode <span class="text-red-500">*</span></label>
                            <input type="text" x-model="form.kode" class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm transition focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20" :class="errors.kode && 'border-red-400'">
                            <template x-if="errors.kode"><p class="mt-1 text-xs text-red-500" x-text="errors.kode[0]"></p></template>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">Nama <span class="text-red-500">*</span></label>
                            <input type="text" x-model="form.nama" class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm transition focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20" :class="errors.nama && 'border-red-400'">
                            <template x-if="errors.nama"><p class="mt-1 text-xs text-red-500" x-text="errors.nama[0]"></p></template>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">Harga <span class="text-red-500">*</span></label>
                            <input type="number" x-model="form.harga" min="0" class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm transition focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20" :class="errors.harga && 'border-red-400'">
                            <template x-if="errors.harga"><p class="mt-1 text-xs text-red-500" x-text="errors.harga[0]"></p></template>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">Supplier</label>
                            <select x-model="form.supplier_id" class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm transition focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20">
                                <option value="">Pilih Supplier</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="mt-8 flex items-center gap-3">
                    <button @click="save()" :disabled="loading" class="rounded-xl bg-red-500 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-600 disabled:opacity-50">
                        <span x-show="!loading" x-text="isEdit ? 'Update' : 'Simpan'"></span>
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
function kemasanCrud() {
    return {
        showModal: false, isEdit: false, editId: null, loading: false, errors: {},
        form: { kode: '', nama: '', harga: 0, supplier_id: '' },
        openCreate() { this.isEdit = false; this.editId = null; this.errors = {}; this.form = { kode: '', nama: '', harga: 0, supplier_id: '' }; this.showModal = true; },
        async openEdit(id) {
            this.isEdit = true; this.editId = id; this.errors = {}; this.loading = true; this.showModal = true;
            const res = await fetch(`/admin/kemasan/${id}`, { headers: { 'Accept': 'application/json' } });
            const data = await res.json();
            this.form = { kode: data.kode, nama: data.nama, harga: data.harga, supplier_id: data.supplier_id ? String(data.supplier_id) : '' };
            this.loading = false;
        },
        async save() {
            this.loading = true; this.errors = {};
            const url = this.isEdit ? `/admin/kemasan/${this.editId}` : '/admin/kemasan';
            const res = await fetch(url, {
                method: this.isEdit ? 'PUT' : 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                body: JSON.stringify(this.form)
            });
            if (!res.ok) { const d = await res.json(); this.errors = d.errors || {}; this.loading = false; return; }
            window.location.reload();
        },
        async destroy(id) {
            const result = await Swal.fire({ title: 'Hapus kemasan?', text: 'Data yang dihapus tidak dapat dikembalikan.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#6b7280', confirmButtonText: 'Ya, hapus!', cancelButtonText: 'Batal' });
            if (!result.isConfirmed) return;
            await fetch(`/admin/kemasan/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' } });
            window.location.reload();
        }
    }
}
</script>
@endpush
