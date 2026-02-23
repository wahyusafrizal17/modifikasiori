@extends('layouts.admin')

@section('title', 'Buat Work Order')

@section('content')
<div x-data="serviceOrderForm()" class="space-y-6">
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.dashboard') }}" class="transition hover:text-gray-700">Home</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('admin.service-orders.index') }}" class="transition hover:text-gray-700">Work Order</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="font-medium text-gray-900">Buat Baru</span>
    </nav>

    <form action="{{ route('admin.service-orders.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            {{-- Left: Basic Info --}}
            <div class="space-y-6 lg:col-span-2">
                <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900">Informasi Servis</h3>
                    <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">Pelanggan <span class="text-red-500">*</span></label>
                            <select x-ref="pelangganSelect" name="pelanggan_id" placeholder="Ketik untuk mencari pelanggan...">
                                @foreach($pelanggans as $p)
                                <option value="{{ $p->id }}">{{ $p->nama }}</option>
                                @endforeach
                            </select>
                            @error('pelanggan_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">Kendaraan <span class="text-red-500">*</span></label>
                            <select name="kendaraan_id" x-model="kendaraan_id" class="h-10 w-full rounded-xl border border-gray-200 bg-gray-50 px-4 text-sm outline-none transition focus:border-red-300 focus:ring-2 focus:ring-red-100">
                                <option value="">— Pilih Kendaraan —</option>
                                <template x-for="k in kendaraans" :key="k.id">
                                    <option :value="k.id" x-text="`${k.nomor_polisi} - ${k.merk} ${k.tipe || ''}`"></option>
                                </template>
                            </select>
                            @error('kendaraan_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">Mekanik</label>
                            <select x-ref="mekanikSelect" name="mekanik_id" placeholder="Ketik untuk mencari mekanik...">
                                @foreach($mekaniks as $m)
                                <option value="{{ $m->id }}" {{ old('mekanik_id') == $m->id ? 'selected' : '' }}>{{ $m->nama }} ({{ $m->spesialisasi ?? '-' }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">Tanggal Masuk <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_masuk" value="{{ old('tanggal_masuk', now()->toDateString()) }}" class="h-10 w-full rounded-xl border border-gray-200 bg-gray-50 px-4 text-sm outline-none transition focus:border-red-300 focus:ring-2 focus:ring-red-100">
                            @error('tanggal_masuk') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">Next Service Date</label>
                            <input type="date" name="next_service_date" value="{{ old('next_service_date') }}" class="h-10 w-full rounded-xl border border-gray-200 bg-gray-50 px-4 text-sm outline-none transition focus:border-red-300 focus:ring-2 focus:ring-red-100">
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Keluhan</label>
                        <textarea name="keluhan" rows="3" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm outline-none transition focus:border-red-300 focus:ring-2 focus:ring-red-100">{{ old('keluhan') }}</textarea>
                    </div>
                </div>

                {{-- Jasa Servis --}}
                <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-900">Jasa Servis</h3>
                        <button type="button" @click="addJasa()" class="inline-flex items-center gap-1 rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-600 transition hover:bg-blue-100">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            Tambah Jasa
                        </button>
                    </div>
                    <div class="mt-4 space-y-3">
                        <template x-for="(item, index) in jasaItems" :key="item._uid">
                            <div class="flex items-start gap-3 rounded-xl border border-gray-100 bg-gray-50 p-4">
                                <div class="flex-1" x-init="$nextTick(() => initJasaSelect($el.querySelector('select'), index))">
                                    <select :id="`jasa_select_${item._uid}`" :name="`jasa_items[${index}][jasa_servis_id]`" placeholder="Ketik untuk mencari jasa...">
                                        @foreach($jasaServis as $j)
                                        <option value="{{ $j->id }}">{{ $j->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="w-40">
                                    <input type="number" :name="`jasa_items[${index}][biaya]`" x-model.number="item.biaya" placeholder="Biaya" class="h-10 w-full rounded-xl border border-gray-200 bg-white px-4 text-sm outline-none text-right">
                                </div>
                                <button type="button" @click="removeJasa(index)" class="mt-1.5 text-red-400 transition hover:text-red-600">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </template>
                        <p x-show="jasaItems.length === 0" class="py-4 text-center text-sm text-gray-400">Belum ada jasa ditambahkan.</p>
                    </div>
                </div>

                {{-- Sparepart --}}
                <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-900">Sparepart</h3>
                        <button type="button" @click="addProduct()" class="inline-flex items-center gap-1 rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-600 transition hover:bg-blue-100">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            Tambah Sparepart
                        </button>
                    </div>
                    <div class="mt-4 space-y-3">
                        <template x-for="(item, index) in productItems" :key="item._uid">
                            <div class="flex items-start gap-3 rounded-xl border border-gray-100 bg-gray-50 p-4">
                                <div class="flex-1" x-init="$nextTick(() => initProductSelect($el.querySelector('select'), index))">
                                    <select :id="`product_select_${item._uid}`" :name="`product_items[${index}][product_id]`" placeholder="Ketik untuk mencari produk...">
                                        @foreach($products as $prod)
                                        <option value="{{ $prod->id }}">{{ $prod->nama_produk }} (Stok: {{ $prod->jumlah }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="w-20">
                                    <input type="number" :name="`product_items[${index}][qty]`" x-model.number="item.qty" min="1" placeholder="Qty" class="h-10 w-full rounded-xl border border-gray-200 bg-white px-3 text-sm outline-none text-center">
                                </div>
                                <div class="w-36">
                                    <input type="number" :name="`product_items[${index}][harga]`" x-model.number="item.harga" placeholder="Harga" class="h-10 w-full rounded-xl border border-gray-200 bg-white px-4 text-sm outline-none text-right">
                                </div>
                                <button type="button" @click="removeProduct(index)" class="mt-1.5 text-red-400 transition hover:text-red-600">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </template>
                        <p x-show="productItems.length === 0" class="py-4 text-center text-sm text-gray-400">Belum ada sparepart ditambahkan.</p>
                    </div>
                </div>
            </div>

            {{-- Right: Summary --}}
            <div>
                <div class="sticky top-8 rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900">Ringkasan Biaya</h3>
                    <div class="mt-5 space-y-3 text-sm">
                        <div class="flex justify-between"><span class="text-gray-500">Total Jasa</span><span class="font-semibold text-gray-900" x-text="'Rp ' + totalJasa.toLocaleString('id-ID')"></span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Total Sparepart</span><span class="font-semibold text-gray-900" x-text="'Rp ' + totalSparepart.toLocaleString('id-ID')"></span></div>
                        <hr class="border-gray-100">
                        <div class="flex justify-between text-base"><span class="font-bold text-gray-900">Estimasi Total</span><span class="font-bold text-red-600" x-text="'Rp ' + grandTotal.toLocaleString('id-ID')"></span></div>
                    </div>
                    <div class="mt-6 space-y-3">
                        <button type="button" @click="submitForm()" class="w-full rounded-xl bg-red-500 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-red-600">Simpan Work Order</button>
                        <a href="{{ route('admin.service-orders.index') }}" class="block w-full rounded-xl border border-gray-200 py-3 text-center text-sm font-medium text-gray-600 transition hover:bg-gray-50">Batal</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
const jasaDefaults = @json($jasaServis->keyBy('id'));
const productDefaults = @json($products->keyBy('id'));
let _uid = 0;

function serviceOrderForm() {
    return {
        pelanggan_id: '{{ old("pelanggan_id") }}',
        kendaraan_id: '{{ old("kendaraan_id") }}',
        kendaraans: [],
        jasaItems: [],
        productItems: [],
        _tomSelects: {},

        init() {
            this.$nextTick(() => {
                new TomSelect(this.$refs.pelangganSelect, {
                    placeholder: 'Ketik untuk mencari pelanggan...',
                    allowEmptyOption: true,
                    onChange: (value) => { this.pelanggan_id = value; this.loadKendaraan(); }
                });
                new TomSelect(this.$refs.mekanikSelect, {
                    placeholder: 'Ketik untuk mencari mekanik...',
                    allowEmptyOption: true,
                });
            });
        },

        async loadKendaraan() {
            this.kendaraan_id = '';
            if (!this.pelanggan_id) { this.kendaraans = []; return; }
            const res = await fetch(`/admin/api/pelanggans/${this.pelanggan_id}/kendaraans`);
            this.kendaraans = await res.json();
        },

        addJasa() { this.jasaItems.push({ _uid: ++_uid, jasa_servis_id: '', biaya: 0 }); },
        addProduct() { this.productItems.push({ _uid: ++_uid, product_id: '', qty: 1, harga: 0 }); },

        removeJasa(index) {
            const item = this.jasaItems[index];
            const key = 'jasa_' + item._uid;
            if (this._tomSelects[key]) { this._tomSelects[key].destroy(); delete this._tomSelects[key]; }
            this.jasaItems.splice(index, 1);
        },
        removeProduct(index) {
            const item = this.productItems[index];
            const key = 'product_' + item._uid;
            if (this._tomSelects[key]) { this._tomSelects[key].destroy(); delete this._tomSelects[key]; }
            this.productItems.splice(index, 1);
        },

        initJasaSelect(el, index) {
            const item = this.jasaItems[index];
            if (!el || !item) return;
            const key = 'jasa_' + item._uid;
            const self = this;
            this._tomSelects[key] = new TomSelect(el, {
                placeholder: '— Pilih Jasa —',
                allowEmptyOption: true,
                onChange(value) {
                    item.jasa_servis_id = value;
                    if (value && jasaDefaults[value]) item.biaya = Number(jasaDefaults[value].biaya);
                }
            });
        },
        initProductSelect(el, index) {
            const item = this.productItems[index];
            if (!el || !item) return;
            const key = 'product_' + item._uid;
            const self = this;
            this._tomSelects[key] = new TomSelect(el, {
                placeholder: '— Pilih Produk —',
                allowEmptyOption: true,
                onChange(value) {
                    item.product_id = value;
                    if (value && productDefaults[value]) item.harga = Number(productDefaults[value].harga_jual);
                }
            });
        },

        get totalJasa() { return this.jasaItems.reduce((sum, i) => sum + Number(i.biaya || 0), 0); },
        get totalSparepart() { return this.productItems.reduce((sum, i) => sum + (Number(i.qty || 0) * Number(i.harga || 0)), 0); },
        get grandTotal() { return this.totalJasa + this.totalSparepart; },

        async submitForm() {
            for (const item of this.productItems) {
                if (!item.product_id) continue;
                const prod = productDefaults[item.product_id];
                if (prod && Number(prod.jumlah) < Number(item.qty)) {
                    await Swal.fire({ title: 'Stok Tidak Cukup', html: `<b>${prod.nama_produk}</b> hanya tersisa <b>${prod.jumlah}</b> unit, tidak bisa mengambil <b>${item.qty}</b> unit.`, icon: 'error', confirmButtonColor: '#ef4444' });
                    return;
                }
            }
            const result = await Swal.fire({ title: 'Simpan Work Order?', text: 'Pastikan data yang diisi sudah benar.', icon: 'question', showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#6b7280', confirmButtonText: 'Ya, simpan!', cancelButtonText: 'Batal' });
            if (result.isConfirmed) this.$root.querySelector('form').submit();
        },
    }
}
</script>
@endpush
