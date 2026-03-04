@extends('layouts.produksi')

@section('title', 'Buat Produksi Baru')

@section('content')
<div x-data="productionCreate()" class="space-y-6">
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('produksi.dashboard') }}" class="transition hover:text-gray-700">Home</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('produksi.production.index') }}" class="transition hover:text-gray-700">Produksi</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="font-medium text-gray-900">Buat Baru</span>
    </nav>

    @include('partials.flash')

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Left: BSP Output Sections --}}
        <div class="lg:col-span-2 space-y-6">
            <template x-for="(output, oIdx) in outputs" :key="output._key">
                <div class="rounded-xl border border-gray-100 bg-white shadow-sm overflow-hidden">
                    {{-- Section Header --}}
                    <div class="flex items-center justify-between border-b border-gray-100 bg-gray-50/50 px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-100 text-sm font-bold text-red-600" x-text="oIdx + 1"></div>
                            <div>
                                <h3 class="text-sm font-bold text-gray-900">Bahan Siap Produksi</h3>
                                <p class="text-xs text-gray-500">Pilih BSP dan bahan baku yang digunakan</p>
                            </div>
                        </div>
                        <button x-show="outputs.length > 1" @click="removeOutput(oIdx)" type="button"
                                class="flex h-8 w-8 items-center justify-center rounded-lg text-red-400 transition hover:bg-red-50 hover:text-red-600">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="p-6 space-y-5">
                        {{-- BSP Selection --}}
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                            <div class="sm:col-span-2">
                                <label class="mb-1.5 block text-xs font-semibold text-gray-600">Bahan Siap Produksi <span class="text-red-500">*</span></label>
                                <select :id="'bsp-select-' + output._key" placeholder="Cari atau ketik nama baru..."></select>
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-semibold text-gray-600">Jumlah Target <span class="text-red-500">*</span></label>
                                <input type="number" x-model.number="output.jumlah_target" min="1" class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm text-center focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20">
                            </div>
                        </div>

                        {{-- Bahan Baku Selector --}}
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-gray-600">Bahan Baku</label>
                            <div class="flex flex-wrap items-end gap-3">
                                <div class="flex-1 min-w-[200px]">
                                    <select :id="'bb-select-' + output._key" placeholder="Cari bahan baku..."></select>
                                </div>
                                <div class="w-24">
                                    <input type="number" x-model.number="output._addQty" min="1" class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm text-center focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20" placeholder="Qty">
                                </div>
                                <button @click="addBahanBaku(oIdx)" type="button" class="flex h-[42px] items-center gap-1.5 rounded-xl bg-gray-800 px-4 text-sm font-semibold text-white transition hover:bg-gray-900">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                    Tambah
                                </button>
                            </div>
                        </div>

                        {{-- Bahan Baku Table --}}
                        <div x-show="output.items.length > 0" class="overflow-x-auto rounded-xl border border-gray-100">
                            <table class="w-full text-left text-sm">
                                <thead>
                                    <tr class="bg-gray-50">
                                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Kode</th>
                                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Nama</th>
                                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 text-right">Harga</th>
                                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 text-center">Stok</th>
                                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 text-center">Jumlah</th>
                                        <th class="px-4 py-3"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <template x-for="(item, iIdx) in output.items" :key="iIdx">
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 font-medium text-gray-900" x-text="item.kode"></td>
                                            <td class="px-4 py-3 text-gray-700" x-text="item.nama"></td>
                                            <td class="px-4 py-3 text-right text-gray-700" x-text="formatRupiah(item.harga)"></td>
                                            <td class="px-4 py-3 text-center text-gray-500" x-text="item.stok"></td>
                                            <td class="px-4 py-3 text-center">
                                                <input type="number" x-model.number="item.jumlah" :max="item.stok" min="1" class="w-20 rounded-lg border border-gray-200 px-2 py-1.5 text-center text-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20">
                                            </td>
                                            <td class="px-4 py-3">
                                                <button @click="output.items.splice(iIdx, 1)" type="button" class="flex h-7 w-7 items-center justify-center rounded-lg text-red-400 transition hover:bg-red-50 hover:text-red-600">
                                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        <div x-show="output.items.length === 0" class="rounded-xl border border-dashed border-gray-200 py-8 text-center">
                            <svg class="mx-auto h-8 w-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                            <p class="mt-2 text-sm text-gray-400">Belum ada bahan baku ditambahkan</p>
                        </div>

                        {{-- Section Summary --}}
                        <div x-show="output.items.length > 0" class="flex flex-wrap items-center gap-4 rounded-lg bg-gray-50 px-4 py-3 text-sm">
                            <span class="text-gray-500">Bahan Baku: <strong class="text-gray-800" x-text="output.items.length + ' item'"></strong></span>
                            <span class="text-gray-300">|</span>
                            <span class="text-gray-500">Total Qty: <strong class="text-gray-800" x-text="output.items.reduce((s, i) => s + i.jumlah, 0)"></strong></span>
                            <span class="text-gray-300">|</span>
                            <span class="text-gray-500">Harga: <strong class="text-gray-800" x-text="formatRupiah(output.items.reduce((s, i) => s + (i.harga * i.jumlah), 0))"></strong></span>
                        </div>
                    </div>
                </div>
            </template>

            {{-- Add Output Button --}}
            <button @click="addOutput()" type="button"
                    class="flex w-full items-center justify-center gap-2 rounded-xl border-2 border-dashed border-gray-200 py-4 text-sm font-semibold text-gray-500 transition hover:border-red-300 hover:text-red-500">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Tambah BSP Lainnya
            </button>
        </div>

        {{-- Right: Summary & Submit --}}
        <div class="space-y-6">
            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900">Ringkasan</h2>

                <div class="mt-4 space-y-3">
                    <div class="flex items-center justify-between rounded-lg bg-blue-50 px-4 py-3">
                        <span class="text-sm font-medium text-blue-700">BSP Output</span>
                        <span class="text-sm font-bold text-blue-800" x-text="outputs.length + ' item'"></span>
                    </div>
                    <div class="flex items-center justify-between rounded-lg bg-gray-50 px-4 py-3">
                        <span class="text-sm font-medium text-gray-700">Total Bahan Baku</span>
                        <span class="text-sm font-bold text-gray-900" x-text="outputs.reduce((s, o) => s + o.items.length, 0) + ' item'"></span>
                    </div>
                    <div class="flex items-center justify-between rounded-lg bg-green-50 px-4 py-3">
                        <span class="text-sm font-medium text-green-700">Total Harga</span>
                        <span class="text-sm font-bold text-green-800" x-text="formatRupiah(outputs.reduce((s, o) => s + o.items.reduce((ss, i) => ss + (i.harga * i.jumlah), 0), 0))"></span>
                    </div>
                </div>

                <div class="mt-5">
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700">Team Produksi <span class="text-red-500">*</span></label>
                    <select x-ref="teamSelect" id="team-select" placeholder="Cari team..."></select>
                </div>

                <div class="mt-4">
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700">Catatan (opsional)</label>
                    <textarea x-model="formCatatan" rows="3" class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm transition focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20" placeholder="Catatan tambahan..."></textarea>
                </div>

                <div class="mt-5 space-y-3">
                    <button @click="submitProduction()" :disabled="loading || !canSubmit()"
                            class="flex w-full items-center justify-center gap-2 rounded-xl bg-red-500 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-red-600 disabled:cursor-not-allowed disabled:opacity-50">
                        <svg x-show="!loading" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <svg x-show="loading" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        <span x-text="loading ? 'Menyimpan...' : 'Mulai Produksi'"></span>
                    </button>
                    <a href="{{ route('produksi.production.index') }}" class="flex w-full items-center justify-center rounded-xl border border-gray-200 px-5 py-3 text-sm font-medium text-gray-600 transition hover:bg-gray-50">Batal</a>
                </div>
            </div>

            <div class="rounded-xl border border-green-200 bg-green-50 p-4">
                <div class="flex gap-3">
                    <svg class="h-5 w-5 flex-shrink-0 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div>
                        <p class="text-sm font-semibold text-green-800">Hasil produksi = Bahan Siap Produksi</p>
                        <p class="mt-1 text-xs text-green-700">Stok bahan baku akan berkurang. Setelah laporan selesai, hasilnya menjadi Bahan Siap Produksi dengan stok sendiri.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function productionCreate() {
    const baseUrl = '{{ url("produksi/production") }}';
    const bahanBakus = @json($bahanBakus);
    const teams = @json($teams);
    const bspMaster = @json($bspList);

    return {
        loading: false,
        outputs: [],
        formTeam: '',
        formCatatan: '',
        _selects: {},

        init() {
            this.$nextTick(() => {
                this.teamSelect = new TomSelect(this.$refs.teamSelect, {
                    valueField: 'id',
                    labelField: 'nama',
                    searchField: ['nama'],
                    placeholder: 'Cari team...',
                    options: teams.map(t => ({ id: t.id, nama: t.nama })),
                    onChange: (val) => { this.formTeam = val; },
                });

                this.addOutput();
            });
        },

        addOutput() {
            const key = 'out_' + Date.now() + '_' + Math.random().toString(36).substr(2, 5);
            this.outputs.push({
                _key: key,
                bsp_id: null,
                bsp_nama: '',
                bsp_is_new: false,
                jumlah_target: 1,
                items: [],
                _addQty: 1,
                _addBBId: '',
            });

            setTimeout(() => this.initSelectsFor(key), 100);
        },

        removeOutput(idx) {
            const key = this.outputs[idx]._key;
            if (this._selects[key]) {
                this._selects[key].bsp?.destroy();
                this._selects[key].bb?.destroy();
                delete this._selects[key];
            }
            this.outputs.splice(idx, 1);
        },

        initSelectsFor(key, retries = 3) {
            const output = this.outputs.find(o => o._key === key);
            if (!output) return;

            const bspEl = document.getElementById('bsp-select-' + key);
            const bbEl = document.getElementById('bb-select-' + key);

            if (!bspEl || !bbEl) {
                if (retries > 0) setTimeout(() => this.initSelectsFor(key, retries - 1), 150);
                return;
            }

            const bspInstance = new TomSelect(bspEl, {
                valueField: 'id',
                labelField: 'nama',
                searchField: ['nama', 'kode'],
                placeholder: 'Cari atau ketik nama baru...',
                create: function(input, callback) {
                    callback({ id: '__new__' + Date.now(), kode: '(Baru)', nama: input });
                },
                options: bspMaster.map(b => ({ id: b.id, kode: b.kode, nama: b.nama })),
                render: {
                    option: (data, escape) => `<div class="flex items-center justify-between gap-3"><span>${escape(data.nama)}</span><span class="text-xs text-gray-400">${escape(data.kode)}</span></div>`,
                    item: (data, escape) => `<div>${escape(data.nama)}</div>`,
                    option_create: (data, escape) => `<div class="create px-3 py-2 text-sm"><span class="text-red-500 font-semibold">+ Buat baru:</span> <strong>${escape(data.input)}</strong></div>`,
                },
                onChange: (value) => {
                    if (String(value).startsWith('__new__')) {
                        const option = bspInstance.options[value];
                        output.bsp_id = null;
                        output.bsp_nama = option?.nama || '';
                        output.bsp_is_new = true;
                    } else {
                        output.bsp_id = parseInt(value);
                        output.bsp_nama = '';
                        output.bsp_is_new = false;
                    }
                },
            });

            const bbInstance = new TomSelect(bbEl, {
                valueField: 'id',
                labelField: 'label',
                searchField: ['label', 'kode'],
                placeholder: 'Cari bahan baku...',
                options: bahanBakus.map(i => ({ id: i.id, kode: i.kode, nama: i.nama, harga: i.harga, stok: i.stok, label: `[${i.kode}] ${i.nama}` })),
                render: {
                    option: (data, escape) => `<div class="flex items-center justify-between gap-3"><span><span class="font-medium">[${escape(data.kode)}]</span> ${escape(data.nama)}</span><span class="text-xs text-gray-400">Stok: ${data.stok}</span></div>`,
                    item: (data, escape) => `<div>[${escape(data.kode)}] ${escape(data.nama)}</div>`,
                },
                onChange: (val) => { output._addBBId = val; },
            });

            this._selects[key] = { bsp: bspInstance, bb: bbInstance };
        },

        addBahanBaku(oIdx) {
            const output = this.outputs[oIdx];
            if (!output._addBBId || output._addQty < 1) return;

            const found = bahanBakus.find(b => b.id == output._addBBId);
            if (!found) return;

            if (output._addQty > found.stok) {
                Swal.fire('Stok Tidak Cukup', `Stok ${found.nama} hanya ${found.stok}.`, 'warning');
                return;
            }

            const exists = output.items.find(i => i.bahan_baku_id == found.id);
            if (exists) {
                exists.jumlah = Math.min(exists.jumlah + output._addQty, found.stok);
            } else {
                output.items.push({
                    bahan_baku_id: found.id,
                    kode: found.kode,
                    nama: found.nama,
                    harga: found.harga || 0,
                    stok: found.stok,
                    jumlah: output._addQty,
                });
            }

            output._addQty = 1;
            output._addBBId = '';
            const bbSelect = this._selects[output._key]?.bb;
            if (bbSelect) bbSelect.clear();
        },

        formatRupiah(val) {
            return 'Rp ' + Number(val || 0).toLocaleString('id-ID');
        },

        canSubmit() {
            if (!this.formTeam || this.outputs.length === 0) return false;
            return this.outputs.every(o =>
                (o.bsp_id || o.bsp_is_new) &&
                o.jumlah_target > 0 &&
                o.items.length > 0
            );
        },

        async submitProduction() {
            if (!this.canSubmit()) return;

            const bspNames = this.outputs.map((o, i) => {
                const name = o.bsp_is_new ? `${o.bsp_nama} (baru)` : (bspMaster.find(b => b.id == o.bsp_id)?.nama || '-');
                return `${i + 1}. ${name} — target ${o.jumlah_target} unit`;
            }).join('<br>');

            const result = await Swal.fire({
                title: 'Mulai Produksi?',
                html: `<div style="text-align:left;font-size:14px;">
                    <p class="mb-2"><strong>BSP yang akan diproduksi:</strong></p>
                    <p>${bspNames}</p>
                    <p class="mt-3 text-gray-500">Stok bahan baku akan dikurangi.</p>
                </div>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Mulai!',
                cancelButtonText: 'Batal'
            });
            if (!result.isConfirmed) return;

            this.loading = true;

            const payload = {
                team_produksi_id: this.formTeam,
                catatan: this.formCatatan || null,
                outputs: this.outputs.map(o => ({
                    bsp_id: o.bsp_is_new ? null : o.bsp_id,
                    bsp_nama: o.bsp_is_new ? o.bsp_nama : null,
                    jumlah_target: o.jumlah_target,
                    items: o.items.map(i => ({ bahan_baku_id: i.bahan_baku_id, jumlah: i.jumlah })),
                })),
            };

            const res = await fetch(baseUrl, {
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

            window.location.href = '{{ route("produksi.production.index") }}';
        }
    }
}
</script>
@endpush
