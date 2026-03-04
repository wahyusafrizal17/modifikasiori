@extends('layouts.speedshop')

@section('title', 'Input No. Invoice / Surat Jalan - Pembelian MO')

@section('content')
<div x-data="pembelianMOCreate()" class="space-y-6">
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('speedshop.dashboard') }}" class="transition hover:text-gray-700">Home</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('speedshop.stock-in.pembelian-mo.index') }}" class="transition hover:text-gray-700">Pembelian MO</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="font-medium text-gray-900">Input Baru</span>
    </nav>

    <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
        <h1 class="text-xl font-bold text-gray-900">Cari No. Invoice / Surat Jalan</h1>
        <p class="mt-1 text-sm text-gray-500">Masukkan nomor invoice atau surat jalan untuk melihat daftar produk mutasi dari warehouse</p>

        <div class="mt-5 flex flex-wrap items-end gap-3">
            <div class="min-w-[240px] flex-1">
                <label class="mb-1.5 block text-xs font-semibold text-gray-600">No. Invoice / Surat Jalan</label>
                <input type="text" x-model="searchNo" @keydown.enter.prevent="doLookup()"
                       placeholder="Contoh: SJ-2024-001 atau INV-001"
                       class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm transition focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20">
            </div>
            <button @click="doLookup()" :disabled="loading" class="flex h-11 items-center gap-2 rounded-xl bg-red-500 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-600 disabled:cursor-not-allowed disabled:opacity-50">
                <svg x-show="!loading" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <svg x-show="loading" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                Cari
            </button>
        </div>

        <template x-if="errorMsg">
            <div class="mt-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" x-text="errorMsg"></div>
        </template>

        <template x-if="found && result">
            <div class="mt-6 space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <h2 class="text-lg font-bold text-gray-900">Daftar Produk Ditemukan</h2>
                    <template x-if="result.type === 'mutasi'">
                        <a :href="detailUrl" class="inline-flex items-center gap-2 rounded-xl bg-emerald-500 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-600">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Lihat Detail & Verifikasi
                        </a>
                    </template>
                </div>

                <div class="overflow-x-auto rounded-xl border border-gray-100">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">No.</th>
                                <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Kode Produk</th>
                                <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Nama Produk</th>
                                <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 text-center">Qty</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <template x-for="(item, idx) in (result?.items || [])" :key="idx">
                                <tr class="transition hover:bg-gray-50">
                                    <td class="px-5 py-4 text-gray-500" x-text="idx + 1"></td>
                                    <td class="px-5 py-4 font-medium text-gray-900" x-text="item.kode_produk"></td>
                                    <td class="px-5 py-4 text-gray-700" x-text="item.nama_produk"></td>
                                    <td class="px-5 py-4 text-center font-semibold text-gray-900" x-text="item.qty"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <template x-if="result.type === 'transaksi_offline'">
                    <p class="text-sm text-amber-700">Transaksi dari warehouse. Untuk mutasi, gunakan nomor surat jalan.</p>
                </template>
            </div>
        </template>
    </div>
</div>
@endsection

@push('scripts')
<script>
function pembelianMOCreate() {
    return {
        searchNo: '',
        loading: false,
        found: false,
        result: null,
        errorMsg: '',
        detailUrl: '',

        get lookupUrl() {
            return '{{ route("speedshop.stock-in.pembelian-mo.lookup") }}?no=' + encodeURIComponent(this.searchNo || '');
        },

        async doLookup() {
            const no = (this.searchNo || '').trim();
            if (!no) {
                this.errorMsg = 'Masukkan nomor invoice atau surat jalan terlebih dahulu.';
                this.found = false;
                this.result = null;
                return;
            }

            this.loading = true;
            this.errorMsg = '';
            this.found = false;
            this.result = null;
            this.detailUrl = '';

            try {
                const res = await fetch(this.lookupUrl, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();

                if (data.found && data.data) {
                    this.found = true;
                    this.result = { type: data.type, ...data.data };
                    if (data.type === 'mutasi' && data.data.id) {
                        this.detailUrl = '{{ url("speedshop/stock-in/pembelian-mo/mutasi") }}/' + data.data.id;
                    }
                } else {
                    this.errorMsg = 'Data tidak ditemukan. Pastikan nomor invoice atau surat jalan benar dan status mutasi "dikirim".';
                }
            } catch (e) {
                this.errorMsg = 'Terjadi kesalahan. Coba lagi.';
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
@endpush
