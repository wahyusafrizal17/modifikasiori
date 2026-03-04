@extends('layouts.warehouse')

@section('title', 'Input Transaksi Offline')

@section('content')
<div x-data="transaksiOfflineCreate()" class="space-y-6">
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('warehouse.dashboard') }}" class="transition hover:text-gray-700">Home</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('warehouse.transaksi-offline.index') }}" class="transition hover:text-gray-700">Transaksi Offline</a>
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
        {{-- Left: Form & Items --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Header Form --}}
            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900">Data Transaksi</h2>
                <p class="mt-1 text-sm text-gray-500">Isi data transaksi dan tambahkan produk</p>

                <div class="mt-5 space-y-4">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-gray-600">Tujuan *</label>
                            <select x-model="formTujuan" class="w-full rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20">
                                <option value="speedshop">Speedshop</option>
                                <option value="reseller">Reseller</option>
                                <option value="umum">Umum</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-gray-600">No Transaksi</label>
                            <div class="flex gap-2" x-show="formTujuan === 'speedshop'">
                                <input type="text" x-model="formNoTransaksi" class="flex-1 rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20" placeholder="Kosongkan = baru, atau masukkan No yang ada">
                                <button type="button" @click="loadTransaksiByNo()" :disabled="loadingLookup" class="rounded-xl bg-blue-500 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-600 disabled:opacity-50">
                                    <span x-text="loadingLookup ? '...' : 'Load'"></span>
                                </button>
                            </div>
                            <div x-show="formTujuan !== 'speedshop'" class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-500">
                                Otomatis
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label class="mb-1.5 block text-xs font-semibold text-gray-600">Nama Toko</label>
                            <input type="text" x-model="formNamaToko" class="w-full rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20" placeholder="Nama toko/customer">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="mb-1.5 block text-xs font-semibold text-gray-600">Alamat</label>
                            <textarea x-model="formAlamat" rows="2" class="w-full rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20" placeholder="Alamat"></textarea>
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
            </div>

            {{-- Tambah Item --}}
            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900">Tambah Item</h2>
                <div class="mt-4 flex gap-4">
                    <div class="flex-1">
                        <label class="mb-1.5 block text-xs font-semibold text-gray-600">Produk *</label>
                        <select x-ref="productSelect" id="product-select-offline" placeholder="Pilih produk..."></select>
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

            {{-- Item yang sudah ada (Speedshop Load) --}}
            <div x-show="loadedExistingItems.length > 0" class="rounded-xl border border-blue-100 bg-blue-50/50 p-6 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900">Item Transaksi (sudah ada)</h2>
                <p class="mt-1 text-sm text-gray-600">Item yang sudah tercatat di transaksi ini</p>
                <div class="mt-4 overflow-x-auto rounded-lg border border-blue-100 bg-white">
                    <table class="w-full text-left text-sm">
                        <thead><tr class="bg-blue-50"><th class="px-4 py-2 text-xs font-semibold text-gray-600">No.</th><th class="px-4 py-2 text-xs font-semibold text-gray-600">Kode</th><th class="px-4 py-2 text-xs font-semibold text-gray-600">Nama</th><th class="px-4 py-2 text-xs font-semibold text-gray-600 text-center">Qty</th></tr></thead>
                        <tbody>
                            <template x-for="(item, idx) in loadedExistingItems" :key="idx">
                                <tr class="border-t border-blue-50"><td class="px-4 py-2 text-gray-500" x-text="idx+1"></td><td class="px-4 py-2 font-medium" x-text="item.kode_produk"></td><td class="px-4 py-2" x-text="item.nama_produk"></td><td class="px-4 py-2 text-center" x-text="item.qty"></td></tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Daftar Item Baru --}}
            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-900">Daftar Item <span x-show="loadedExistingItems.length > 0" class="text-sm font-normal text-gray-500">(baru)</span></h2>
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
                                    <td class="px-5 py-4">
                                        <button type="button" @click="formItems.splice(idx, 1)" class="rounded-lg p-1.5 text-red-400 transition hover:bg-red-50 hover:text-red-600">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="formItems.length === 0">
                                <tr>
                                    <td colspan="5" class="px-5 py-12 text-center text-gray-400">
                                        <p class="text-sm font-medium">Belum ada item. Pilih produk dan klik Tambah.</p>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Right: Summary --}}
        <div class="space-y-6">
            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900">Ringkasan</h2>
                <div class="mt-4 space-y-3">
                    <div class="flex items-center justify-between rounded-lg bg-gray-50 px-4 py-3">
                        <span class="text-sm font-medium text-gray-700">No Transaksi</span>
                        <span class="text-sm font-bold text-gray-900" x-text="formTujuan === 'speedshop' && formNoTransaksi ? formNoTransaksi : (formItems.length ? 'Auto' : '-')"></span>
                    </div>
                    <div class="flex items-center justify-between rounded-lg bg-gray-50 px-4 py-3">
                        <span class="text-sm font-medium text-gray-700">Total Item</span>
                        <span class="text-sm font-bold text-gray-900" x-text="formItems.length"></span>
                    </div>
                    <div class="flex items-center justify-between rounded-lg bg-green-50 px-4 py-3">
                        <span class="text-sm font-medium text-green-700">Total Qty</span>
                        <span class="text-sm font-bold text-green-800" x-text="formItems.reduce((s,i)=>s+i.qty,0)"></span>
                    </div>
                </div>
                <div class="mt-5 space-y-3">
                    <button type="button" @click="submitTransaksi()" :disabled="loading || formItems.length === 0"
                            class="flex w-full items-center justify-center gap-2 rounded-xl bg-red-500 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-red-600 disabled:cursor-not-allowed disabled:opacity-50">
                        <span x-text="loading ? 'Menyimpan...' : 'Simpan'"></span>
                    </button>
                    <a href="{{ route('warehouse.transaksi-offline.index') }}" class="flex w-full items-center justify-center rounded-xl border border-gray-200 px-5 py-3 text-sm font-medium text-gray-600 transition hover:bg-gray-50">Batal</a>
                </div>
            </div>
        </div>
    </div>
