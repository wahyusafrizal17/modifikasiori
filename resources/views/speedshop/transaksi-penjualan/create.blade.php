@extends('layouts.speedshop')

@section('title', 'Input Transaksi Penjualan')

@section('content')
<div x-data="transaksiPenjualanCreate()" class="space-y-6">
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('speedshop.dashboard') }}" class="transition hover:text-gray-700">Home</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('speedshop.transaksi') }}" class="transition hover:text-gray-700">Transaksi Penjualan</a>
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
        <div class="lg:col-span-2 space-y-6">
            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900">Data Transaksi</h2>
                <p class="mt-1 text-sm text-gray-500">Data pembeli (opsional) dan tambahkan produk</p>
                <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label class="mb-1.5 block text-xs font-semibold text-gray-600">Nama Pembeli</label>
                        <input type="text" x-model="formNamaPembeli" class="w-full rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20" placeholder="Nama pembeli">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-gray-600">No HP</label>
                        <input type="text" x-model="formNoHp" class="w-full rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20" placeholder="08xxxx">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-gray-600">Jenis Pembayaran</label>
                        <select x-model="formJenisPembayaran" class="w-full rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20">
                            <option value="">-- Pilih --</option>
                            <option value="cash">Cash</option>
                            <option value="transfer">Transfer</option>
                            <option value="debit">Debit</option>
                            <option value="kredit">Kredit</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900">Tambah Item</h2>
                <div class="mt-4 flex gap-4">
                    <div class="flex-1">
                        <label class="mb-1.5 block text-xs font-semibold text-gray-600">Produk *</label>
                        <select x-ref="productSelect" id="product-select-penjualan" placeholder="Pilih produk..."></select>
                    </div>
                    <div class="w-24">
                        <label class="mb-1.5 block text-xs font-semibold text-gray-600">Qty *</label>
                        <input type="number" x-model.number="addQty" min="1" class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-center text-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20">
                    </div>
                    <div class="flex items-end">
                        <button type="button" @click="addItem()" class="rounded-xl bg-red-500 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-red-600">
                            + Tambah
                        </button>
                    </div>
                </div>
            </div>

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
                                <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Kode</th>
                                <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Nama Produk</th>
                                <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 text-center">Qty</th>
                                <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 text-right">Harga</th>
                                <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 text-right">Subtotal</th>
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
                                    <td class="px-5 py-4 text-right text-gray-600" x-text="formatRupiah(item.harga_satuan)"></td>
                                    <td class="px-5 py-4 text-right font-medium" x-text="formatRupiah(item.harga_satuan * item.qty)"></td>
                                    <td class="px-5 py-4">
                                        <button type="button" @click="formItems.splice(idx, 1)" class="rounded-lg p-1.5 text-red-400 transition hover:bg-red-50 hover:text-red-600">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="formItems.length === 0">
                                <tr>
                                    <td colspan="7" class="px-5 py-12 text-center text-gray-400">
                                        <p class="text-sm font-medium">Belum ada item. Pilih produk dan klik Tambah.</p>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

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
                        <span class="text-sm font-bold text-gray-900" x-text="formItems.reduce((s,i)=>s+i.qty,0)"></span>
                    </div>
                    <div class="flex items-center justify-between rounded-lg bg-red-50 px-4 py-3">
                        <span class="text-sm font-medium text-red-700">Total Bayar</span>
                        <span class="text-sm font-bold text-red-800" x-text="formatRupiah(formItems.reduce((s,i)=>s+(i.harga_satuan*i.qty),0))"></span>
                    </div>
                </div>
                <div class="mt-5 space-y-3">
                    <button type="button" @click="submitTransaksi()" :disabled="loading || formItems.length === 0"
                            class="flex w-full items-center justify-center gap-2 rounded-xl bg-red-500 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-red-600 disabled:cursor-not-allowed disabled:opacity-50">
                        <span x-text="loading ? 'Menyimpan...' : 'Simpan Transaksi'"></span>
                    </button>
                    <a href="{{ route('speedshop.transaksi') }}" class="flex w-full items-center justify-center rounded-xl border border-gray-200 px-5 py-3 text-sm font-medium text-gray-600 transition hover:bg-gray-50">Batal</a>
                </div>
            </div>
        </div>
    </div>
</div>

<form id="form-submit-penjualan" method="POST" action="{{ route('speedshop.transaksi-penjualan.store') }}" class="hidden">
    @csrf
    <input type="hidden" name="nama_pembeli" id="input-nama-pembeli">
    <input type="hidden" name="no_hp" id="input-no-hp">
    <input type="hidden" name="jenis_pembayaran" id="input-jenis-pembayaran">
    <div id="form-items-container-penjualan"></div>
</form>
@endsection

@push('scripts')
<script>
function transaksiPenjualanCreate() {
    return {
        loading: false,
        formNamaPembeli: '',
        formNoHp: '',
        formJenisPembayaran: '',
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
                        harga_satuan: Number(p.harga_jual_speedshop || p.harga_jual || 0),
                        label: `[${p.kode_produk}] ${p.nama_produk} (Stok: ${Number(p.jumlah).toLocaleString('id-ID')})`,
                    })),
                    onChange: (val) => { this.addProductId = val || ''; },
                    render: {
                        option: (data, escape) => `<div><span class="font-medium">${escape(data.kode_produk)}</span> - ${escape(data.nama_produk)} <span class="text-gray-400 text-xs">(Stok: ${Number(data.jumlah||0).toLocaleString('id-ID')})</span></div>`,
                        item: (data, escape) => `<div>${escape(data.kode_produk)} - ${escape(data.nama_produk)}</div>`,
                    },
                });
            });
        },

        formatRupiah(n) {
            return 'Rp ' + Number(n || 0).toLocaleString('id-ID');
        },

        addItem() {
            if (!this.addProductId || this.addQty < 1) {
                Swal.fire('Perhatian', 'Pilih produk dan isi qty minimal 1.', 'warning');
                return;
            }
            const product = this.products.find(p => p.id == this.addProductId);
            if (!product) return;
            const harga = Number(product.harga_jual_speedshop || product.harga_jual || 0);
            if (product.jumlah < this.addQty) {
                Swal.fire('Stok Tidak Cukup', `Stok tersedia: ${Number(product.jumlah).toLocaleString('id-ID')}`, 'warning');
                return;
            }
            const exists = this.formItems.find(i => i.product_id == this.addProductId);
            if (exists) {
                const newQty = exists.qty + this.addQty;
                if (product.jumlah < newQty) {
                    Swal.fire('Stok Tidak Cukup', `Stok tersedia: ${Number(product.jumlah).toLocaleString('id-ID')}`, 'warning');
                    return;
                }
                exists.qty = newQty;
            } else {
                this.formItems.push({
                    product_id: product.id,
                    kode_produk: product.kode_produk,
                    nama_produk: product.nama_produk,
                    qty: this.addQty,
                    harga_satuan: harga,
                });
            }
            this.addProductId = '';
            this.addQty = 1;
            if (this.productSelect) this.productSelect.clear();
        },

        submitTransaksi() {
            if (this.formItems.length === 0) {
                Swal.fire('Perhatian', 'Tambahkan minimal 1 produk.', 'warning');
                return;
            }
            this.loading = true;
            const form = document.getElementById('form-submit-penjualan');
            document.getElementById('input-nama-pembeli').value = this.formNamaPembeli;
            document.getElementById('input-no-hp').value = this.formNoHp;
            document.getElementById('input-jenis-pembayaran').value = this.formJenisPembayaran;
            const container = document.getElementById('form-items-container-penjualan');
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
