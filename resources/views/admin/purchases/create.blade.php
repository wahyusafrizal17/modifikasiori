@extends('layouts.admin')

@section('title', 'Buat Pembelian')

@section('content')
<div x-data="purchaseForm()" class="space-y-6">
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.dashboard') }}" class="transition hover:text-gray-700">Home</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('admin.purchases.index') }}" class="transition hover:text-gray-700">Pembelian</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="font-medium text-gray-900">Buat Pembelian</span>
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

    <form action="{{ route('admin.purchases.store') }}" method="POST" x-ref="form">
        @csrf
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            {{-- Left Column --}}
            <div class="space-y-6 lg:col-span-2">
                {{-- Informasi Pembelian --}}
                <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900">Informasi Pembelian</h3>
                    <div class="mt-5 space-y-4">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">Supplier <span class="text-red-500">*</span></label>
                            <select x-ref="supplierSelect" name="supplier_id" placeholder="Ketik untuk mencari supplier...">
                                <option value="">— Pilih Supplier —</option>
                                @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->nama }}</option>
                                @endforeach
                            </select>
                            @error('supplier_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">Tanggal <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal" value="{{ old('tanggal', now()->toDateString()) }}" class="h-10 w-full rounded-xl border border-gray-200 bg-gray-50 px-4 text-sm outline-none transition focus:border-red-400 focus:ring-2 focus:ring-red-400/20">
                            @error('tanggal') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">Catatan</label>
                            <textarea name="catatan" rows="3" placeholder="Catatan tambahan (opsional)" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm outline-none transition focus:border-red-400 focus:ring-2 focus:ring-red-400/20">{{ old('catatan') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Item Pembelian --}}
                <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-900">Item Pembelian</h3>
                        <button type="button" @click="addItem()" class="inline-flex items-center gap-1 rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-600 transition hover:bg-blue-100">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            Tambah Item
                        </button>
                    </div>

                    <div class="mt-4 rounded-xl border border-gray-100">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Produk</th>
                                    <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 w-24 text-center">Qty</th>
                                    <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 w-40 text-right">Harga Satuan</th>
                                    <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 w-40 text-right">Subtotal</th>
                                    <th class="px-4 py-3 w-12"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <template x-for="(item, index) in items" :key="item._uid">
                                    <tr>
                                        <td class="px-4 py-3">
                                            <div x-init="$nextTick(() => initProductSelect($el.querySelector('select'), item))">
                                                <select :id="`product_select_${item._uid}`" placeholder="Pilih produk...">
                                                    <option value="">— Pilih Produk —</option>
                                                    @foreach($products as $prod)
                                                    <option value="{{ $prod->id }}">{{ $prod->kode_produk }} — {{ $prod->nama_produk }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <input type="hidden" :name="`items[${index}][product_id]`" :value="item.product_id">
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="number" :name="`items[${index}][qty]`" x-model.number="item.qty" min="1" class="h-10 w-full rounded-xl border border-gray-200 bg-white px-3 text-sm text-center outline-none transition focus:border-red-400 focus:ring-2 focus:ring-red-400/20">
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="number" :name="`items[${index}][harga_satuan]`" x-model.number="item.harga_satuan" min="0" class="h-10 w-full rounded-xl border border-gray-200 bg-white px-3 text-sm text-right outline-none transition focus:border-red-400 focus:ring-2 focus:ring-red-400/20">
                                        </td>
                                        <td class="px-4 py-3 text-right font-medium text-gray-900" x-text="'Rp ' + (item.qty * item.harga_satuan).toLocaleString('id-ID')"></td>
                                        <td class="px-4 py-3 text-center">
                                            <button type="button" @click="removeItem(item._uid)" class="text-red-400 transition hover:text-red-600">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                        <p x-show="items.length === 0" class="py-8 text-center text-sm text-gray-400">Belum ada item ditambahkan.</p>
                    </div>
                </div>
            </div>

            {{-- Right Column: Summary --}}
            <div>
                <div class="sticky top-8 rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900">Ringkasan</h3>
                    <div class="mt-5 space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Total Items</span>
                            <span class="font-semibold text-gray-900" x-text="items.length + ' item'"></span>
                        </div>
                        <hr class="border-gray-100">
                        <div class="flex justify-between text-base">
                            <span class="font-bold text-gray-900">Grand Total</span>
                            <span class="font-bold text-red-600" x-text="'Rp ' + grandTotal.toLocaleString('id-ID')"></span>
                        </div>
                    </div>
                    <div class="mt-6 space-y-3">
                        <button type="button" @click="submitForm()" class="w-full rounded-xl bg-red-500 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-red-600">Simpan</button>
                        <a href="{{ route('admin.purchases.index') }}" class="block w-full rounded-xl border border-gray-200 py-3 text-center text-sm font-medium text-gray-600 transition hover:bg-gray-50">Batal</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
const productDefaults = @json($products->keyBy('id'));
let _uidCounter = 0;

function purchaseForm() {
    return {
        items: [],
        _tomSelects: {},

        init() {
            this.$nextTick(() => {
                new TomSelect(this.$refs.supplierSelect, {
                    placeholder: 'Ketik untuk mencari supplier...',
                    allowEmptyOption: true,
                });
            });
        },

        addItem() {
            this.items.push({
                _uid: ++_uidCounter,
                product_id: '',
                qty: 1,
                harga_satuan: 0,
            });
        },

        removeItem(uid) {
            const index = this.items.findIndex(i => i._uid === uid);
            if (index === -1) return;
            const key = 'product_' + uid;
            if (this._tomSelects[key]) {
                this._tomSelects[key].destroy();
                delete this._tomSelects[key];
            }
            this.items.splice(index, 1);
        },

        initProductSelect(el, item) {
            if (!el || !item) return;
            const key = 'product_' + item._uid;
            this._tomSelects[key] = new TomSelect(el, {
                placeholder: '— Pilih Produk —',
                allowEmptyOption: true,
                dropdownParent: 'body',
                onChange(value) {
                    item.product_id = value;
                    if (value && productDefaults[value]) {
                        item.harga_satuan = Number(productDefaults[value].harga_pembelian) || 0;
                    }
                }
            });
        },

        get grandTotal() {
            return this.items.reduce((sum, item) => sum + (Number(item.qty || 0) * Number(item.harga_satuan || 0)), 0);
        },

        async submitForm() {
            if (this.items.length === 0) {
                await Swal.fire({
                    title: 'Item Kosong',
                    text: 'Tambahkan minimal 1 item pembelian.',
                    icon: 'warning',
                    confirmButtonColor: '#ef4444',
                });
                return;
            }

            for (let i = 0; i < this.items.length; i++) {
                if (!this.items[i].product_id) {
                    await Swal.fire({
                        title: 'Produk Belum Dipilih',
                        text: `Pilih produk pada item ke-${i + 1}.`,
                        icon: 'warning',
                        confirmButtonColor: '#ef4444',
                    });
                    return;
                }
            }

            const result = await Swal.fire({
                title: 'Simpan Pembelian?',
                text: 'Pastikan data yang diisi sudah benar.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, simpan!',
                cancelButtonText: 'Batal',
            });
            if (!result.isConfirmed) return;

            this.items.forEach((item, index) => {
                const hiddenInput = this.$refs.form.querySelector(`input[name="items[${index}][product_id]"]`);
                if (hiddenInput) hiddenInput.value = item.product_id;
            });

            this.$nextTick(() => {
                this.$refs.form.submit();
            });
        },
    }
}
</script>
@endpush