</div>

<form id="form-submit-offline" method="POST" action="{{ route('warehouse.transaksi-offline.store') }}" class="hidden">
    @csrf
    <input type="hidden" name="no_transaksi" id="input-no-transaksi">
    <input type="hidden" name="tujuan" id="input-tujuan">
    <input type="hidden" name="nama_toko" id="input-nama-toko">
    <input type="hidden" name="alamat" id="input-alamat">
    <input type="hidden" name="no_hp" id="input-no-hp">
    <input type="hidden" name="jenis_pembayaran" id="input-jenis-pembayaran">
    <div id="form-items-container-offline"></div>
</form>
@endsection

@push('scripts')
<script>
function transaksiOfflineCreate() {
    return {
        loading: false,
        loadingLookup: false,
        formTujuan: 'speedshop',
        formNoTransaksi: '',
        formNamaToko: '',
        formAlamat: '',
        formNoHp: '',
        formJenisPembayaran: '',
        addProductId: '',
        addQty: 1,
        formItems: [],
        productSelect: null,
        products: @json($products),
        lookupUrl: '{{ url("warehouse/transaksi-offline/lookup") }}',

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
                        option: (data, escape) => `<div><span class="font-medium">${escape(data.kode_produk)}</span> - ${escape(data.nama_produk)} <span class="text-gray-400 text-xs">(Stok: ${Number(data.jumlah||0).toLocaleString('id-ID')})</span></div>`,
                        item: (data, escape) => `<div>${escape(data.kode_produk)} - ${escape(data.nama_produk)}</div>`,
                    },
                });
            });
        },

        loadedExistingItems: [],

        async loadTransaksiByNo() {
            const no = this.formNoTransaksi?.trim();
            if (!no) {
                Swal.fire('Perhatian', 'Masukkan No Transaksi terlebih dahulu.', 'warning');
                return;
            }
            this.loadingLookup = true;
            try {
                const res = await fetch(`${this.lookupUrl}?no_transaksi=${encodeURIComponent(no)}`, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();
                if (data.found && data.transaksi) {
                    const t = data.transaksi;
                    this.formNoTransaksi = t.no_transaksi;
                    this.formNamaToko = t.nama_toko || '';
                    this.formAlamat = t.alamat || '';
                    this.formNoHp = t.no_hp || '';
                    this.formJenisPembayaran = t.jenis_pembayaran || '';
                    this.loadedExistingItems = (t.items || []).map(i => ({
                        product_id: i.product_id,
                        kode_produk: i.kode_produk,
                        nama_produk: i.nama_produk,
                        qty: i.qty,
                    }));
                    this.formItems = [];
                    Swal.fire('Berhasil', 'Transaksi berhasil diload. Tambahkan item baru di bawah.', 'success');
                } else {
                    Swal.fire('Tidak Ditemukan', 'No Transaksi tidak ditemukan atau bukan transaksi Speedshop.', 'info');
                }
            } catch (e) {
                Swal.fire('Error', 'Gagal memuat transaksi.', 'error');
            }
            this.loadingLookup = false;
        },

        addItem() {
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
                });
            }
            this.addProductId = '';
            this.addQty = 1;
            if (this.productSelect) this.productSelect.clear();
        },

        submitTransaksi() {
            if (this.formItems.length === 0 && this.loadedExistingItems.length === 0) {
                Swal.fire('Perhatian', 'Tambahkan minimal 1 produk.', 'warning');
                return;
            }
            if (this.loadedExistingItems.length > 0 && this.formItems.length === 0) {
                Swal.fire('Perhatian', 'Untuk menambah ke transaksi yang ada, tambahkan minimal 1 item baru.', 'warning');
                return;
            }
            if (this.formTujuan === 'speedshop' && !this.formNoTransaksi?.trim()) {
                this.formNoTransaksi = '';
            }
            this.loading = true;
            const form = document.getElementById('form-submit-offline');
            document.getElementById('input-no-transaksi').value = this.formTujuan === 'speedshop' ? (this.formNoTransaksi || '') : '';
            document.getElementById('input-tujuan').value = this.formTujuan;
            document.getElementById('input-nama-toko').value = this.formNamaToko;
            document.getElementById('input-alamat').value = this.formAlamat;
            document.getElementById('input-no-hp').value = this.formNoHp;
            document.getElementById('input-jenis-pembayaran').value = this.formJenisPembayaran;
            const container = document.getElementById('form-items-container-offline');
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
