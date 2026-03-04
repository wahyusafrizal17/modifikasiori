@extends('layouts.warehouse')

@section('title', 'Input Transaksi Baru')

@section('content')
<div x-data="transaksiCreate()" class="space-y-6">
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('warehouse.dashboard') }}" class="transition hover:text-gray-700">Home</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('warehouse.transaksi.index') }}" class="transition hover:text-gray-700">Transaksi</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="font-medium text-gray-900">Input Baru</span>
    </nav>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Left: Add Items --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900">Tambah Item</h2>
                <p class="mt-1 text-sm text-gray-500">Pilih supplier, produk, harga pembelian, dan jumlah</p>

                <div class="mt-5 space-y-4">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-gray-600">Supplier</label>
                            <select x-ref="supplierSelect" id="supplier-select" placeholder="Pilih supplier..."></select>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-gray-600">Kode Produk *</label>
                            <select x-ref="productSelect" id="product-select" placeholder="Pilih produk..."></select>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-gray-600">Harga Pembelian *</label>
                            <input type="number" x-model.number="addHargaPembelian" min="0" class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20" placeholder="0">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-gray-600">Qty *</label>
                            <input type="number" x-model.number="addQty" min="1" class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm text-center focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20">
                        </div>
                    </div>
                    <button @click="addItem()" class="flex items-center gap-2 rounded-xl bg-red-500 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-red-600">
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
                                <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Supplier</th>
                                <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Kode Produk</th>
                                <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Nama Produk</th>
                                <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 text-right">Harga Pembelian</th>
                                <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 text-center">Qty</th>
                                <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 text-right">Subtotal</th>
                                <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <template x-for="(item, idx) in formItems" :key="idx">
                                <tr class="transition hover:bg-gray-50">
                                    <td class="px-5 py-4 text-gray-500" x-text="idx + 1"></td>
                                    <td class="px-5 py-4 text-gray-700" x-text="item.supplier_nama || '-'"></td>
                                    <td class="px-5 py-4 font-medium text-gray-900" x-text="item.kode_produk"></td>
                                    <td class="px-5 py-4 text-gray-700" x-text="item.nama_produk"></td>
                                    <td class="px-5 py-4 text-right text-gray-700" x-text="formatRupiah(item.harga_pembelian)"></td>
                                    <td class="px-5 py-4 text-center">
                                        <input type="number" x-model.number="item.qty" min="1" class="w-20 rounded-lg border border-gray-200 px-2 py-1.5 text-center text-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20">
                                    </td>
                                    <td class="px-5 py-4 text-right font-medium text-gray-900" x-text="formatRupiah(item.harga_pembelian * item.qty)"></td>
                                    <td class="px-5 py-4 text-right">
                                        <button @click="formItems.splice(idx, 1)" class="flex h-8 w-8 items-center justify-center rounded-lg text-red-400 transition hover:bg-red-50 hover:text-red-600">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="formItems.length === 0">
                                <tr>
                                    <td colspan="8" class="px-5 py-12 text-center text-gray-400">
                                        <svg class="mx-auto h-10 w-10 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                        <p class="mt-3 text-sm font-medium">Belum ada item ditambahkan</p>
                                        <p class="mt-1 text-xs">Pilih supplier, produk, harga dan qty di atas</p>
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
                        <span class="text-sm font-medium text-gray-700">Total Item</span>
                        <span class="text-sm font-bold text-gray-900" x-text="formItems.length"></span>
                    </div>
                    <div class="flex items-center justify-between rounded-lg bg-gray-50 px-4 py-3">
                        <span class="text-sm font-medium text-gray-700">Total Qty</span>
                        <span class="text-sm font-bold text-gray-900" x-text="formItems.reduce((sum, i) => sum + i.qty, 0)"></span>
                    </div>
                    <div class="flex items-center justify-between rounded-lg bg-green-50 px-4 py-3">
                        <span class="text-sm font-medium text-green-700">Total Harga</span>
                        <span class="text-sm font-bold text-green-800" x-text="formatRupiah(formItems.reduce((sum, i) => sum + (i.harga_pembelian * i.qty), 0))"></span>
                    </div>
                </div>

                <div class="mt-5">
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700">Catatan (opsional)</label>
                    <textarea x-model="formCatatan" rows="3" class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm transition focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20" placeholder="Catatan tambahan..."></textarea>
                </div>

                <div class="mt-5 space-y-3">
                    <button @click="submitTransaksi()" :disabled="loading || formItems.length === 0"
                            class="flex w-full items-center justify-center gap-2 rounded-xl bg-red-500 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-red-600 disabled:cursor-not-allowed disabled:opacity-50">
                        <svg x-show="!loading" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <svg x-show="loading" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        <span x-text="loading ? 'Mengirim...' : 'Submit untuk Verifikasi'"></span>
                    </button>
                    <a href="{{ route('warehouse.transaksi.index') }}" class="flex w-full items-center justify-center rounded-xl border border-gray-200 px-5 py-3 text-sm font-medium text-gray-600 transition hover:bg-gray-50">
                        Batal
                    </a>
                </div>
            </div>

            <div class="rounded-xl border border-yellow-200 bg-yellow-50 p-4">
                <div class="flex gap-3">
                    <svg class="h-5 w-5 flex-shrink-0 text-yellow-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div>
                        <p class="text-sm font-semibold text-yellow-800">Perlu Verifikasi Manager</p>
                        <p class="mt-1 text-xs text-yellow-700">Setelah disubmit, transaksi akan menunggu persetujuan Manager Warehouse. Stok baru bertambah setelah disetujui.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function transaksiCreate() {
    const baseUrl = '{{ url("warehouse/transaksi") }}';
    return {
        loading: false,
        addSupplierId: '',
        addProductId: '',
        addHargaPembelian: 0,
        addQty: 1,
        formItems: [],
        formCatatan: '',
        supplierSelect: null,
        productSelect: null,

        suppliers: @json($suppliers),
        products: @json($products),

        init() {
            this.$nextTick(() => {
                this.supplierSelect = new TomSelect(this.$refs.supplierSelect, {
                    valueField: 'id',
                    labelField: 'nama',
                    searchField: ['nama'],
                    placeholder: 'Pilih supplier (opsional)...',
                    options: this.suppliers.map(s => ({ id: s.id, nama: s.nama })),
                    allowEmptyOption: true,
                    onChange: (val) => { this.addSupplierId = val || ''; },
                    render: {
                        option: (data, escape) => `<div>${escape(data.nama)}</div>`,
                        item: (data, escape) => `<div>${escape(data.nama)}</div>`,
                    }
                });
                this.supplierSelect.addOption({ id: '', nama: '-' });

                this.productSelect = new TomSelect(this.$refs.productSelect, {
                    valueField: 'id',
                    labelField: 'kode_produk',
                    searchField: ['kode_produk', 'nama_produk'],
                    placeholder: 'Pilih produk...',
                    options: this.products.map(p => ({
                        id: p.id,
                        kode_produk: p.kode_produk,
                        nama_produk: p.nama_produk,
                        label: `[${p.kode_produk}] ${p.nama_produk}`,
                    })),
                    onChange: (val) => { this.addProductId = val || ''; },
                    render: {
                        option: (data, escape) => `<div>[${escape(data.kode_produk)}] ${escape(data.nama_produk)}</div>`,
                        item: (data, escape) => `<div>[${escape(data.kode_produk)}] ${escape(data.nama_produk)}</div>`,
                    }
                });
            });
        },

        formatRupiah(val) {
            return 'Rp ' + Number(val || 0).toLocaleString('id-ID');
        },

        addItem() {
            if (!this.addProductId || this.addHargaPembelian < 0 || this.addQty < 1) {
                Swal.fire('Error', 'Pilih produk, isi harga pembelian dan qty minimal 1.', 'warning');
                return;
            }

            const product = this.products.find(p => p.id == this.addProductId);
            if (!product) return;

            const supplier = this.addSupplierId ? this.suppliers.find(s => s.id == this.addSupplierId) : null;

            const exists = this.formItems.find(i => i.product_id == this.addProductId && i.supplier_id == (this.addSupplierId || null) && i.harga_pembelian == this.addHargaPembelian);
            if (exists) {
                exists.qty += this.addQty;
            } else {
                this.formItems.push({
                    supplier_id: this.addSupplierId || null,
                    supplier_nama: supplier?.nama || null,
                    product_id: product.id,
                    kode_produk: product.kode_produk,
                    nama_produk: product.nama_produk,
                    harga_pembelian: this.addHargaPembelian,
                    qty: this.addQty,
                });
            }

            this.addProductId = '';
            this.addHargaPembelian = 0;
            this.addQty = 1;
            if (this.supplierSelect) this.supplierSelect.clear();
            if (this.productSelect) this.productSelect.clear();
        },

        async submitTransaksi() {
            if (this.formItems.length === 0) return;

            const result = await Swal.fire({
                title: 'Submit Transaksi?',
                text: 'Data akan dikirim untuk diverifikasi oleh Manager Warehouse.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Submit!',
                cancelButtonText: 'Batal'
            });
            if (!result.isConfirmed) return;

            this.loading = true;

            const payload = {
                catatan: this.formCatatan || null,
                items: this.formItems.map(i => ({
                    supplier_id: i.supplier_id || null,
                    product_id: i.product_id,
                    harga_pembelian: i.harga_pembelian,
                    qty: i.qty,
                })),
            };

            const res = await fetch(baseUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            if (!res.ok) {
                const d = await res.json();
                this.loading = false;
                Swal.fire('Error', d.message || 'Gagal menyimpan.', 'error');
                return;
            }

            window.location.href = '{{ route("warehouse.transaksi.index") }}';
        }
    }
}
</script>
@endpush
