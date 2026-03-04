@extends('layouts.warehouse')

@section('title', 'Input Transaksi Online')

@section('content')
<div x-data="transaksiOnlineCreate()" class="space-y-6">
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('warehouse.dashboard') }}" class="transition hover:text-gray-700">Home</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('warehouse.transaksi-online.index') }}" class="transition hover:text-gray-700">Transaksi Online</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="font-medium text-gray-900">Input Baru</span>
    </nav>

    @include('partials.flash')
    @if($errors->any())
    <div class="rounded-xl border border-red-200 bg-red-50 p-4">
        <ul class="list-inside list-disc text-sm text-red-700">
            @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Left: Add Items --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900">Tambah Item</h2>
                <p class="mt-1 text-sm text-gray-500">Masukkan No RESI, pilih produk dan jumlah</p>

                <div class="mt-5 space-y-4">
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-gray-600">No RESI *</label>
                        <input type="text" x-model="formNoResi" class="w-full rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20" placeholder="Contoh: SP1234567890">
                    </div>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-gray-600">Produk *</label>
                            <select x-ref="productSelect" id="product-select" placeholder="Pilih produk..."></select>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-gray-600">Qty *</label>
                            <input type="number" x-model.number="addQty" min="1" class="w-full rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm text-center focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20" placeholder="1">
                        </div>
                    </div>
                    <button type="button" @click="addItem()" class="flex items-center gap-2 rounded-xl bg-red-500 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-red-600">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Tambah
                    </button>
                </div>
            </div>

            {{-- Item Table --}}
            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-900">Daftar Item</h2>
                    <span class="inline-flex items-center rounded-lg bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600" x-text="formItems.length + ' item'"></span>
                </div>

                <div class="mt-4 overflow-x-auto rounded-xl border border-gray-100">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">No.</th>
                                <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Kode Produk</th>
                                <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Nama Produk</th>
                                <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 text-center">Qty</th>
                                <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <template x-for="(item, idx) in formItems" :key="idx">
                                <tr class="transition hover:bg-gray-50">
                                    <td class="px-5 py-4 text-gray-500" x-text="idx + 1"></td>
                                    <td class="px-5 py-4 font-medium text-gray-900" x-text="item.kode_produk"></td>
                                    <td class="px-5 py-4 text-gray-700" x-text="item.nama_produk"></td>
                                    <td class="px-5 py-4 text-center">
                                        <input type="number" x-model.number="item.qty" min="1" class="w-20 rounded-lg border border-gray-200 px-2 py-1.5 text-center text-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20">
                                    </td>
                                    <td class="px-5 py-4 text-right">
                                        <button type="button" @click="formItems.splice(idx, 1)" class="flex h-8 w-8 items-center justify-center rounded-lg text-red-400 transition hover:bg-red-50 hover:text-red-600">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="formItems.length === 0">
                                <tr>
                                    <td colspan="5" class="px-5 py-12 text-center text-gray-400">
                                        <svg class="mx-auto h-10 w-10 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                        <p class="mt-3 text-sm font-medium">Belum ada item ditambahkan</p>
                                        <p class="mt-1 text-xs">Masukkan No RESI, pilih produk dan qty di atas</p>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Right: Summary & Submit --}}
        <div class="space-y-6">
            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900">Ringkasan</h2>

                <div class="mt-4 space-y-3">
                    <div class="flex items-center justify-between rounded-lg bg-gray-50 px-4 py-3">
                        <span class="text-sm font-medium text-gray-700">No RESI</span>
                        <span class="text-sm font-bold text-gray-900" x-text="formNoResi || '-'"></span>
                    </div>
                    <div class="flex items-center justify-between rounded-lg bg-gray-50 px-4 py-3">
                        <span class="text-sm font-medium text-gray-700">Total Item</span>
                        <span class="text-sm font-bold text-gray-900" x-text="formItems.length"></span>
                    </div>
                    <div class="flex items-center justify-between rounded-lg bg-green-50 px-4 py-3">
                        <span class="text-sm font-medium text-green-700">Total Qty</span>
                        <span class="text-sm font-bold text-green-800" x-text="formItems.reduce((sum, i) => sum + i.qty, 0)"></span>
                    </div>
                </div>

                <div class="mt-5 space-y-3">
                    <button type="button" @click="submitTransaksi()" :disabled="loading || formItems.length === 0 || !formNoResi.trim()"
                            class="flex w-full items-center justify-center gap-2 rounded-xl bg-red-500 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-red-600 disabled:cursor-not-allowed disabled:opacity-50">
                        <svg x-show="!loading" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <svg x-show="loading" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        <span x-text="loading ? 'Menyimpan...' : 'Simpan'"></span>
                    </button>
                    <a href="{{ route('warehouse.transaksi-online.index') }}" class="flex w-full items-center justify-center rounded-xl border border-gray-200 px-5 py-3 text-sm font-medium text-gray-600 transition hover:bg-gray-50">
                        Batal
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<form id="form-submit" method="POST" action="{{ route('warehouse.transaksi-online.store') }}" class="hidden">
    @csrf
    <input type="hidden" name="no_resi" id="input-no-resi">
    <div id="form-items-container"></div>
