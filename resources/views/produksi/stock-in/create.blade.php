@extends('layouts.produksi')

@section('title', 'Input Stock IN Baru')

@section('content')
<div x-data="stockInCreate()" class="space-y-6">
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('produksi.dashboard') }}" class="transition hover:text-gray-700">Home</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('produksi.stock-in.index') }}" class="transition hover:text-gray-700">Stock IN</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="font-medium text-gray-900">Input Baru</span>
    </nav>

    @include('partials.flash')

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Left: Add Items --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900">Tambah Item</h2>
                <p class="mt-1 text-sm text-gray-500">Pilih bahan baku atau kemasan yang masuk, lalu masukkan jumlahnya</p>

                <div class="mt-5 flex flex-wrap items-end gap-3">
                    <div class="w-40">
                        <label class="mb-1.5 block text-xs font-semibold text-gray-600">Tipe</label>
                        <select x-model="addType" @change="onTypeChange()" class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20">
                            <option value="bahan_baku">Bahan Baku</option>
                            <option value="kemasan">Kemasan</option>
                        </select>
                    </div>
                    <div class="flex-1 min-w-[220px]">
                        <label class="mb-1.5 block text-xs font-semibold text-gray-600">Item</label>
                        <select x-ref="itemSelect" id="item-select" placeholder="Cari item..."></select>
                    </div>
                    <div class="w-28">
                        <label class="mb-1.5 block text-xs font-semibold text-gray-600">Jumlah</label>
                        <input type="number" x-model.number="addJumlah" min="1" class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm text-center focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20">
                    </div>
                    <button @click="addItem()" class="flex h-[42px] items-center gap-2 rounded-xl bg-red-500 px-5 text-sm font-semibold text-white transition hover:bg-red-600">
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
                                <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Tipe</th>
                                <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Kode</th>
                                <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Nama Item</th>
                                <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 text-right">Harga</th>
                                <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 text-center">Jumlah</th>
                                <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 text-right">Subtotal</th>
                                <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <template x-for="(item, idx) in formItems" :key="idx">
                                <tr class="transition hover:bg-gray-50">
                                    <td class="px-5 py-4 text-gray-500" x-text="idx + 1"></td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex items-center rounded-lg px-1 py-1 text-xs font-semibold"
                                              :class="item.type === 'bahan_baku' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700'"
                                              x-text="item.type === 'bahan_baku' ? 'Bahan Baku' : 'Kemasan'"></span>
                                    </td>
                                    <td class="px-5 py-4 font-medium text-gray-900" x-text="item.kode"></td>
                                    <td class="px-5 py-4 text-gray-700" x-text="item.nama"></td>
                                    <td class="px-5 py-4 text-right text-gray-700" x-text="formatRupiah(item.harga)"></td>
                                    <td class="px-5 py-4 text-center">
                                        <input type="number" x-model.number="item.jumlah" min="1" class="w-20 rounded-lg border border-gray-200 px-2 py-1.5 text-center text-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20">
                                    </td>
                                    <td class="px-5 py-4 text-right font-medium text-gray-900" x-text="formatRupiah(item.harga * item.jumlah)"></td>
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
                                        <svg class="mx-auto h-10 w-10 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                        <p class="mt-3 text-sm font-medium">Belum ada item ditambahkan</p>
                                        <p class="mt-1 text-xs">Pilih bahan baku atau kemasan di atas</p>
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
                    <div class="flex items-center justify-between rounded-lg bg-blue-50 px-4 py-3">
                        <span class="text-sm font-medium text-blue-700">Bahan Baku</span>
                        <span class="text-sm font-bold text-blue-800" x-text="formItems.filter(i => i.type === 'bahan_baku').length + ' item'"></span>
                    </div>
                    <div class="flex items-center justify-between rounded-lg bg-purple-50 px-4 py-3">
                        <span class="text-sm font-medium text-purple-700">Kemasan</span>
                        <span class="text-sm font-bold text-purple-800" x-text="formItems.filter(i => i.type === 'kemasan').length + ' item'"></span>
                    </div>
                    <div class="flex items-center justify-between rounded-lg bg-gray-50 px-4 py-3">
                        <span class="text-sm font-medium text-gray-700">Total Qty</span>
                        <span class="text-sm font-bold text-gray-900" x-text="formItems.reduce((sum, i) => sum + i.jumlah, 0)"></span>
                    </div>
                    <div class="flex items-center justify-between rounded-lg bg-green-50 px-4 py-3">
                        <span class="text-sm font-medium text-green-700">Total Harga</span>
                        <span class="text-sm font-bold text-green-800" x-text="formatRupiah(formItems.reduce((sum, i) => sum + (i.harga * i.jumlah), 0))"></span>
                    </div>
                </div>

                <div class="mt-5">
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700">Catatan (opsional)</label>
                    <textarea x-model="formCatatan" rows="3" class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm transition focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20" placeholder="Catatan tambahan..."></textarea>
                </div>

                <div class="mt-5 space-y-3">
                    <button @click="submitStockIn()" :disabled="loading || formItems.length === 0"
                            class="flex w-full items-center justify-center gap-2 rounded-xl bg-red-500 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-red-600 disabled:cursor-not-allowed disabled:opacity-50">
                        <svg x-show="!loading" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <svg x-show="loading" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        <span x-text="loading ? 'Mengirim...' : 'Submit untuk Verifikasi'"></span>
                    </button>
                    <a href="{{ route('produksi.stock-in.index') }}" class="flex w-full items-center justify-center rounded-xl border border-gray-200 px-5 py-3 text-sm font-medium text-gray-600 transition hover:bg-gray-50">
                        Batal
                    </a>
                </div>
            </div>

            <div class="rounded-xl border border-yellow-200 bg-yellow-50 p-4">
                <div class="flex gap-3">
                    <svg class="h-5 w-5 flex-shrink-0 text-yellow-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div>
                        <p class="text-sm font-semibold text-yellow-800">Perlu Verifikasi Manager</p>
                        <p class="mt-1 text-xs text-yellow-700">Setelah disubmit, stock in akan menunggu persetujuan Manager Produksi. Stok baru bertambah setelah disetujui.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function stockInCreate() {
    const baseUrl = '{{ url("produksi/stock-in") }}';
    return {
        loading: false,
        addType: 'bahan_baku',
        addItemId: '',
        addJumlah: 1,
        formItems: [],
        formCatatan: '',
        tomSelect: null,

        bahanBakus: @json($bahanBakus),
        kemasans: @json($kemasans),

        init() {
            this.$nextTick(() => {
                this.tomSelect = new TomSelect(this.$refs.itemSelect, {
                    valueField: 'id',
                    labelField: 'label',
                    searchField: ['label', 'kode'],
                    placeholder: 'Cari item...',
                    options: this.buildOptions(),
                    onChange: (val) => { this.addItemId = val; },
                    render: {
                        option: (data, escape) => {
                            return `<div class="flex items-center justify-between gap-3">
                                <span><span class="font-medium text-gray-900">[${escape(data.kode)}]</span> ${escape(data.nama)}</span>
                                <span class="text-xs text-gray-400">Rp ${Number(data.harga || 0).toLocaleString('id-ID')}</span>
                            </div>`;
                        },
                        item: (data, escape) => {
                            return `<div>[${escape(data.kode)}] ${escape(data.nama)}</div>`;
                        },
                    }
                });
            });
        },

        buildOptions() {
            const list = this.addType === 'bahan_baku' ? this.bahanBakus : this.kemasans;
            return list.map(i => ({ id: i.id, kode: i.kode, nama: i.nama, harga: i.harga, label: `[${i.kode}] ${i.nama}` }));
        },

        onTypeChange() {
            this.addItemId = '';
            if (this.tomSelect) {
                this.tomSelect.clear();
                this.tomSelect.clearOptions();
                this.tomSelect.addOptions(this.buildOptions());
            }
        },

        formatRupiah(val) {
            return 'Rp ' + Number(val || 0).toLocaleString('id-ID');
        },

        addItem() {
            if (!this.addItemId || this.addJumlah < 1) return;

            const list = this.addType === 'bahan_baku' ? this.bahanBakus : this.kemasans;
            const found = list.find(i => i.id == this.addItemId);
            if (!found) return;

            const exists = this.formItems.find(i => i.type === this.addType && i.id == this.addItemId);
            if (exists) {
                exists.jumlah += this.addJumlah;
            } else {
                this.formItems.push({
                    type: this.addType,
                    id: found.id,
                    kode: found.kode,
                    nama: found.nama,
                    harga: found.harga || 0,
                    jumlah: this.addJumlah,
                });
            }

            this.addItemId = '';
            this.addJumlah = 1;
            if (this.tomSelect) this.tomSelect.clear();
        },

        async submitStockIn() {
            if (this.formItems.length === 0) return;

            const result = await Swal.fire({
                title: 'Submit Stock IN?',
                text: 'Data akan dikirim untuk diverifikasi oleh Manager Produksi.',
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
                items: this.formItems.map(i => ({ type: i.type, id: i.id, jumlah: i.jumlah })),
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

            window.location.href = '{{ route("produksi.stock-in.index") }}';
        }
    }
}
</script>
@endpush
