@extends('layouts.speedshop')

@section('title', 'Estimasi Service')

@section('content')
<style>
@media print {
    body * { visibility: hidden; }
    #estimasi-print-area, #estimasi-print-area * { visibility: visible; }
    #estimasi-print-area { position: absolute; left: 0; top: 0; width: 100%; }
    .no-print { display: none !important; }
}
</style>

<div x-data="estimasiService()" class="space-y-6">
    <nav class="flex items-center gap-2 text-sm text-gray-500 no-print">
        <a href="{{ route('speedshop.dashboard') }}" class="transition hover:text-gray-700">Home</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="font-medium text-gray-900">Estimasi Service</span>
    </nav>

    <div class="no-print">
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            {{-- Left: Form & Items --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Informasi Servis --}}
                <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-bold text-gray-900">Informasi Servis</h2>
                    <p class="mt-1 text-sm text-gray-500">Data pelanggan, kendaraan, dan detail service untuk estimasi</p>

                    <div class="mt-5 space-y-4">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-xs font-semibold text-gray-600">Pelanggan *</label>
                                <div class="flex gap-2 items-center">
                                    <select id="pelanggan-select-estimasi" class="flex-1 rounded-xl bg-white text-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20">
                                        <option value="">Pilih Pelanggan</option>
                                        @foreach($pelanggans as $p)
                                        <option value="{{ $p->id }}">{{ $p->nama }}{{ $p->no_hp ? " ({$p->no_hp})" : '' }}</option>
                                        @endforeach
                                    </select>
                                    <button type="button" @click="showQuickAddModal = true" class="flex-shrink-0 rounded-lg p-2 text-gray-500 transition hover:bg-gray-100 hover:text-gray-700" title="Tambah Pelanggan & Kendaraan Baru">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-semibold text-gray-600">Kendaraan *</label>
                                <select x-model="kendaraanId" :disabled="!pelangganId" class="w-full rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20 disabled:bg-gray-50 disabled:cursor-not-allowed">
                                    <option value="">- Pilih Kendaraan -</option>
                                    <template x-for="k in kendaraans" :key="k.id">
                                        <option :value="k.id" x-text="k.nomor_polisi + ' - ' + k.merk + ' ' + (k.tipe || '')"></option>
                                    </template>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-xs font-semibold text-gray-600">Tanggal</label>
                                <div class="relative">
                                    <input type="date" x-model="tanggalEstimasi" class="w-full rounded-xl border border-gray-200 bg-white px-4 py-2.5 pr-10 text-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20">
                                </div>
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-semibold text-gray-600">Sumber Kedatangan</label>
                                <select x-model="sumberKedatangan" class="w-full rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20">
                                    <option value="">-- Pilih --</option>
                                    @foreach(\App\Models\ServiceOrder::SUMBER_KEDATANGAN as $val => $label)
                                    <option value="{{ $val }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-semibold text-gray-600">Kategori Service</label>
                                <select x-model="kategoriService" class="w-full rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20">
                                    <option value="">-- Pilih --</option>
                                    @foreach(\App\Models\ServiceOrder::KATEGORI_SERVICE as $val => $label)
                                    <option value="{{ $val }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-gray-600">Keluhan</label>
                            <textarea x-model="keluhan" rows="3" class="w-full rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20" placeholder="Keluhan / deskripsi service"></textarea>
                        </div>
                    </div>
                </div>

                {{-- Modal Quick Add Pelanggan & Kendaraan --}}
                <div x-show="showQuickAddModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" @keydown.escape.window="showQuickAddModal = false">
                    <div x-show="showQuickAddModal" x-transition.opacity class="fixed inset-0 bg-black/50" @click="showQuickAddModal = false"></div>
                    <div class="flex min-h-full items-center justify-center p-4">
                        <div x-show="showQuickAddModal" x-transition class="relative w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl" @click.stop>
                            <h3 class="text-lg font-bold text-gray-900">Tambah Pelanggan & Kendaraan Baru</h3>
                            <p class="mt-1 text-sm text-gray-500">Isi data pelanggan dan kendaraan sekaligus</p>
                            <div class="mt-5 space-y-4">
                                <div class="rounded-lg border border-gray-100 bg-gray-50/50 p-4">
                                    <p class="mb-3 text-xs font-bold uppercase tracking-wider text-gray-500">Data Pelanggan</p>
                                    <div class="space-y-3">
                                        <div>
                                            <label class="mb-1 block text-xs font-medium text-gray-700">Nama *</label>
                                            <input type="text" x-model="quickAdd.nama" class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20" placeholder="Nama pelanggan">
                                        </div>
                                        <div>
                                            <label class="mb-1 block text-xs font-medium text-gray-700">No HP</label>
                                            <input type="text" x-model="quickAdd.no_hp" class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20" placeholder="08xxxx">
                                        </div>
                                        <div>
                                            <label class="mb-1 block text-xs font-medium text-gray-700">Alamat</label>
                                            <textarea x-model="quickAdd.alamat" rows="2" class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20" placeholder="Alamat"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="rounded-lg border border-gray-100 bg-gray-50/50 p-4 mt-4">
                                    <p class="mb-3 text-xs font-bold uppercase tracking-wider text-gray-500">Data Kendaraan</p>
                                    <div class="space-y-3">
                                        <div>
                                            <label class="mb-1 block text-xs font-medium text-gray-700">No Polisi *</label>
                                            <input type="text" x-model="quickAdd.nomor_polisi" class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20" placeholder="B 1234 XYZ">
                                        </div>
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="mb-1 block text-xs font-medium text-gray-700">Merk *</label>
                                                <input type="text" x-model="quickAdd.merk" class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20" placeholder="Honda">
                                            </div>
                                            <div>
                                                <label class="mb-1 block text-xs font-medium text-gray-700">Tipe</label>
                                                <input type="text" x-model="quickAdd.tipe" class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20" placeholder="Beat">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="mb-1 block text-xs font-medium text-gray-700">Tahun</label>
                                            <input type="number" x-model="quickAdd.tahun" min="1990" max="2030" class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20" placeholder="2024">
                                        </div>
                                    </div>
                                </div>
                                <div class="flex gap-3 pt-2">
                                    <button type="button" @click="submitQuickAdd()" :disabled="quickAddLoading" class="flex-1 rounded-xl bg-red-500 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-red-600 disabled:opacity-50">
                                        <span x-show="!quickAddLoading">Simpan & Pilih</span>
                                        <span x-show="quickAddLoading">Menyimpan...</span>
                                    </button>
                                    <button type="button" @click="showQuickAddModal = false" class="rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-600 transition hover:bg-gray-50">Batal</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Jasa Servis --}}
                <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Jasa Servis</h2>
                            <p class="mt-1 text-sm text-gray-500">Tambah jasa servis untuk estimasi</p>
                        </div>
                        <span class="inline-flex items-center rounded-lg bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600" x-text="formJasa.length + ' jasa'"></span>
                    </div>
                    <div class="mt-4 flex gap-4">
                        <div class="flex-1">
                            <label class="mb-1.5 block text-xs font-semibold text-gray-600">Jasa</label>
                            <select id="jasa-select-estimasi" class="w-full rounded-xl bg-white text-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20"></select>
                        </div>
                        <div class="flex items-end">
                            <button type="button" @click="addJasa()" class="rounded-xl bg-red-500 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-red-600">+ Tambah Jasa</button>
                        </div>
                    </div>
                    <div class="mt-4 overflow-x-auto rounded-xl border border-gray-100">
                        <table class="w-full text-left text-sm">
                            <thead><tr class="bg-gray-50"><th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">No.</th><th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Nama Jasa</th><th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 text-right">Biaya</th><th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 w-16"></th></tr></thead>
                            <tbody class="divide-y divide-gray-100">
                                <template x-for="(j, idx) in formJasa" :key="j.id + '-' + idx">
                                    <tr class="transition hover:bg-gray-50">
                                        <td class="px-5 py-4 text-gray-500" x-text="idx + 1"></td>
                                        <td class="px-5 py-4 font-medium text-gray-900" x-text="j.nama"></td>
                                        <td class="px-5 py-4 text-right">
                                            <input type="number" x-model.number="j.biaya" min="0" class="w-32 rounded-lg border border-gray-200 px-3 py-1.5 text-right text-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20">
                                        </td>
                                        <td class="px-5 py-4">
                                            <button type="button" @click="formJasa.splice(idx, 1)" class="rounded-lg p-1.5 text-red-400 transition hover:bg-red-50 hover:text-red-600">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                                <template x-if="formJasa.length === 0">
                                    <tr><td colspan="4" class="px-5 py-8 text-center text-gray-400">Belum ada jasa ditambahkan.</td></tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Sparepart --}}
                <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Sparepart</h2>
                            <p class="mt-1 text-sm text-gray-500">Tambah produk sparepart untuk estimasi</p>
                        </div>
                        <span class="inline-flex items-center rounded-lg bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600" x-text="formSparepart.length + ' item'"></span>
                    </div>
                    <div class="mt-4 flex gap-4 flex-wrap">
                        <div class="flex-1 min-w-[200px]">
                            <label class="mb-1.5 block text-xs font-semibold text-gray-600">Produk</label>
                            <select x-ref="sparepartSelect" id="product-select-estimasi" class="w-full rounded-xl bg-white text-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20"></select>
                        </div>
                        <div class="w-24">
                            <label class="mb-1.5 block text-xs font-semibold text-gray-600">Qty</label>
                            <input type="number" x-model.number="addSparepartQty" min="1" class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-center text-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20">
                        </div>
                        <div class="flex items-end">
                            <button type="button" @click="addSparepart()" class="rounded-xl bg-red-500 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-red-600">+ Tambah Sparepart</button>
                        </div>
                    </div>
                    <div class="mt-4 overflow-x-auto rounded-xl border border-gray-100">
                        <table class="w-full text-left text-sm">
                            <thead><tr class="bg-gray-50"><th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">No.</th><th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Kode</th><th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Nama</th><th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 text-center">Qty</th><th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 text-right">Harga</th><th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 text-right">Subtotal</th><th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 w-16"></th></tr></thead>
                            <tbody class="divide-y divide-gray-100">
                                <template x-for="(s, idx) in formSparepart" :key="s.product_id + '-' + idx">
                                    <tr class="transition hover:bg-gray-50">
                                        <td class="px-5 py-4 text-gray-500" x-text="idx + 1"></td>
                                        <td class="px-5 py-4 font-medium text-gray-900" x-text="s.kode_produk"></td>
                                        <td class="px-5 py-4 text-gray-700" x-text="s.nama_produk"></td>
                                        <td class="px-5 py-4 text-center">
                                            <input type="number" x-model.number="s.qty" min="1" @change="s.subtotal = s.qty * s.harga" class="w-16 rounded-lg border border-gray-200 px-2 py-1.5 text-center text-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20">
                                        </td>
                                        <td class="px-5 py-4 text-right">
                                            <input type="number" x-model.number="s.harga" min="0" @change="s.subtotal = s.qty * s.harga" class="w-28 rounded-lg border border-gray-200 px-2 py-1.5 text-right text-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20">
                                        </td>
                                        <td class="px-5 py-4 text-right font-medium text-gray-900" x-text="formatRupiah((s.qty * s.harga))"></td>
                                        <td class="px-5 py-4">
                                            <button type="button" @click="formSparepart.splice(idx, 1)" class="rounded-lg p-1.5 text-red-400 transition hover:bg-red-50 hover:text-red-600">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                                <template x-if="formSparepart.length === 0">
                                    <tr><td colspan="7" class="px-5 py-8 text-center text-gray-400">Belum ada sparepart ditambahkan.</td></tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Right: Ringkasan & Button --}}
            <div class="space-y-6">
                <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-bold text-gray-900">Ringkasan</h2>
                    <p class="mt-1 text-sm text-gray-500">Estimasi biaya service</p>
                    <div class="mt-5 space-y-3">
                        <div class="flex items-center justify-between rounded-lg bg-blue-50 px-4 py-3">
                            <span class="text-sm font-medium text-blue-700">Total Jasa</span>
                            <span class="text-sm font-bold text-blue-800" x-text="formatRupiah(totalJasa)"></span>
                        </div>
                        <div class="flex items-center justify-between rounded-lg bg-gray-50 px-4 py-3">
                            <span class="text-sm font-medium text-gray-700">Total Sparepart</span>
                            <span class="text-sm font-bold text-gray-900" x-text="formatRupiah(totalSparepart)"></span>
                        </div>
                        <div class="flex items-center justify-between rounded-lg bg-emerald-50 px-4 py-3">
                            <span class="text-sm font-medium text-emerald-700">Estimasi Total</span>
                            <span class="text-sm font-bold text-emerald-800" x-text="formatRupiah(totalJasa + totalSparepart)"></span>
                        </div>
                    </div>
                    <div class="mt-6 space-y-3">
                        <button type="button" @click="hitungEstimasi()" class="flex w-full items-center justify-center gap-2 rounded-xl bg-red-500 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-red-600">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            Hitung Estimasi
                        </button>
                        <a href="{{ route('speedshop.dashboard') }}" class="flex w-full items-center justify-center gap-2 rounded-xl border border-gray-200 px-5 py-3 text-sm font-medium text-gray-600 transition hover:bg-gray-50">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Invoice-like Result --}}
    <div id="estimasi-print-area" x-show="showEstimasiResult" x-cloak class="mt-8 rounded-xl border border-gray-200 bg-white p-8 shadow-sm print:shadow-none" x-transition>
        <div class="mb-6 text-center no-print">
            <button type="button" @click="window.print()" class="inline-flex items-center gap-2 rounded-xl bg-red-500 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-red-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak Estimasi
            </button>
        </div>
        <div class="border-b-2 border-red-500 pb-4">
            <h1 class="text-2xl font-bold text-red-600">ESTIMASI</h1>
            <p class="text-gray-700">ModifikasiOri Bengkel</p>
        </div>
        <div class="mt-4 flex justify-end text-right text-sm text-gray-600">
            <div>
                <p>Tanggal: <span x-text="formatDate(tanggalEstimasi)"></span></p>
            </div>
        </div>
        <div class="mt-6 grid grid-cols-2 gap-8">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Pelanggan</p>
                <p class="mt-1 font-medium text-gray-900" x-text="selectedPelanggan?.nama || '-'"></p>
                <p class="text-sm text-gray-600" x-text="selectedPelanggan?.no_hp || '-'"></p>
                <p class="text-sm text-gray-600" x-text="selectedPelanggan?.alamat || '-'"></p>
                <p class="text-sm text-gray-600" x-text="selectedPelanggan?.kota || '-'"></p>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Kendaraan</p>
                <p class="mt-1 font-bold text-gray-900" x-text="selectedKendaraan?.nomor_polisi || '-'"></p>
                <p class="text-sm text-gray-600" x-text="(selectedKendaraan ? selectedKendaraan.merk + ' ' + (selectedKendaraan.tipe || '') : '-').trim()"></p>
                <p class="text-sm text-gray-600" x-text="selectedKendaraan?.tahun ? 'Tahun ' + selectedKendaraan.tahun : '-'"></p>
            </div>
        </div>
        <div class="mt-8">
            <p class="mb-2 text-xs font-bold uppercase tracking-wider text-gray-500">Jasa Servis</p>
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="pb-2 font-semibold text-gray-600">Nama Jasa</th>
                        <th class="pb-2 text-right font-semibold text-gray-600">Biaya</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(j, idx) in formJasa" :key="j.id + '-print-' + idx">
                        <tr class="border-b border-gray-100">
                            <td class="py-2 text-gray-900" x-text="j.nama"></td>
                            <td class="py-2 text-right text-gray-700" x-text="formatRupiah(j.biaya)"></td>
                        </tr>
                    </template>
                    <template x-if="formJasa.length === 0">
                        <tr><td colspan="2" class="py-4 text-center text-gray-400">-</td></tr>
                    </template>
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            <p class="mb-2 text-xs font-bold uppercase tracking-wider text-gray-500">Sparepart</p>
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="pb-2 font-semibold text-gray-600">Nama</th>
                        <th class="pb-2 text-center font-semibold text-gray-600">Qty</th>
                        <th class="pb-2 text-right font-semibold text-gray-600">Harga</th>
                        <th class="pb-2 text-right font-semibold text-gray-600">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(s, idx) in formSparepart" :key="s.product_id + '-print-' + idx">
                        <tr class="border-b border-gray-100">
                            <td class="py-2 text-gray-900" x-text="s.nama_produk"></td>
                            <td class="py-2 text-center text-gray-700" x-text="s.qty"></td>
                            <td class="py-2 text-right text-gray-700" x-text="formatRupiah(s.harga)"></td>
                            <td class="py-2 text-right text-gray-700" x-text="formatRupiah(s.qty * s.harga)"></td>
                        </tr>
                    </template>
                    <template x-if="formSparepart.length === 0">
                        <tr><td colspan="4" class="py-4 text-center text-gray-400">-</td></tr>
                    </template>
                </tbody>
            </table>
        </div>
        <div class="mt-8 flex justify-end">
            <div class="w-64 space-y-2 text-right">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Total Jasa</span>
                    <span x-text="formatRupiah(totalJasa)"></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Total Sparepart</span>
                    <span x-text="formatRupiah(totalSparepart)"></span>
                </div>
                <div class="border-t border-gray-200 pt-3">
                    <div class="flex justify-between font-bold text-red-600">
                        <span>Grand Total</span>
                        <span x-text="formatRupiah(totalJasa + totalSparepart)"></span>
                    </div>
                </div>
            </div>
        </div>
        <p class="mt-12 text-center text-sm text-gray-500">Terima kasih telah mempercayakan kendaraan Anda kepada kami.</p>
        <p class="mt-2 text-center text-xs text-gray-400">ModifikasiOri Bengkel - {{ date('Y') }}</p>
    </div>
