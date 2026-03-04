@extends('layouts.warehouse')

@section('title', 'Buat Mutasi ke Speedshop')

@section('content')
<div x-data="mutasiKeSpeedshop()" class="space-y-6">
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('warehouse.dashboard') }}" class="transition hover:text-gray-700">Home</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('warehouse.transaksi-speedshop') }}" class="transition hover:text-gray-700">Transaksi Speedshop (Mutasi)</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="font-medium text-gray-900">Buat Mutasi ke Speedshop</span>
    </nav>

    @include('partials.flash')

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6">
            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Pilih Produk</h2>
                        <p class="mt-1 text-sm text-gray-500">Produk dengan stok tersedia di warehouse Anda</p>
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap items-end gap-3">
                    <div class="min-w-[260px] flex-1">
                        <label class="mb-1.5 block text-xs font-semibold text-gray-600">Produk</label>
                        <select x-ref="productSelect" id="product-select" placeholder="Cari produk..."></select>
                    </div>
                    <div class="w-28">
                        <label class="mb-1.5 block text-xs font-semibold text-gray-600">Jumlah</label>
                        <input type="number" x-model.number="addQty" min="1" class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-center text-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20">
                    </div>
                    <button type="button" @click="addProduct()" class="flex h-[42px] items-center gap-2 rounded-xl bg-red-500 px-5 text-sm font-semibold text-white transition hover:bg-red-600">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Tambah
                    </button>
                </div>

                <div class="mt-4 overflow-x-auto rounded-xl border border-gray-100">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Kode</th>
                                <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Nama Produk</th>
                                <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 text-center">Stok</th>
                                <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 text-right">Harga Jual Speedshop</th>
                                <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 text-center">Jumlah Mutasi</th>
                                <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 text-right">Subtotal</th>
                                <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <template x-for="(item, idx) in formItems" :key="idx">
                                <tr class="hover:bg-gray-50">
                                    <td class="px-5 py-4 font-medium text-gray-900" x-text="item.kode_produk"></td>
                                    <td class="px-5 py-4 text-gray-700" x-text="item.nama_produk"></td>
                                    <td class="px-5 py-4 text-center">
                                        <span class="inline-flex items-center rounded-lg bg-green-100 px-2 py-0.5 text-xs font-semibold text-green-700" x-text="item.jumlah + ' unit'"></span>
                                    </td>
                                    <td class="px-5 py-4 text-right text-gray-700" x-text="formatRp(item.harga_jual_speedshop || 0)"></td>
                                    <td class="px-5 py-4 text-center">
                                        <input type="number" x-model.number="item.quantity" :max="item.jumlah" min="1" class="w-24 rounded-lg border border-gray-200 px-2 py-1.5 text-center text-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20">
                                    </td>
                                    <td class="px-5 py-4 text-right font-semibold text-gray-900" x-text="formatRp((item.harga_jual_speedshop || 0) * (item.quantity || 0))"></td>
                                    <td class="px-5 py-4">
                                        <button type="button" @click="formItems.splice(idx, 1)" class="flex h-8 w-8 items-center justify-center rounded-lg text-red-400 transition hover:bg-red-50 hover:text-red-600">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="formItems.length === 0">
                                <tr>
                                    <td colspan="7" class="px-5 py-8 text-center text-gray-400 text-sm">Belum ada produk ditambahkan</td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900">Detail Mutasi</h2>
                <p class="mt-1 text-sm text-gray-500">Pilih Speedshop tujuan dan isi nomor surat jalan.</p>

                <div class="mt-5">
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700">Speedshop Tujuan <span class="text-red-500">*</span></label>
                    <select x-model="formWarehouseId" class="w-full rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20">
                        <option value="">— Pilih Speedshop —</option>
                        @foreach($warehouses as $w)
                        <option value="{{ $w->id }}">{{ $w->nama }}{{ $w->alamat ? ' — ' . \Illuminate\Support\Str::limit($w->alamat, 40) : '' }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mt-4 space-y-4">
                    <div class="flex items-center justify-between rounded-lg bg-blue-50 px-4 py-3">
                        <span class="text-sm font-medium text-blue-700">Total Produk</span>
                        <span class="text-sm font-bold text-blue-800" x-text="formItems.length + ' item'"></span>
                    </div>
                    <div class="flex items-center justify-between rounded-lg bg-green-50 px-4 py-3">
                        <span class="text-sm font-medium text-green-700">Total Unit</span>
                        <span class="text-sm font-bold text-green-800" x-text="formItems.reduce((s, i) => s + (i.quantity || 0), 0) + ' unit'"></span>
                    </div>
                    <div class="flex items-center justify-between rounded-lg bg-gray-50 px-4 py-3 border border-gray-100">
                        <span class="text-sm font-medium text-gray-700">Total Nilai (Harga Jual Speedshop)</span>
                        <span class="text-sm font-bold text-gray-900" x-text="formatRp(formItems.reduce((s, i) => s + (i.harga_jual_speedshop || 0) * (i.quantity || 0), 0))"></span>
                    </div>
                </div>

                <div class="mt-5">
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700">No. Surat Jalan <span class="text-red-500">*</span></label>
                    <input type="text" x-model="formSuratJalan" class="w-full rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20" placeholder="Contoh: SJ-2026-001">
                </div>

                <div class="mt-4">
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700">Catatan (opsional)</label>
                    <textarea x-model="formCatatan" rows="3" class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm transition focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20" placeholder="Catatan tambahan..."></textarea>
                </div>

                <div class="mt-5 space-y-3">
                    <button type="button" @click="submitMutasi()" :disabled="loading || formItems.length === 0 || !formSuratJalan.trim() || !formWarehouseId"
                            class="flex w-full items-center justify-center gap-2 rounded-xl bg-red-500 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-red-600 disabled:cursor-not-allowed disabled:opacity-50">
                        <svg x-show="!loading" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                        <svg x-show="loading" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        <span x-text="loading ? 'Mengirim...' : 'Kirim ke Speedshop'"></span>
                    </button>
                    <a href="{{ route('warehouse.transaksi-speedshop') }}" class="flex w-full items-center justify-center rounded-xl border border-gray-200 px-5 py-3 text-sm font-medium text-gray-600 transition hover:bg-gray-50">Batal</a>
                </div>
            </div>

            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                <p class="text-sm font-semibold text-amber-800">Stok warehouse akan dikurangi</p>
                <p class="mt-1 text-xs text-amber-700">Setelah mutasi dibuat, stok produk di warehouse akan langsung berkurang. Speedshop akan menerima notifikasi dan dapat memverifikasi penerimaan.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function mutasiKeSpeedshop() {
    const products = @json($products);
    const warehouses = @json($warehouses);

    return {
        loading: false,
        formItems: [],
        formWarehouseId: '',
        formSuratJalan: '',
        formCatatan: '',
        addQty: 1,
        productSelect: null,

        init() {
            this.$nextTick(() => {
                this.productSelect = new TomSelect(this.$refs.productSelect, {
                    valueField: 'id',
                    labelField: 'nama_produk',
                    searchField: ['nama_produk', 'kode_produk'],
                    placeholder: 'Cari produk...',
                    options: products.map(p => ({
                        id: p.id,
                        kode_produk: p.kode_produk,
                        nama_produk: p.nama_produk,
                        jumlah: p.jumlah,
                        harga_jual_speedshop: Number(p.harga_jual_speedshop) || 0,
                    })),
                    render: {
                        option: (data, escape) => `<div class="flex items-center justify-between gap-3"><span>[${escape(data.kode_produk)}] ${escape(data.nama_produk)}</span><span class="text-xs text-gray-400">Stok: ${data.jumlah} · Rp ${new Intl.NumberFormat('id-ID').format(data.harga_jual_speedshop || 0)}</span></div>`,
                        item: (data, escape) => `<div>[${escape(data.kode_produk)}] ${escape(data.nama_produk)}</div>`,
                    }
                });
            });
        },

        addProduct() {
            const val = this.productSelect?.getValue();
            if (!val) return;
            const found = products.find(p => p.id == val);
            if (!found) return;

            if (this.addQty > found.jumlah) {
                Swal.fire('Stok Tidak Cukup', `Stok ${found.nama_produk} tersedia ${found.jumlah} unit.`, 'warning');
                return;
            }

            const exists = this.formItems.find(i => i.product_id == found.id);
            if (exists) {
                exists.quantity = Math.min((exists.quantity || 0) + this.addQty, found.jumlah);
            } else {
                this.formItems.push({
                    product_id: found.id,
                    kode_produk: found.kode_produk,
                    nama_produk: found.nama_produk,
                    jumlah: found.jumlah,
                    harga_jual_speedshop: Number(found.harga_jual_speedshop) || 0,
                    quantity: this.addQty,
                });
            }
            this.addQty = 1;
            this.productSelect.clear();
        },

        formatRp(n) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(Number(n) || 0);
        },

        async submitMutasi() {
            if (this.formItems.length === 0 || !this.formSuratJalan.trim() || !this.formWarehouseId) return;

            const totalUnit = this.formItems.reduce((s, i) => s + (i.quantity || 0), 0);
            const totalNilai = this.formItems.reduce((s, i) => s + (i.harga_jual_speedshop || 0) * (i.quantity || 0), 0);
            const tujuanNama = warehouses.find(w => w.id == this.formWarehouseId)?.nama || 'Speedshop';
            const result = await Swal.fire({
                title: 'Kirim Mutasi ke Speedshop?',
                html: `<div style="text-align:left;font-size:14px;">
                    <p><strong>Speedshop Tujuan:</strong> ${tujuanNama}</p>
                    <p><strong>No. Surat Jalan:</strong> ${this.formSuratJalan}</p>
                    <p><strong>Total:</strong> ${this.formItems.length} produk, ${totalUnit} unit</p>
                    <p><strong>Total Nilai (Harga Jual Speedshop):</strong> ${this.formatRp(totalNilai)}</p>
                    <p class="mt-2 text-gray-500">Stok warehouse akan dikurangi. Speedshop tujuan akan mendapat notifikasi.</p>
                </div>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Kirim!',
                cancelButtonText: 'Batal'
            });
            if (!result.isConfirmed) return;

            this.loading = true;

            const payload = {
                warehouse_id: this.formWarehouseId,
                nomor_surat_jalan: this.formSuratJalan,
                catatan: this.formCatatan || null,
                items: this.formItems.map(i => ({ product_id: i.product_id, quantity: i.quantity })),
            };

            const res = await fetch('{{ route("warehouse.transaksi-speedshop.store") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                body: JSON.stringify(payload)
            });

            if (!res.ok) {
                const d = await res.json().catch(() => ({}));
                this.loading = false;
                Swal.fire('Error', d.message || 'Gagal menyimpan.', 'error');
                return;
            }

            const data = await res.json();
            await Swal.fire('Berhasil', data.message || 'Mutasi ke Speedshop berhasil dibuat.', 'success');
            window.location.href = data.redirect || '{{ route("warehouse.transaksi-speedshop") }}';
        }
    }
}
</script>
@endpush
