@extends('layouts.admin')

@section('title', 'Products')

@section('content')
<div x-data="productsCrud()" class="space-y-6">
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.dashboard') }}" class="transition hover:text-gray-700">Home</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="font-medium text-gray-900">Products</span>
    </nav>

    @include('partials.flash')

    <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-xl font-bold text-gray-900">Products</h2>
            <div class="flex items-center gap-3">
                <form method="GET" class="flex items-center gap-2">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." class="h-10 w-56 rounded-xl border border-gray-200 bg-gray-50 pl-10 pr-4 text-sm text-gray-700 placeholder-gray-400 transition focus:border-red-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-400/20">
                    </div>
                    <select name="category_id" onchange="this.form.submit()" class="h-10 rounded-xl border border-gray-200 bg-gray-50 px-3 text-sm text-gray-700 focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nama }}</option>
                        @endforeach
                    </select>
                </form>
                <button @click="openCreate()" class="inline-flex h-10 items-center gap-2 rounded-xl bg-green-500 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-green-600 active:scale-[0.98]">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Add New Product
                </button>
            </div>
        </div>

        <div class="mt-6 overflow-x-auto rounded-xl border border-gray-100">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">No.</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Kode Produk</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Nama Produk</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Kategori</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Stok</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 text-right">Harga Pembelian (Rp)</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 text-right">Harga Jual (Rp)</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($products as $i => $product)
                    <tr class="transition hover:bg-gray-50">
                        <td class="px-5 py-4 text-gray-500">{{ $products->firstItem() + $i }}</td>
                        <td class="px-5 py-4 font-medium text-gray-900">{{ $product->kode_produk }}</td>
                        <td class="px-5 py-4 text-gray-700">{{ $product->nama_produk }}</td>
                        <td class="px-5 py-4 text-gray-600">{{ $product->category->nama ?? '-' }}</td>
                        <td class="px-5 py-4 text-center">
                            <span class="inline-flex items-center rounded-lg {{ $product->jumlah > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }} px-2.5 py-1 text-xs font-semibold">{{ number_format($product->jumlah) }}</span>
                        </td>
                        <td class="px-5 py-4 text-right text-gray-700">{{ number_format($product->harga_pembelian, 0, ',', '.') }}</td>
                        <td class="px-5 py-4 text-right text-gray-700">{{ number_format($product->harga_jual, 0, ',', '.') }}</td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <button @click="openBarcode('{{ $product->kode_produk }}')" class="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-700 text-white shadow-sm transition hover:bg-gray-800" title="Barcode">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                </button>
                                <button @click="openEdit({{ $product->id }})" class="flex h-8 w-8 items-center justify-center rounded-lg bg-green-500 text-white shadow-sm transition hover:bg-green-600" title="Edit">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </button>
                                <button @click="destroy({{ $product->id }})" class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-500 text-white shadow-sm transition hover:bg-red-600" title="Hapus">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-5 py-12 text-center text-gray-400">Belum ada data produk.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
        <div class="mt-5">{{ $products->links() }}</div>
        @endif
    </div>

    {{-- Create/Edit Modal --}}
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" @keydown.escape.window="showModal = false">
        <div x-show="showModal" x-transition.opacity class="fixed inset-0 bg-black/50" @click="showModal = false"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div x-show="showModal" x-transition class="relative w-full max-w-lg rounded-2xl bg-white p-8 shadow-xl" @click.stop>
                <h3 class="text-lg font-bold text-gray-900" x-text="isEdit ? 'Edit Produk' : 'Tambah Produk Baru'"></h3>
                <div class="mt-6 space-y-4">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Kode Produk</label>
                        <input type="text" x-model="form.kode_produk" class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm transition focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20" :class="errors.kode_produk && 'border-red-400'">
                        <template x-if="errors.kode_produk"><p class="mt-1 text-xs text-red-500" x-text="errors.kode_produk[0]"></p></template>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Nama Produk</label>
                        <input type="text" x-model="form.nama_produk" class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm transition focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20" :class="errors.nama_produk && 'border-red-400'">
                        <template x-if="errors.nama_produk"><p class="mt-1 text-xs text-red-500" x-text="errors.nama_produk[0]"></p></template>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Kategori</label>
                        <select x-model="form.category_id" class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm transition focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20" :class="errors.category_id && 'border-red-400'">
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->nama }}</option>
                            @endforeach
                        </select>
                        <template x-if="errors.category_id"><p class="mt-1 text-xs text-red-500" x-text="errors.category_id[0]"></p></template>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">Harga Pembelian (Rp)</label>
                            <input type="number" x-model="form.harga_pembelian" min="0" class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm transition focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20" :class="errors.harga_pembelian && 'border-red-400'">
                            <template x-if="errors.harga_pembelian"><p class="mt-1 text-xs text-red-500" x-text="errors.harga_pembelian[0]"></p></template>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">Harga Jual (Rp)</label>
                            <input type="number" x-model="form.harga_jual" min="0" class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm transition focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20" :class="errors.harga_jual && 'border-red-400'">
                            <template x-if="errors.harga_jual"><p class="mt-1 text-xs text-red-500" x-text="errors.harga_jual[0]"></p></template>
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

    {{-- Stock In Modal --}}
    <div x-show="showStockIn" x-cloak class="fixed inset-0 z-50 overflow-y-auto" @keydown.escape.window="showStockIn = false">
        <div x-show="showStockIn" x-transition.opacity class="fixed inset-0 bg-black/50" @click="showStockIn = false"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div x-show="showStockIn" x-transition class="relative w-full max-w-md rounded-2xl bg-white p-8 shadow-xl" @click.stop>
                <h3 class="text-lg font-bold text-gray-900">Stok Masuk</h3>
                <p class="mt-1 text-sm text-gray-500" x-text="'Produk: ' + stockInKode"></p>
                <div class="mt-5 space-y-4">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Jumlah Masuk <span class="text-red-500">*</span></label>
                        <input type="number" x-model.number="stockInForm.qty" min="1" class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm transition focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20">
                        <template x-if="stockInErrors.qty"><p class="mt-1 text-xs text-red-500" x-text="stockInErrors.qty[0]"></p></template>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Keterangan</label>
                        <input type="text" x-model="stockInForm.keterangan" placeholder="Contoh: Restok dari supplier" class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm transition focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20">
                    </div>
                </div>
                <div class="mt-6 flex items-center gap-3">
                    <button @click="saveStockIn()" :disabled="loading" class="rounded-xl bg-blue-500 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-600 disabled:opacity-50">
                        <span x-show="!loading">Simpan</span>
                        <span x-show="loading">Menyimpan...</span>
                    </button>
                    <button @click="showStockIn = false" class="rounded-xl border border-gray-200 px-6 py-2.5 text-sm font-medium text-gray-600 transition hover:bg-gray-50">Batal</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Stock History Modal --}}
    <div x-show="showHistory" x-cloak class="fixed inset-0 z-50 overflow-y-auto" @keydown.escape.window="showHistory = false">
        <div x-show="showHistory" x-transition.opacity class="fixed inset-0 bg-black/50" @click="showHistory = false"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div x-show="showHistory" x-transition class="relative w-full max-w-2xl rounded-2xl bg-white p-8 shadow-xl" @click.stop>
                <h3 class="text-lg font-bold text-gray-900">History Stok</h3>
                <p class="mt-1 text-sm text-gray-500">
                    <span x-text="historyProduct.kode_produk"></span> — <span x-text="historyProduct.nama_produk"></span>
                    <span class="ml-2 inline-flex items-center rounded-lg bg-blue-100 px-2 py-0.5 text-xs font-semibold text-blue-700">Stok: <span x-text="historyProduct.jumlah" class="ml-1"></span></span>
                </p>
                <div class="mt-5 max-h-96 overflow-y-auto rounded-xl border border-gray-100">
                    <table class="w-full text-left text-sm">
                        <thead class="sticky top-0 bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Tanggal</th>
                                <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Tipe</th>
                                <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 text-center">Qty</th>
                                <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Keterangan</th>
                                <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Referensi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <template x-for="m in historyMovements" :key="m.id">
                                <tr class="transition hover:bg-gray-50">
                                    <td class="px-4 py-3 text-gray-600 whitespace-nowrap" x-text="new Date(m.created_at).toLocaleDateString('id-ID', {day:'2-digit',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'})"></td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center rounded-lg px-2 py-0.5 text-xs font-semibold" :class="m.type === 'masuk' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'" x-text="m.type === 'masuk' ? '▲ Masuk' : '▼ Keluar'"></span>
                                    </td>
                                    <td class="px-4 py-3 text-center font-semibold" :class="m.type === 'masuk' ? 'text-green-600' : 'text-red-600'" x-text="(m.type === 'masuk' ? '+' : '-') + m.qty"></td>
                                    <td class="px-4 py-3 text-gray-600" x-text="m.keterangan || '-'"></td>
                                    <td class="px-4 py-3 text-gray-500 text-xs" x-text="m.reference || '-'"></td>
                                </tr>
                            </template>
                            <template x-if="historyMovements.length === 0">
                                <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">Belum ada history stok.</td></tr>
                            </template>
                        </tbody>
                    </table>
                </div>
                <div class="mt-6">
                    <button @click="showHistory = false" class="rounded-xl border border-gray-200 px-6 py-2.5 text-sm font-medium text-gray-600 transition hover:bg-gray-50">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Barcode Modal --}}
    <div x-show="showBarcode" x-cloak class="fixed inset-0 z-50 overflow-y-auto" @keydown.escape.window="showBarcode = false">
        <div x-show="showBarcode" x-transition.opacity class="fixed inset-0 bg-black/50" @click="showBarcode = false"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div x-show="showBarcode" x-transition class="relative w-full max-w-md rounded-2xl bg-white p-8 shadow-xl" @click.stop>
                <div class="mt-5 flex justify-center" id="barcodeContainer">
                    <svg id="barcodeEl"></svg>
                </div>
                <div class="mt-5">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Jumlah Label</label>
                    <div class="flex items-center gap-3">
                        <input type="number" x-model.number="labelQty" min="1" max="100" class="h-10 w-24 rounded-xl border border-gray-200 bg-gray-50 px-4 text-sm text-center outline-none transition focus:border-red-300 focus:ring-2 focus:ring-red-100">
                        <button @click="printBarcode()" class="inline-flex h-10 items-center gap-2 rounded-xl bg-blue-500 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-600">Print Barcode</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