</div>

@push('scripts')
<script>
function estimasiService() {
    const jasaList = @json($jasaServis->map(fn($j) => ['id' => $j->id, 'nama' => $j->nama, 'biaya' => (int)$j->biaya]));
    const products = @json($products);
    const initialPelanggans = @json($initialPelanggans);

    return {
        pelangganId: '',
        kendaraanId: '',
        kendaraans: [],
        jasaSelect: null,
        formJasa: [],
        addSparepartQty: 1,
        formSparepart: [],
        sparepartSelect: null,
        showEstimasiResult: false,
        tanggalEstimasi: '{{ date('Y-m-d') }}',
        sumberKedatangan: '',
        kategoriService: '',
        keluhan: '',

        get totalJasa() { return this.formJasa.reduce((s, j) => s + (Number(j.biaya) || 0), 0); },
        get totalSparepart() { return this.formSparepart.reduce((s, p) => s + (p.qty * p.harga), 0); },
        get selectedPelanggan() { return this.pelanggansList.find(p => String(p.id) === this.pelangganId) || null; },
        get selectedKendaraan() { return this.kendaraans.find(k => String(k.id) === this.kendaraanId) || null; },

        formatRupiah(n) { return 'Rp ' + Number(n || 0).toLocaleString('id-ID'); },
        formatDate(d) {
            if (!d) return '-';
            const dt = new Date(d);
            return dt.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
        },

        showQuickAddModal: false,
        quickAddLoading: false,
        quickAdd: { nama: '', no_hp: '', alamat: '', nomor_polisi: '', merk: '', tipe: '', tahun: '' },
        pelanggansList: initialPelanggans,
        kendaraanByPelanggan: Object.fromEntries(initialPelanggans.map(p => [String(p.id), p.kendaraans || []])),

        loadKendaraan() {
            this.kendaraanId = '';
            this.kendaraans = this.pelangganId ? (this.kendaraanByPelanggan[this.pelangganId] || []) : [];
        },

        async submitQuickAdd() {
            if (!this.quickAdd.nama?.trim()) { Swal.fire('Perhatian', 'Nama pelanggan wajib diisi.', 'warning'); return; }
            if (!this.quickAdd.nomor_polisi?.trim()) { Swal.fire('Perhatian', 'No Polisi wajib diisi.', 'warning'); return; }
            if (!this.quickAdd.merk?.trim()) { Swal.fire('Perhatian', 'Merk kendaraan wajib diisi.', 'warning'); return; }
            this.quickAddLoading = true;
            try {
                const res = await fetch('{{ route('speedshop.pelanggans.quick-add') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                    body: JSON.stringify({
                        nama: this.quickAdd.nama, no_hp: this.quickAdd.no_hp || null, alamat: this.quickAdd.alamat || null,
                        nomor_polisi: this.quickAdd.nomor_polisi, merk: this.quickAdd.merk, tipe: this.quickAdd.tipe || null,
                        tahun: this.quickAdd.tahun ? parseInt(this.quickAdd.tahun) : null,
                    })
                });
                const data = await res.json();
                if (!data.success || !data.pelanggan) throw new Error('Gagal menyimpan');
                const p = data.pelanggan;
                const kendList = (p.kendaraans || []).map(k => ({ id: k.id, nomor_polisi: k.nomor_polisi, merk: k.merk, tipe: k.tipe || '', tahun: k.tahun }));
                this.kendaraanByPelanggan[String(p.id)] = kendList;
                this.pelanggansList.push({ id: p.id, nama: p.nama, no_hp: p.no_hp || '', alamat: p.alamat || '', kota: (p.kota && p.kota.nama) ? p.kota.nama : '', kendaraans: kendList });
                if (this.pelangganSelect) {
                    this.pelangganSelect.addOption({ id: String(p.id), label: p.nama + (p.no_hp ? ' (' + p.no_hp + ')' : '') });
                    this.pelangganSelect.setValue(String(p.id));
                }
                this.pelangganId = String(p.id);
                this.loadKendaraan();
                if (kendList.length) this.kendaraanId = String(kendList[0].id);
                this.showQuickAddModal = false;
                this.quickAdd = { nama: '', no_hp: '', alamat: '', nomor_polisi: '', merk: '', tipe: '', tahun: '' };
                Swal.fire('Berhasil', 'Pelanggan dan kendaraan berhasil ditambahkan.', 'success');
            } catch (e) { Swal.fire('Gagal', e.message || 'Gagal menyimpan data.', 'error'); }
            this.quickAddLoading = false;
        },

        addJasa() {
            const sel = this.jasaSelect;
            const id = sel ? sel.getValue() : '';
            if (!id) { Swal.fire('Perhatian', 'Pilih jasa terlebih dahulu.', 'warning'); return; }
            const j = jasaList.find(x => x.id == id);
            if (!j || this.formJasa.some(x => x.id == id)) {
                if (this.formJasa.some(x => x.id == id)) Swal.fire('Perhatian', 'Jasa sudah ditambahkan.', 'warning');
                return;
            }
            this.formJasa.push({ id: j.id, nama: j.nama, biaya: j.biaya });
            if (sel) sel.clear();
        },

        addSparepart() {
            const sel = this.sparepartSelect;
            if (!sel || !sel.value) { Swal.fire('Perhatian', 'Pilih produk terlebih dahulu.', 'warning'); return; }
            const product = products.find(p => p.id == sel.value);
            if (!product) return;
            const qty = Math.max(1, this.addSparepartQty || 1);
            const harga = Number(product.harga_jual_speedshop || product.harga_jual || product.hpp || 0);
            const exists = this.formSparepart.find(p => p.product_id == product.id);
            if (exists) exists.qty += qty;
            else this.formSparepart.push({ product_id: product.id, kode_produk: product.kode_produk, nama_produk: product.nama_produk, qty: qty, harga: harga, subtotal: qty * harga });
            this.addSparepartQty = 1;
            if (sel) sel.clear();
        },

        hitungEstimasi() {
            const pelVal = this.pelangganSelect ? this.pelangganSelect.getValue() : this.pelangganId;
            const kendVal = this.kendaraanId || '';
            if (!pelVal) { Swal.fire('Perhatian', 'Pilih pelanggan terlebih dahulu.', 'warning'); return; }
            if (!kendVal) { Swal.fire('Perhatian', 'Pilih kendaraan terlebih dahulu.', 'warning'); return; }
            if (this.formJasa.length === 0 && this.formSparepart.length === 0) {
                Swal.fire('Perhatian', 'Tambahkan minimal 1 jasa atau sparepart.', 'warning');
                return;
            }
            this.pelangganId = pelVal;
            this.showEstimasiResult = true;
            this.$nextTick(() => document.getElementById('estimasi-print-area')?.scrollIntoView({ behavior: 'smooth' }));
        },

        init() {
            this.$nextTick(() => {
                const self = this;
                if (typeof TomSelect !== 'undefined') {
                    const pelEl = document.getElementById('pelanggan-select-estimasi');
                    if (pelEl) {
                        this.pelangganSelect = new TomSelect(pelEl, {
                            placeholder: 'Pilih Pelanggan',
                            allowEmptyOption: true,
                            valueField: 'id', labelField: 'label',
                            options: [{ id: '', label: 'Pilih Pelanggan' }, ...self.pelanggansList.map(p => ({
                                id: String(p.id), label: p.nama + (p.no_hp ? ' (' + p.no_hp + ')' : ''),
                            }))],
                            onChange(val) { self.pelangganId = val || ''; self.loadKendaraan(); },
                        });
                    }
                    const jasaEl = document.getElementById('jasa-select-estimasi');
                    if (jasaEl) {
                        this.jasaSelect = new TomSelect(jasaEl, {
                            valueField: 'id',
                            labelField: 'label',
                            searchField: ['nama'],
                            placeholder: 'Pilih jasa servis...',
                            options: jasaList.map(j => ({
                                id: j.id,
                                nama: j.nama,
                                biaya: j.biaya,
                                label: `${j.nama} - Rp ${Number(j.biaya).toLocaleString('id-ID')}`,
                            })),
                            render: {
                                option: (data, escape) => `<div><span class="font-medium">${escape(data.nama)}</span> <span class="text-gray-400 text-xs">Rp ${Number(data.biaya).toLocaleString('id-ID')}</span></div>`,
                                item: (data, escape) => `<div>${escape(data.label)}</div>`,
                            },
                        });
                    }
                    const el = document.getElementById('product-select-estimasi');
                    if (el) {
                        this.sparepartSelect = new TomSelect(el, {
                            valueField: 'id', labelField: 'label', searchField: ['kode_produk', 'nama_produk'],
                            placeholder: 'Pilih produk...',
                            options: products.map(p => ({
                                id: p.id, kode_produk: p.kode_produk, nama_produk: p.nama_produk,
                                harga_jual_speedshop: p.harga_jual_speedshop, harga_jual: p.harga_jual, hpp: p.hpp, jumlah: p.jumlah,
                                label: `[${p.kode_produk}] ${p.nama_produk}`,
                            })),
                        });
                    }
                }
            });
        }
    };
}
</script>
@endpush
@endsection
