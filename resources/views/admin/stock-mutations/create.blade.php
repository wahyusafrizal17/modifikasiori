@extends('layouts.admin')

@section('title', 'Buat Mutasi Stok')

@section('content')
<div x-data="mutationForm()" class="space-y-6">
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.dashboard') }}" class="transition hover:text-gray-700">Home</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('admin.stock-mutations.index') }}" class="transition hover:text-gray-700">Mutasi Stok</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="font-medium text-gray-900">Buat Mutasi</span>
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

    <form action="{{ route('admin.stock-mutations.store') }}" method="POST" x-ref="form">
        @csrf
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            {{-- Left Column --}}
            <div class="space-y-6 lg:col-span-2">
                {{-- Informasi Mutasi --}}
                <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900">Informasi Mutasi</h3>
                    <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">Gudang Asal <span class="text-red-500">*</span></label>
                            <select x-ref="fromWarehouseSelect" name="from_warehouse_id" placeholder="Pilih gudang asal...">
                                @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}" {{ old('from_warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->nama }}</option>
                                @endforeach
                            </select>
                            @error('from_warehouse_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">Gudang Tujuan <span class="text-red-500">*</span></label>
                            <select x-ref="toWarehouseSelect" name="to_warehouse_id" placeholder="Pilih gudang tujuan...">
                                @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}" {{ old('to_warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->nama }}</option>
                                @endforeach
                            </select>
                            @error('to_warehouse_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">Tanggal <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal" value="{{ old('tanggal', now()->toDateString()) }}" class="h-10 w-full rounded-xl border border-gray-200 bg-gray-50 px-4 text-sm outline-none transition focus:border-red-300 focus:ring-2 focus:ring-red-100">
                            @error('tanggal') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Catatan</label>
                        <textarea name="catatan" rows="3" placeholder="Catatan tambahan (opsional)" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm outline-none transition focus:border-red-300 focus:ring-2 focus:ring-red-100">{{ old('catatan') }}</textarea>
                    </div>
                </div>

                {{-- Item Mutasi --}}
                <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-900">Item Mutasi</h3>
                        <button type="button" @click="addItem()" class="inline-flex items-center gap-1 rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-600 transition hover:bg-blue-100">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            Tambah Item
                        </button>
                    </div>

                    <div class="mt-4">
                        <div class="rounded-xl border border-gray-100">
                            <table class="w-full text-left text-sm">
                                <thead>
                                    <tr class="bg-gray-50">
                                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Produk</th>
                                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 w-32 text-center">Qty</th>
                                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 w-16"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <template x-for="(item, index) in items" :key="item._uid">
                                        <tr>
                                            <td class="px-4 py-3">
                                                <div x-init="$nextTick(() => initProductSelect($el.querySelector('select'), index))">
                                                    <select :id="`product_select_${item._uid}`" placeholder="Ketik untuk mencari produk...">
                                                        <option value="">— Pilih Produk —</option>
                                                        @foreach($products as $prod)
                                                        <option value="{{ $prod->id }}">{{ $prod->kode_produk }} — {{ $prod->nama_produk }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <input type="hidden" :name="`items[${index}][product_id]`" :value="item.product_id">
                                            </td>
                                            <td class="px-4 py-3">
                                                <input type="number" :name="`items[${index}][qty]`" x-model.number="item.qty" min="1" placeholder="0" class="h-10 w-full rounded-xl border border-gray-200 bg-white px-3 text-sm text-center outline-none transition focus:border-red-300 focus:ring-2 focus:ring-red-100">
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <button type="button" @click="removeItem(index)" class="text-red-400 transition hover:text-red-600">
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                        <p x-show="items.length === 0" class="py-8 text-center text-sm text-gray-400">Belum ada item ditambahkan. Klik "Tambah Item" untuk memulai.</p>
                    </div>
                </div>
            </div>

            {{-- Right Column: Summary --}}
            <div>
                <div class="sticky top-8 rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900">Ringkasan</h3>
                    <div class="mt-5 space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Total Item</span>
                            <span class="font-semibold text-gray-900" x-text="items.length + ' produk'"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Total Qty</span>
                            <span class="font-semibold text-gray-900" x-text="totalQty + ' unit'"></span>
                        </div>
                    </div>
                    <div class="mt-6 space-y-3">
                        <button type="button" @click="submitForm()" class="w-full rounded-xl bg-red-500 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-red-600">Simpan Mutasi</button>
                        <a href="{{ route('admin.stock-mutations.index') }}" class="block w-full rounded-xl border border-gray-200 py-3 text-center text-sm font-medium text-gray-600 transition hover:bg-gray-50">Batal</a>
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
let _uid = 0;

function mutationForm() {
    return {
        from_warehouse_id: '{{ old("from_warehouse_id") }}',
        to_warehouse_id: '{{ old("to_warehouse_id") }}',
        items: [],
        _tomSelects: {},

        init() {
            this.$nextTick(() => {
                new TomSelect(this.$refs.fromWarehouseSelect, {
                    placeholder: 'Pilih gudang asal...',
                    allowEmptyOption: true,
                    onChange: (value) => { this.from_warehouse_id = value; }
                });
                new TomSelect(this.$refs.toWarehouseSelect, {
                    placeholder: 'Pilih gudang tujuan...',
                    allowEmptyOption: true,
                    onChange: (value) => { this.to_warehouse_id = value; }
                });
            });
        },

        addItem() {
            this.items.push({ _uid: ++_uid, product_id: '', qty: 1 });
        },

        removeItem(index) {
            const item = this.items[index];
            const key = 'product_' + item._uid;
            if (this._tomSelects[key]) {
                this._tomSelects[key].destroy();
                delete this._tomSelects[key];
            }
            this.items.splice(index, 1);
        },

        initProductSelect(el, index) {
            const item = this.items[index];
            if (!el || !item) return;
            const key = 'product_' + item._uid;
            this._tomSelects[key] = new TomSelect(el, {
                placeholder: '— Pilih Produk —',
                allowEmptyOption: true,
                dropdownParent: 'body',
                onChange(value) {
                    item.product_id = value;
                }
            });
        },

        get totalQty() {
            return this.items.reduce((sum, i) => sum + Number(i.qty || 0), 0);
        },

        async submitForm() {
            if (!this.from_warehouse_id || !this.to_warehouse_id) {
                await Swal.fire({ title: 'Data Belum Lengkap', text: 'Pilih gudang asal dan gudang tujuan terlebih dahulu.', icon: 'warning', confirmButtonColor: '#ef4444' });
                return;
            }
            if (this.from_warehouse_id === this.to_warehouse_id) {
                await Swal.fire({ title: 'Gudang Sama', text: 'Gudang asal dan gudang tujuan tidak boleh sama.', icon: 'warning', confirmButtonColor: '#ef4444' });
                return;
            }
            if (this.items.length === 0) {
                await Swal.fire({ title: 'Belum Ada Item', text: 'Tambahkan minimal 1 item untuk mutasi.', icon: 'warning', confirmButtonColor: '#ef4444' });
                return;
            }
            for (const item of this.items) {
                if (!item.product_id || !item.qty || item.qty < 1) {
                    await Swal.fire({ title: 'Data Item Tidak Valid', text: 'Pastikan semua item memiliki produk dan qty yang valid.', icon: 'warning', confirmButtonColor: '#ef4444' });
                    return;
                }
            }
            const result = await Swal.fire({
                title: 'Simpan Mutasi Stok?',
                text: 'Pastikan data yang diisi sudah benar.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, simpan!',
                cancelButtonText: 'Batal'
            });
            if (result.isConfirmed) {
                const form = this.$refs.form;
                this.items.forEach((item, index) => {
                    const hiddenInput = form.querySelector(`input[name="items[${index}][product_id]"]`);
                    if (hiddenInput) hiddenInput.value = item.product_id;
                });
                this.$nextTick(() => form.submit());
            }
        }
    }
}
</script>
@endpush
