@extends('layouts.admin')

@section('title', 'Buat Produksi')

@section('content')
<div x-data="productionForm()" class="space-y-6">
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.dashboard') }}" class="transition hover:text-gray-700">Home</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('admin.productions.index') }}" class="transition hover:text-gray-700">Produksi</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="font-medium text-gray-900">Buat Produksi</span>
    </nav>

    @if($errors->any())
    <div class="rounded-xl border border-red-200 bg-red-50 p-4">
        <p class="text-sm font-semibold text-red-700">Terjadi kesalahan:</p>
        <ul class="mt-2 list-disc pl-5 text-sm text-red-600">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.productions.store') }}" method="POST" x-ref="form">
        @csrf
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">
                {{-- Informasi Produksi --}}
                <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900">Informasi Produksi</h3>
                    <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">Tanggal <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal" value="{{ old('tanggal', now()->toDateString()) }}" class="h-10 w-full rounded-xl border border-gray-200 bg-gray-50 px-4 text-sm outline-none transition focus:border-red-400 focus:ring-2 focus:ring-red-400/20">
                            @error('tanggal') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">Catatan</label>
                            <textarea name="catatan" rows="3" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm outline-none transition focus:border-red-400 focus:ring-2 focus:ring-red-400/20">{{ old('catatan') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Bahan Baku --}}
                <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Bahan Baku yang Digunakan</h3>
                            <p class="mt-1 text-sm text-gray-500">Pilih produk bahan baku yang akan digunakan dalam produksi. Stok akan otomatis dikurangi.</p>
                        </div>
                        <button type="button" @click="addMaterial()" class="inline-flex items-center gap-1 rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-600 transition hover:bg-blue-100">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            Tambah Bahan Baku
                        </button>
                    </div>
                    <div class="mt-4 space-y-3">
                        <template x-for="(item, index) in materials" :key="item._matUid">
                            <div class="flex items-start gap-3 rounded-xl border border-gray-100 bg-gray-50 p-4">
                                <div class="flex-1" x-init="$nextTick(() => initMaterialSelect($el.querySelector('select'), index))">
                                    <label class="mb-1 block text-xs font-medium text-gray-500">Produk Bahan Baku</label>
                                    <select :id="`mat_select_${item._matUid}`" placeholder="Ketik untuk mencari produk...">
                                        <option value="">— Pilih Bahan Baku —</option>
                                        @foreach($products as $prod)
                                        <option value="{{ $prod->id }}">{{ $prod->kode_produk }} — {{ $prod->nama_produk }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" :name="`materials[${index}][product_id]`" :value="item.product_id">
                                </div>
                                <div class="w-24">
                                    <label class="mb-1 block text-xs font-medium text-gray-500">Qty</label>
                                    <input type="number" :name="`materials[${index}][qty]`" x-model.number="item.qty" min="1" class="h-10 w-full rounded-xl border border-gray-200 bg-white px-3 text-sm text-center outline-none transition focus:border-red-400 focus:ring-2 focus:ring-red-400/20">
                                </div>
                                <div class="w-28">
                                    <label class="mb-1 block text-xs font-medium text-gray-500">Stok Tersedia</label>
                                    <input type="text" :value="item.product_id && productsData[item.product_id] ? productsData[item.product_id].jumlah : '-'" readonly class="h-10 w-full rounded-xl border border-gray-100 bg-gray-100 px-3 text-sm text-center text-gray-500 outline-none cursor-not-allowed">
                                </div>
                                <button type="button" @click="removeMaterial(index)" class="mt-6 text-red-400 transition hover:text-red-600">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </template>
                        <p x-show="materials.length === 0" class="py-4 text-center text-sm text-gray-400">Belum ada bahan baku ditambahkan.</p>
                    </div>
                </div>

                {{-- Hasil Produksi --}}
                <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Hasil Produksi (Bahan Jadi)</h3>
                            <p class="mt-1 text-sm text-gray-500">Pilih produk hasil produksi. Stok akan ditambahkan setelah lolos QC.</p>
                        </div>
                        <button type="button" @click="addResult()" class="inline-flex items-center gap-1 rounded-lg bg-green-50 px-3 py-1.5 text-xs font-semibold text-green-600 transition hover:bg-green-100">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            Tambah Hasil
                        </button>
                    </div>
                    <div class="mt-4 space-y-3">
                        <template x-for="(item, index) in results" :key="item._resUid">
                            <div class="flex items-start gap-3 rounded-xl border border-gray-100 bg-gray-50 p-4">
                                <div class="flex-1" x-init="$nextTick(() => initResultSelect($el.querySelector('select'), index))">
                                    <label class="mb-1 block text-xs font-medium text-gray-500">Produk Hasil</label>
                                    <select :id="`res_select_${item._resUid}`" placeholder="Ketik untuk mencari produk...">
                                        <option value="">— Pilih Produk Hasil —</option>
                                        @foreach($products as $prod)
                                        <option value="{{ $prod->id }}">{{ $prod->kode_produk }} — {{ $prod->nama_produk }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" :name="`results[${index}][product_id]`" :value="item.product_id">
                                </div>
                                <div class="w-24">
                                    <label class="mb-1 block text-xs font-medium text-gray-500">Qty</label>
                                    <input type="number" :name="`results[${index}][qty]`" x-model.number="item.qty" min="1" class="h-10 w-full rounded-xl border border-gray-200 bg-white px-3 text-sm text-center outline-none transition focus:border-red-400 focus:ring-2 focus:ring-red-400/20">
                                </div>
                                <button type="button" @click="removeResult(index)" class="mt-6 text-red-400 transition hover:text-red-600">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </template>
                        <p x-show="results.length === 0" class="py-4 text-center text-sm text-gray-400">Belum ada hasil produksi ditambahkan.</p>
                    </div>
                </div>
            </div>

            {{-- Right: Summary --}}
            <div>
                <div class="sticky top-8 rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900">Ringkasan</h3>
                    <div class="mt-5 space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Bahan Baku</span>
                            <span class="font-semibold text-gray-900" x-text="materials.length + ' items'"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Hasil Produksi</span>
                            <span class="font-semibold text-gray-900" x-text="results.length + ' items'"></span>
                        </div>
                    </div>
                    <div class="mt-6 space-y-3">
                        <button type="button" @click="submitForm()" class="w-full rounded-xl bg-red-500 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-red-600">Simpan</button>
                        <a href="{{ route('admin.productions.index') }}" class="block w-full rounded-xl border border-gray-200 py-3 text-center text-sm font-medium text-gray-600 transition hover:bg-gray-50">Batal</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
const productsData = @json($products->keyBy('id'));
let _matUid = 0;
let _resUid = 0;

function productionForm() {
    return {
        materials: [],
        results: [],
        productsData: productsData,
        _matTomSelects: {},
        _resTomSelects: {},

        addMaterial() {
            this.materials.push({ _matUid: ++_matUid, product_id: '', qty: 1 });
        },

        removeMaterial(index) {
            const item = this.materials[index];
            const key = 'mat_' + item._matUid;
            if (this._matTomSelects[key]) {
                this._matTomSelects[key].destroy();
                delete this._matTomSelects[key];
            }
            this.materials.splice(index, 1);
        },

        addResult() {
            this.results.push({ _resUid: ++_resUid, product_id: '', qty: 1 });
        },

        removeResult(index) {
            const item = this.results[index];
            const key = 'res_' + item._resUid;
            if (this._resTomSelects[key]) {
                this._resTomSelects[key].destroy();
                delete this._resTomSelects[key];
            }
            this.results.splice(index, 1);
        },

        initMaterialSelect(el, index) {
            const item = this.materials[index];
            if (!el || !item) return;
            const key = 'mat_' + item._matUid;
            this._matTomSelects[key] = new TomSelect(el, {
                placeholder: '— Pilih Bahan Baku —',
                allowEmptyOption: true,
                dropdownParent: 'body',
                onChange: (value) => {
                    item.product_id = value;
                }
            });
        },

        initResultSelect(el, index) {
            const item = this.results[index];
            if (!el || !item) return;
            const key = 'res_' + item._resUid;
            this._resTomSelects[key] = new TomSelect(el, {
                placeholder: '— Pilih Produk Hasil —',
                allowEmptyOption: true,
                dropdownParent: 'body',
                onChange: (value) => {
                    item.product_id = value;
                }
            });
        },

        async submitForm() {
            if (this.materials.length === 0) {
                await Swal.fire({ title: 'Perhatian', text: 'Tambahkan minimal 1 bahan baku.', icon: 'warning', confirmButtonColor: '#ef4444' });
                return;
            }
            if (this.results.length === 0) {
                await Swal.fire({ title: 'Perhatian', text: 'Tambahkan minimal 1 hasil produksi.', icon: 'warning', confirmButtonColor: '#ef4444' });
                return;
            }

            for (const item of this.materials) {
                if (!item.product_id) continue;
                const prod = this.productsData[item.product_id];
                if (prod && Number(prod.jumlah) < Number(item.qty)) {
                    await Swal.fire({
                        title: 'Stok Tidak Cukup',
                        html: `<b>${prod.nama_produk}</b> hanya tersisa <b>${prod.jumlah}</b> unit, tidak bisa menggunakan <b>${item.qty}</b> unit.`,
                        icon: 'error',
                        confirmButtonColor: '#ef4444'
                    });
                    return;
                }
            }

            const result = await Swal.fire({
                title: 'Simpan Produksi?',
                html: 'Stok bahan baku akan <b>dikurangi</b> sesuai jumlah yang digunakan. Pastikan data sudah benar.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, simpan!',
                cancelButtonText: 'Batal'
            });
            if (result.isConfirmed) {
                const form = this.$refs.form;
                this.materials.forEach((item, index) => {
                    const hiddenInput = form.querySelector(`input[name="materials[${index}][product_id]"]`);
                    if (hiddenInput) hiddenInput.value = item.product_id;
                });
                this.results.forEach((item, index) => {
                    const hiddenInput = form.querySelector(`input[name="results[${index}][product_id]"]`);
                    if (hiddenInput) hiddenInput.value = item.product_id;
                });
                this.$nextTick(() => form.submit());
            }
        }
    }
}
</script>
@endpush
