@extends('layouts.produksi')

@section('title', 'Buat Packing Kemas')

@section('content')
<div x-data="packingCreate()" class="space-y-6">
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('produksi.dashboard') }}" class="transition hover:text-gray-700">Home</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('produksi.packing.index') }}" class="transition hover:text-gray-700">Packing Kemas</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="font-medium text-gray-900">Buat Baru</span>
    </nav>

    @include('partials.flash')

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6">
            <template x-for="(row, rIdx) in items" :key="row._key">
                <div class="rounded-xl border border-gray-100 bg-white shadow-sm overflow-visible">
                    <div class="flex items-center justify-between border-b border-gray-100 bg-gray-50/50 px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-100 text-sm font-bold text-red-600" x-text="rIdx + 1"></div>
                            <div>
                                <h3 class="text-sm font-bold text-gray-900">Produk</h3>
                                <p class="text-xs text-gray-500">Pilih produk, jumlah, BSP dan kemasan untuk produk ini</p>
                            </div>
                        </div>
                        <button x-show="items.length > 1" @click="removeItem(rIdx)" type="button"
                                class="flex h-8 w-8 items-center justify-center rounded-lg text-red-400 transition hover:bg-red-50 hover:text-red-600">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="p-6 space-y-5">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                            <div class="sm:col-span-2">
                                <label class="mb-1.5 block text-xs font-semibold text-gray-600">Produk <span class="text-red-500">*</span></label>
                                <select :id="'product-select-' + row._key" :data-key="row._key" placeholder="Cari produk..."></select>
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-semibold text-gray-600">Jumlah (unit) <span class="text-red-500">*</span></label>
                                <input type="number" x-model.number="row.quantity" min="1" class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm text-center focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20">
                            </div>
                        </div>

                        {{-- BSP untuk produk ini --}}
                        <div>
                            <label class="mb-2 block text-xs font-semibold text-gray-600">Bahan Siap Produksi</label>
                            <div class="flex flex-wrap items-end gap-3">
                                <div class="min-w-[200px] flex-1">
                                    <select :id="'bsp-select-' + row._key" :data-key="row._key" placeholder="Cari BSP..."></select>
                                </div>
                                <div class="w-24">
                                    <input type="number" x-model.number="row._bspQty" min="1" class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-center text-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20">
                                </div>
                                <button type="button" @click="addBspFor(rIdx)" class="flex h-[42px] items-center gap-2 rounded-xl bg-red-500 px-4 text-sm font-semibold text-white transition hover:bg-red-600">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                    Tambah
                                </button>
                            </div>
                            <div class="mt-3 overflow-x-auto rounded-xl border border-gray-100">
                                <table class="w-full text-left text-sm">
                                    <thead><tr class="bg-gray-50">
                                        <th class="px-4 py-2 text-xs font-bold uppercase text-gray-500">Kode</th>
                                        <th class="px-4 py-2 text-xs font-bold uppercase text-gray-500">Nama</th>
                                        <th class="px-4 py-2 text-xs font-bold uppercase text-gray-500 text-center">Stok</th>
                                        <th class="px-4 py-2 text-xs font-bold uppercase text-gray-500 text-center">Jumlah</th>
                                        <th class="px-4 py-2 w-10"></th>
                                    </tr></thead>
                                    <tbody class="divide-y divide-gray-100">
                                        <template x-for="(b, bIdx) in row.bsp" :key="bIdx">
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-2 font-medium" x-text="b.kode"></td>
                                                <td class="px-4 py-2 text-gray-700" x-text="b.nama"></td>
                                                <td class="px-4 py-2 text-center"><span class="text-xs font-medium text-green-700" x-text="b.stok + ' unit'"></span></td>
                                                <td class="px-4 py-2 text-center">
                                                    <input type="number" x-model.number="b.qty" :max="b.stok" min="1" class="w-16 rounded border border-gray-200 px-2 py-1 text-center text-sm">
                                                </td>
                                                <td class="px-4 py-2">
                                                    <button type="button" @click="row.bsp.splice(bIdx, 1)" class="text-red-400 hover:text-red-600">
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </button>
                                                </td>
                                            </tr>
                                        </template>
                                        <tr x-show="row.bsp.length === 0"><td colspan="5" class="px-4 py-4 text-center text-gray-400 text-xs">Belum ada BSP</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Kemasan untuk produk ini --}}
                        <div>
                            <label class="mb-2 block text-xs font-semibold text-gray-600">Kemasan</label>
                            <div class="flex flex-wrap items-end gap-3">
                                <div class="min-w-[200px] flex-1">
                                    <select :id="'kemasan-select-' + row._key" :data-key="row._key" placeholder="Pilih kemasan..."></select>
                                </div>
                                <div class="w-24">
                                    <input type="number" x-model.number="row._kemasanQty" min="1" class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-center text-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20">
                                </div>
                                <button type="button" @click="addKemasanFor(rIdx)" class="flex h-[42px] items-center gap-2 rounded-xl bg-red-500 px-4 text-sm font-semibold text-white transition hover:bg-red-600">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                    Tambah
                                </button>
                            </div>
                            <div class="mt-3 overflow-x-auto rounded-xl border border-gray-100">
                                <table class="w-full text-left text-sm">
                                    <thead><tr class="bg-gray-50">
                                        <th class="px-4 py-2 text-xs font-bold uppercase text-gray-500">Kode</th>
                                        <th class="px-4 py-2 text-xs font-bold uppercase text-gray-500">Nama</th>
                                        <th class="px-4 py-2 text-xs font-bold uppercase text-gray-500 text-center">Stok</th>
                                        <th class="px-4 py-2 text-xs font-bold uppercase text-gray-500 text-center">Jumlah</th>
                                        <th class="px-4 py-2 w-10"></th>
                                    </tr></thead>
                                    <tbody class="divide-y divide-gray-100">
                                        <template x-for="(k, kIdx) in row.kemasan" :key="kIdx">
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-2 font-medium" x-text="k.kode"></td>
                                                <td class="px-4 py-2 text-gray-700" x-text="k.nama"></td>
                                                <td class="px-4 py-2 text-center text-gray-500" x-text="k.stok"></td>
                                                <td class="px-4 py-2 text-center">
                                                    <input type="number" x-model.number="k.qty" :max="k.stok" min="1" class="w-16 rounded border border-gray-200 px-2 py-1 text-center text-sm">
                                                </td>
                                                <td class="px-4 py-2">
                                                    <button type="button" @click="row.kemasan.splice(kIdx, 1)" class="text-red-400 hover:text-red-600">
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </button>
                                                </td>
                                            </tr>
                                        </template>
                                        <tr x-show="row.kemasan.length === 0"><td colspan="5" class="px-4 py-4 text-center text-gray-400 text-xs">Belum ada kemasan</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <button @click="addItem()" type="button"
                    class="flex w-full items-center justify-center gap-2 rounded-xl border-2 border-dashed border-gray-200 py-4 text-sm font-semibold text-gray-500 transition hover:border-red-300 hover:text-red-500">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Tambah Produk Lainnya
            </button>
        </div>

        <div class="space-y-6">
            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900">Ringkasan</h2>
                <div class="mt-4 space-y-3">
                    <div class="flex items-center justify-between rounded-lg bg-blue-50 px-4 py-3">
                        <span class="text-sm font-medium text-blue-700">Jumlah Produk</span>
                        <span class="text-sm font-bold text-blue-800" x-text="items.length + ' item'"></span>
                    </div>
                    <div class="flex items-center justify-between rounded-lg bg-green-50 px-4 py-3">
                        <span class="text-sm font-medium text-green-700">Total BSP</span>
                        <span class="text-sm font-bold text-green-800" x-text="totalBspUnit() + ' unit'"></span>
                    </div>
                    <div class="flex items-center justify-between rounded-lg bg-purple-50 px-4 py-3">
                        <span class="text-sm font-medium text-purple-700">Total Kemasan</span>
                        <span class="text-sm font-bold text-purple-800" x-text="totalKemasanItem() + ' item'"></span>
                    </div>
                </div>

                <div class="mt-5">
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700">Catatan (opsional)</label>
                    <textarea x-model="formCatatan" rows="3" class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm transition focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20" placeholder="Catatan..."></textarea>
                </div>

                <div class="mt-5 space-y-3">
                    <button type="button" @click="submitPacking()" :disabled="loading || !canSubmit()"
                            class="flex w-full items-center justify-center gap-2 rounded-xl bg-red-500 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-red-600 disabled:cursor-not-allowed disabled:opacity-50">
                        <svg x-show="!loading" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <svg x-show="loading" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        <span x-text="loading ? 'Menyimpan...' : 'Simpan Packing'"></span>
                    </button>
                    <a href="{{ route('produksi.packing.index') }}" class="flex w-full items-center justify-center rounded-xl border border-gray-200 px-5 py-3 text-sm font-medium text-gray-600 transition hover:bg-gray-50">Batal</a>
                </div>
            </div>

            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                <p class="text-sm font-semibold text-amber-800">Produk belum masuk stok</p>
                <p class="mt-1 text-xs text-amber-700">Hasil packing hanya tercatat. Stok produk akan diinput/disesuaikan terpisah.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function packingCreate() {
    const bspList = @json($bspList);
    const kemasans = @json($kemasans);
    const products = @json($products);
    const storeUrl = '{{ route("produksi.packing.store") }}';
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;

    return {
        loading: false,
        items: [],
        formCatatan: '',
        _selects: {},

        init() {
            this.$nextTick(() => this.addItem());
        },

        addItem() {
            const key = 'row_' + Date.now() + '_' + Math.random().toString(36).substr(2, 5);
            this.items.push({
                _key: key,
                product_id: null,
                quantity: 1,
                bsp: [],
                kemasan: [],
                _bspQty: 1,
                _kemasanQty: 1,
            });
            setTimeout(() => this.initSelectsFor(key), 150);
        },

        removeItem(idx) {
            const row = this.items[idx];
            if (this._selects[row._key]) {
                this._selects[row._key].product?.destroy();
                this._selects[row._key].bsp?.destroy();
                this._selects[row._key].kemasan?.destroy();
                delete this._selects[row._key];
            }
            this.items.splice(idx, 1);
        },

        initSelectsFor(key, retries = 5) {
            const row = this.items.find(r => r._key === key);
            if (!row) return;

            const productEl = document.getElementById('product-select-' + key);
            const bspEl = document.getElementById('bsp-select-' + key);
            const kemasanEl = document.getElementById('kemasan-select-' + key);

            if (!productEl || !bspEl || !kemasanEl) {
                if (retries > 0) setTimeout(() => this.initSelectsFor(key, retries - 1), 150);
                return;
            }

            const productInstance = new TomSelect(productEl, {
                valueField: 'id',
                labelField: 'nama_produk',
                searchField: ['nama_produk', 'kode_produk'],
                placeholder: 'Cari produk...',
                dropdownParent: 'body',
                options: products.map(p => ({ id: p.id, kode_produk: p.kode_produk, nama_produk: p.nama_produk })),
                onChange: (val) => { row.product_id = val ? parseInt(val) : null; },
                render: {
                    option: (data, escape) => `<div>[${escape(data.kode_produk)}] ${escape(data.nama_produk)}</div>`,
                    item: (data, escape) => `<div>[${escape(data.kode_produk)}] ${escape(data.nama_produk)}</div>`,
                }
            });

            const bspInstance = new TomSelect(bspEl, {
                valueField: 'id',
                labelField: 'label',
                searchField: ['label', 'kode'],
                placeholder: 'Cari BSP...',
                dropdownParent: 'body',
                options: bspList.map(b => ({ id: b.id, kode: b.kode, nama: b.nama, stok: b.stok, label: `[${b.kode}] ${b.nama}` })),
                render: {
                    option: (data, escape) => `<div class="flex justify-between gap-3"><span>[${escape(data.kode)}] ${escape(data.nama)}</span><span class="text-xs text-gray-400">Stok: ${data.stok}</span></div>`,
                    item: (data, escape) => `<div>[${escape(data.kode)}] ${escape(data.nama)}</div>`,
                }
            });

            const kemasanInstance = new TomSelect(kemasanEl, {
                valueField: 'id',
                labelField: 'nama',
                searchField: ['nama', 'kode'],
                placeholder: 'Pilih kemasan...',
                dropdownParent: 'body',
                options: kemasans.map(k => ({ id: k.id, kode: k.kode, nama: k.nama, stok: k.stok })),
                render: {
                    option: (data, escape) => `<div class="flex justify-between gap-3"><span>[${escape(data.kode)}] ${escape(data.nama)}</span><span class="text-xs text-gray-400">Stok: ${data.stok}</span></div>`,
                    item: (data, escape) => `<div>[${escape(data.kode)}] ${escape(data.nama)}</div>`,
                }
            });

            this._selects[key] = { product: productInstance, bsp: bspInstance, kemasan: kemasanInstance };
        },

        addBspFor(rIdx) {
            const row = this.items[rIdx];
            const sel = this._selects[row._key]?.bsp;
            const val = sel?.getValue();
            if (!val) return;
            const found = bspList.find(b => b.id == val);
            if (!found) return;
            if (row._bspQty > found.stok) {
                Swal.fire('Stok Tidak Cukup', `Stok BSP ${found.nama} hanya ${found.stok}.`, 'warning');
                return;
            }
            const exists = row.bsp.find(b => b.id === found.id);
            if (exists) {
                exists.qty = Math.min((exists.qty || 0) + row._bspQty, found.stok);
            } else {
                row.bsp.push({ id: found.id, kode: found.kode, nama: found.nama, stok: found.stok, qty: row._bspQty });
            }
            row._bspQty = 1;
            sel.clear();
        },

        addKemasanFor(rIdx) {
            const row = this.items[rIdx];
            const sel = this._selects[row._key]?.kemasan;
            const val = sel?.getValue();
            if (!val) return;
            const k = kemasans.find(x => x.id == val);
            if (!k) return;
            const existing = row.kemasan.find(x => x.id === k.id);
            if (existing) {
                existing.qty = Math.min((existing.qty || 0) + row._kemasanQty, k.stok);
            } else {
                row.kemasan.push({ id: k.id, kode: k.kode, nama: k.nama, stok: k.stok, qty: row._kemasanQty });
            }
            row._kemasanQty = 1;
            sel.clear();
        },

        totalBspUnit() {
            return this.items.reduce((sum, row) => sum + row.bsp.reduce((s, b) => s + (b.qty || 0), 0), 0);
        },

        totalKemasanItem() {
            return this.items.reduce((sum, row) => sum + row.kemasan.reduce((s, k) => s + (k.qty || 0), 0), 0);
        },

        canSubmit() {
            if (this.items.length === 0) return false;
            return this.items.every(row => row.product_id && row.quantity >= 1);
        },

        async submitPacking() {
            if (!this.canSubmit()) return;

            const result = await Swal.fire({
                title: 'Simpan Packing?',
                html: `<p>${this.items.length} produk akan dicatat. BSP dan Kemasan akan dikurangi stok-nya.</p><p class="mt-2 text-gray-500">Produk yang dihasilkan <strong>belum masuk stok</strong>.</p>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Simpan',
                cancelButtonText: 'Batal'
            });
            if (!result.isConfirmed) return;

            this.loading = true;

            const payload = {
                catatan: this.formCatatan || null,
                items: this.items.map(row => ({
                    product_id: row.product_id,
                    quantity: row.quantity,
                    bsp: row.bsp.map(b => ({ id: b.id, qty: b.qty })),
                    kemasan: row.kemasan.map(k => ({ id: k.id, qty: k.qty })),
                })),
            };

            const res = await fetch(storeUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                body: JSON.stringify(payload)
            });

            if (!res.ok) {
                const d = await res.json().catch(() => ({}));
                this.loading = false;
                Swal.fire('Error', d.message || 'Gagal menyimpan.', 'error');
                return;
            }

            const data = await res.json();
            await Swal.fire('Berhasil', data.message || 'Packing disimpan.', 'success');
            window.location.href = data.redirect || '{{ route("produksi.packing.index") }}';
        }
    };
}
</script>
@endpush