</form>
@endsection

@push('scripts')
<script>
function transaksiOnlineCreate() {
    return {
        loading: false,
        formNoResi: @json(old('no_resi', '')),
        addProductId: '',
        addQty: 1,
        formItems: [],
        productSelect: null,
        products: @json($products),

        init() {
            this.$nextTick(() => {
                this.productSelect = new TomSelect(this.$refs.productSelect, {
                    valueField: 'id',
                    labelField: 'label',
                    searchField: ['kode_produk', 'nama_produk'],
                    placeholder: 'Pilih produk...',
                    options: this.products.map(p => ({
                        id: p.id,
                        kode_produk: p.kode_produk,
                        nama_produk: p.nama_produk,
                        jumlah: p.jumlah,
                        label: `[${p.kode_produk}] ${p.nama_produk} (Stok: ${Number(p.jumlah).toLocaleString('id-ID')})`,
                    })),
                    onChange: (val) => { this.addProductId = val || ''; },
                    render: {
                        option: (data, escape) => `<div><span class="font-medium">${escape(data.kode_produk)}</span> - ${escape(data.nama_produk)} <span class="text-gray-400 text-xs">(Stok: ${Number(data.jumlah || 0).toLocaleString('id-ID')})</span></div>`,
                        item: (data, escape) => `<div>${escape(data.kode_produk)} - ${escape(data.nama_produk)}</div>`,
                    },
                });
            });
        },

        addItem() {
            if (!this.formNoResi || !this.formNoResi.trim()) {
                Swal.fire('Perhatian', 'Masukkan No RESI terlebih dahulu.', 'warning');
                return;
            }
            if (!this.addProductId || this.addQty < 1) {
                Swal.fire('Perhatian', 'Pilih produk dan isi qty minimal 1.', 'warning');
                return;
            }

            const product = this.products.find(p => p.id == this.addProductId);
            if (!product) return;

            if (product.jumlah < this.addQty) {
                Swal.fire('Stok Tidak Cukup', `Stok tersedia: ${Number(product.jumlah).toLocaleString('id-ID')}`, 'warning');
                return;
            }

            const exists = this.formItems.find(i => i.product_id == this.addProductId);
            if (exists) {
                const newQty = exists.qty + this.addQty;
                if (product.jumlah < newQty) {
                    Swal.fire('Stok Tidak Cukup', `Stok tersedia: ${Number(product.jumlah).toLocaleString('id-ID')}. Total qty yang sudah ditambah: ${exists.qty}`, 'warning');
                    return;
                }
                exists.qty = newQty;
            } else {
                this.formItems.push({
                    product_id: product.id,
                    kode_produk: product.kode_produk,
                    nama_produk: product.nama_produk,
                    qty: this.addQty,
                });
            }

            this.addProductId = '';
            this.addQty = 1;
            if (this.productSelect) this.productSelect.clear();
        },

        submitTransaksi() {
            if (!this.formNoResi || !this.formNoResi.trim()) {
                Swal.fire('Perhatian', 'Masukkan No RESI terlebih dahulu.', 'warning');
                return;
            }
            if (this.formItems.length === 0) {
                Swal.fire('Perhatian', 'Tambahkan minimal 1 produk.', 'warning');
                return;
            }

            this.loading = true;
            const form = document.getElementById('form-submit');
            document.getElementById('input-no-resi').value = this.formNoResi;

            const container = document.getElementById('form-items-container');
            container.innerHTML = '';
            this.formItems.forEach((item, idx) => {
                const inp1 = document.createElement('input');
                inp1.type = 'hidden';
                inp1.name = `items[${idx}][product_id]`;
                inp1.value = item.product_id;
                const inp2 = document.createElement('input');
                inp2.type = 'hidden';
                inp2.name = `items[${idx}][qty]`;
                inp2.value = item.qty;
                container.appendChild(inp1);
                container.appendChild(inp2);
            });

            form.submit();
        },
    };
}
</script>
@endpush