<script>
function productsCrud() {
    return {
        showModal: false, isEdit: false, editId: null, loading: false, errors: {},
        showBarcode: false, barcodeValue: '', labelQty: 1,
        showStockIn: false, stockInId: null, stockInKode: '', stockInForm: { qty: 1, keterangan: '' }, stockInErrors: {},
        showHistory: false, historyProduct: {}, historyMovements: [],
        form: { kode_produk: '', nama_produk: '', category_id: '', harga_pembelian: 0, harga_jual: 0 },

        openCreate() {
            this.isEdit = false; this.editId = null; this.errors = {};
            this.form = { kode_produk: '', nama_produk: '', category_id: '', harga_pembelian: 0, harga_jual: 0 };
            this.showModal = true;
        },
        async openEdit(id) {
            this.isEdit = true; this.editId = id; this.errors = {}; this.loading = true; this.showModal = true;
            const res = await fetch(`/admin/products/${id}`, { headers: { 'Accept': 'application/json' } });
            const data = await res.json();
            this.form = { kode_produk: data.kode_produk, nama_produk: data.nama_produk, category_id: String(data.category_id), harga_pembelian: data.harga_pembelian, harga_jual: data.harga_jual };
            this.loading = false;
        },
        async save() {
            this.loading = true; this.errors = {};
            const url = this.isEdit ? `/admin/products/${this.editId}` : '/admin/products';
            const res = await fetch(url, {
                method: this.isEdit ? 'PUT' : 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                body: JSON.stringify(this.form)
            });
            if (!res.ok) { const d = await res.json(); this.errors = d.errors || {}; this.loading = false; return; }
            window.location.reload();
        },
        async destroy(id) {
            const result = await Swal.fire({ title: 'Hapus produk?', text: 'Data yang dihapus tidak dapat dikembalikan.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#6b7280', confirmButtonText: 'Ya, hapus!', cancelButtonText: 'Batal' });
            if (!result.isConfirmed) return;
            await fetch(`/admin/products/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
            });
            window.location.reload();
        },

        openStockIn(id, kode) {
            this.stockInId = id;
            this.stockInKode = kode;
            this.stockInForm = { qty: 1, keterangan: '' };
            this.stockInErrors = {};
            this.showStockIn = true;
        },
        async saveStockIn() {
            this.loading = true; this.stockInErrors = {};
            const res = await fetch(`/admin/products/${this.stockInId}/stock-in`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                body: JSON.stringify(this.stockInForm)
            });
            if (!res.ok) { const d = await res.json(); this.stockInErrors = d.errors || {}; this.loading = false; return; }
            window.location.reload();
        },

        async openHistory(id) {
            this.historyProduct = {}; this.historyMovements = []; this.showHistory = true;
            const res = await fetch(`/admin/products/${id}/stock-history`, { headers: { 'Accept': 'application/json' } });
            const data = await res.json();
            this.historyProduct = data.product;
            this.historyMovements = data.movements;
        },

        openBarcode(kode) {
            this.barcodeValue = kode;
            this.labelQty = 1;
            this.showBarcode = true;
            this.$nextTick(() => {
                JsBarcode('#barcodeEl', kode, { format: 'CODE128', width: 2, height: 70, displayValue: true, fontSize: 14, margin: 10 });
            });
        },
        printBarcode() {
            const svg = document.getElementById('barcodeEl').outerHTML;
            const w = window.open('', '_blank');
            w.document.write(`<!DOCTYPE html><html><head><title>Barcode ${this.barcodeValue}</title><style>body{margin:0;padding:20px;text-align:center;font-family:sans-serif}.label{display:inline-block;margin:5px;page-break-inside:avoid}@media print{@page{margin:5mm}}</style></head><body>`);
            for (let i = 0; i < this.labelQty; i++) { w.document.write(`<div class="label">${svg}</div>`); }
            w.document.write('</body></html>');
            w.document.close();
            w.onload = () => { w.print(); };
        }
    }
}
</script>
@endpush
