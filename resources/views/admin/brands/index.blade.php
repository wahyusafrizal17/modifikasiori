@extends('layouts.admin')

@section('title', 'Brand Produk')

@section('content')
<div x-data="brandsCrud()" class="space-y-6">
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.dashboard') }}" class="transition hover:text-gray-700">Home</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="font-medium text-gray-900">Brand Produk</span>
    </nav>

    @include('partials.flash')

    <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-xl font-bold text-gray-900">Brand Produk</h2>
            <div class="flex items-center gap-3">
                <form method="GET" class="relative">
                    <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." class="h-10 w-56 rounded-xl border border-gray-200 bg-gray-50 pl-10 pr-4 text-sm text-gray-700 placeholder-gray-400 transition focus:border-red-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-400/20">
                </form>
                <button @click="openCreate()" class="inline-flex h-10 items-center gap-2 rounded-xl bg-red-500 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-600 active:scale-[0.98]">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Tambah Brand
                </button>
            </div>
        </div>

        <div class="mt-6 overflow-x-auto rounded-xl border border-gray-100">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 w-16">No.</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Nama Brand</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 w-28"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($brands as $i => $brand)
                    <tr class="transition hover:bg-gray-50">
                        <td class="px-5 py-4 text-gray-500">{{ $brands->firstItem() + $i }}</td>
                        <td class="px-5 py-4 font-medium text-gray-900">{{ $brand->nama }}</td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <button @click="openEdit({{ $brand->id }})" class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-500 text-white shadow-sm transition hover:bg-red-600">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </button>
                                <button @click="destroy({{ $brand->id }})" class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-500 text-white shadow-sm transition hover:bg-red-600">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="px-5 py-12 text-center text-gray-400">Belum ada data brand.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($brands->hasPages())
        <div class="mt-5">{{ $brands->links() }}</div>
        @endif
    </div>

    {{-- Modal --}}
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" @keydown.escape.window="showModal = false">
        <div x-show="showModal" x-transition.opacity class="fixed inset-0 bg-black/50" @click="showModal = false"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div x-show="showModal" x-transition class="relative w-full max-w-md rounded-2xl bg-white p-8 shadow-xl" @click.stop>
                <h3 class="text-lg font-bold text-gray-900" x-text="isEdit ? 'Edit Brand' : 'Tambah Brand Baru'"></h3>
                <div class="mt-6 space-y-4">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Nama Brand</label>
                        <input type="text" x-model="form.nama" class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm transition focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20" :class="errors.nama && 'border-red-400'">
                        <template x-if="errors.nama"><p class="mt-1 text-xs text-red-500" x-text="errors.nama[0]"></p></template>
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
function brandsCrud() {
    return {
        showModal: false, isEdit: false, editId: null, loading: false, errors: {},
        form: { nama: '' },
        openCreate() { this.isEdit = false; this.editId = null; this.errors = {}; this.form = { nama: '' }; this.showModal = true; },
        async openEdit(id) {
            this.isEdit = true; this.editId = id; this.errors = {}; this.loading = true; this.showModal = true;
            const res = await fetch(`/admin/brands/${id}`, { headers: { 'Accept': 'application/json' } });
            const data = await res.json();
            this.form = { nama: data.nama };
            this.loading = false;
        },
        async save() {
            this.loading = true; this.errors = {};
            const url = this.isEdit ? `/admin/brands/${this.editId}` : '/admin/brands';
            const res = await fetch(url, {
                method: this.isEdit ? 'PUT' : 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                body: JSON.stringify(this.form)
            });
            if (!res.ok) { const d = await res.json(); this.errors = d.errors || {}; this.loading = false; return; }
            window.location.reload();
        },
        async destroy(id) {
            const result = await Swal.fire({ title: 'Hapus brand?', text: 'Data yang dihapus tidak dapat dikembalikan.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#6b7280', confirmButtonText: 'Ya, hapus!', cancelButtonText: 'Batal' });
            if (!result.isConfirmed) return;
            await fetch(`/admin/brands/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' } });
            window.location.reload();
        }
    }
}
</script>
@endpush
