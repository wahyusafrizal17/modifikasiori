@extends('layouts.warehouse')

@section('title', 'Produk & Stok')

@section('content')
<div x-data="productsView()" class="space-y-6">
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('warehouse.dashboard') }}" class="transition hover:text-gray-700">Home</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="font-medium text-gray-900">Produk</span>
    </nav>

    @include('partials.flash')

    <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Produk & Stok</h2>
                <p class="mt-1 text-sm text-gray-500">Lihat produk dan stok warehouse. Warehouse dapat mengupdate HPP dan harga jual.</p>
            </div>
            <form method="GET" class="flex flex-wrap items-center gap-2">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari produk..." class="h-10 w-56 rounded-xl border border-gray-200 bg-gray-50 pl-10 pr-4 text-sm text-gray-700 placeholder-gray-400 transition focus:border-red-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-400/20">
                </div>
                <select name="category_id" onchange="this.form.submit()" class="h-10 rounded-xl border border-gray-200 bg-gray-50 px-3 text-sm text-gray-700 focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nama }}</option>
                    @endforeach
                </select>
                <select name="brand_id" onchange="this.form.submit()" class="h-10 rounded-xl border border-gray-200 bg-gray-50 px-3 text-sm text-gray-700 focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20">
                    <option value="">Semua Brand</option>
                    @foreach($brands as $brand)
                        <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->nama }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        <div class="mt-6 overflow-x-auto rounded-xl border border-gray-100">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">No.</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Kode Produk</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Nama Produk</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Kategori</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Brand</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Stok</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 text-right">HPP (Rp)</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 text-right">Harga Jual Speedshop (Rp)</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 text-right">Harga Jual Reseler (Rp)</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 text-right">Harga Eceran Terendah (Rp)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($products as $i => $product)
                    <tr class="transition hover:bg-gray-50">
                        <td class="px-5 py-4 text-gray-500">{{ $products->firstItem() + $i }}</td>
                        <td class="px-5 py-4 font-medium text-gray-900">{{ $product->kode_produk }}</td>
                        <td class="px-5 py-4 text-gray-700">{{ $product->nama_produk }}</td>
                        <td class="px-5 py-4 text-gray-600">{{ $product->category->nama ?? '-' }}</td>
                        <td class="px-5 py-4 text-gray-600">{{ $product->brand->nama ?? '-' }}</td>
                        <td class="px-5 py-4 text-center">
                            <button type="button" @click="openHistory({{ $product->id }})" class="inline-flex cursor-pointer items-center rounded-lg px-2.5 py-1 text-xs font-semibold transition hover:ring-2 hover:ring-gray-300 {{ $product->jumlah > 0 ? 'bg-green-100 text-green-700 hover:ring-green-400' : 'bg-red-100 text-red-700 hover:ring-red-400' }}" title="Klik untuk lihat history stok">{{ number_format($product->jumlah) }}</button>
                        </td>
                        <td class="min-w-[100px] px-5 py-2 text-right">
                            <div class="cursor-pointer rounded px-2 py-1.5 transition hover:bg-red-50" @click="startEdit({{ $product->id }}, 'hpp')">
                                <span x-show="!(editingCell.id === {{ $product->id }} && editingCell.field === 'hpp')" x-text="formatRupiah(rowsData[{{ $product->id }}]?.hpp ?? 0)" class="text-gray-700"></span>
                                <template x-if="editingCell.id === {{ $product->id }} && editingCell.field === 'hpp'">
                                    <input type="number" min="0" step="1" id="edit-{{ $product->id }}-hpp"
                                        :value="rowsData[{{ $product->id }}]?.hpp ?? 0"
                                        @input="rowsData[{{ $product->id }}].hpp = $event.target.value ? Number($event.target.value) : 0"
                                        @blur="saveInline({{ $product->id }})"
                                        @keydown.enter="$event.target.blur()"
                                        @click.stop
                                        class="w-full min-w-[80px] rounded border border-red-300 py-1 px-2 text-right text-sm focus:border-red-500 focus:outline-none focus:ring-1 focus:ring-red-500">
                                </template>
                            </div>
                        </td>
                        <td class="min-w-[100px] px-5 py-2 text-right">
                            <div class="cursor-pointer rounded px-2 py-1.5 transition hover:bg-red-50" @click="startEdit({{ $product->id }}, 'harga_jual_speedshop')">
                                <span x-show="!(editingCell.id === {{ $product->id }} && editingCell.field === 'harga_jual_speedshop')" x-text="formatRupiah(rowsData[{{ $product->id }}]?.harga_jual_speedshop ?? 0)" class="text-gray-700"></span>
                                <template x-if="editingCell.id === {{ $product->id }} && editingCell.field === 'harga_jual_speedshop'">
                                    <input type="number" min="0" step="1" id="edit-{{ $product->id }}-harga_jual_speedshop"
                                        :value="rowsData[{{ $product->id }}]?.harga_jual_speedshop ?? 0"
                                        @input="rowsData[{{ $product->id }}].harga_jual_speedshop = $event.target.value ? Number($event.target.value) : 0"
                                        @blur="saveInline({{ $product->id }})"
                                        @keydown.enter="$event.target.blur()"
                                        @click.stop
                                        class="w-full min-w-[80px] rounded border border-red-300 py-1 px-2 text-right text-sm focus:border-red-500 focus:outline-none focus:ring-1 focus:ring-red-500">
                                </template>
                            </div>
                        </td>
                        <td class="min-w-[100px] px-5 py-2 text-right">
                            <div class="cursor-pointer rounded px-2 py-1.5 transition hover:bg-red-50" @click="startEdit({{ $product->id }}, 'harga_jual_reseler')">
                                <span x-show="!(editingCell.id === {{ $product->id }} && editingCell.field === 'harga_jual_reseler')" x-text="formatRupiah(rowsData[{{ $product->id }}]?.harga_jual_reseler ?? 0)" class="text-gray-700"></span>
                                <template x-if="editingCell.id === {{ $product->id }} && editingCell.field === 'harga_jual_reseler'">
                                    <input type="number" min="0" step="1" id="edit-{{ $product->id }}-harga_jual_reseler"
                                        :value="rowsData[{{ $product->id }}]?.harga_jual_reseler ?? 0"
                                        @input="rowsData[{{ $product->id }}].harga_jual_reseler = $event.target.value ? Number($event.target.value) : 0"
                                        @blur="saveInline({{ $product->id }})"
                                        @keydown.enter="$event.target.blur()"
                                        @click.stop
                                        class="w-full min-w-[80px] rounded border border-red-300 py-1 px-2 text-right text-sm focus:border-red-500 focus:outline-none focus:ring-1 focus:ring-red-500">
                                </template>
                            </div>
                        </td>
                        <td class="min-w-[100px] px-5 py-2 text-right">
                            <div class="cursor-pointer rounded px-2 py-1.5 transition hover:bg-red-50" @click="startEdit({{ $product->id }}, 'harga_eceran_terendah')">
                                <span x-show="!(editingCell.id === {{ $product->id }} && editingCell.field === 'harga_eceran_terendah')" x-text="formatRupiah(rowsData[{{ $product->id }}]?.harga_eceran_terendah ?? 0)" class="text-gray-700"></span>
                                <template x-if="editingCell.id === {{ $product->id }} && editingCell.field === 'harga_eceran_terendah'">
                                    <input type="number" min="0" step="1" id="edit-{{ $product->id }}-harga_eceran_terendah"
                                        :value="rowsData[{{ $product->id }}]?.harga_eceran_terendah ?? 0"
                                        @input="rowsData[{{ $product->id }}].harga_eceran_terendah = $event.target.value ? Number($event.target.value) : 0"
                                        @blur="saveInline({{ $product->id }})"
                                        @keydown.enter="$event.target.blur()"
                                        @click.stop
                                        class="w-full min-w-[80px] rounded border border-red-300 py-1 px-2 text-right text-sm focus:border-red-500 focus:outline-none focus:ring-1 focus:ring-red-500">
                                </template>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="10" class="px-5 py-12 text-center text-gray-400">Belum ada data produk.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
        <div class="mt-5">{{ $products->links() }}</div>
        @endif
    </div>

    {{-- Stock History Modal --}}
    <div x-show="showHistory" x-cloak class="fixed inset-0 z-50 overflow-y-auto" @keydown.escape.window="showHistory = false">
        <div x-show="showHistory" x-transition.opacity class="fixed inset-0 bg-black/50" @click="showHistory = false"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div x-show="showHistory" x-transition class="relative w-full max-w-2xl rounded-2xl bg-white p-8 shadow-xl" @click.stop>
                <h3 class="text-lg font-bold text-gray-900">History Stok</h3>
                <p class="mt-1 text-sm text-gray-500">
                    <span x-text="historyProduct.kode_produk"></span> — <span x-text="historyProduct.nama_produk"></span>
                    <span class="ml-2 inline-flex items-center rounded-lg bg-blue-100 px-2 py-0.5 text-xs font-semibold text-blue-700">Stok: <span x-text="historyProduct.jumlah" class="ml-1"></span></span>
                </p>
                <div class="mt-5 max-h-96 overflow-y-auto rounded-xl border border-gray-100">
                    <table class="w-full text-left text-sm">
                        <thead class="sticky top-0 bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Tanggal</th>
                                <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Tipe</th>
                                <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 text-center">Qty</th>
                                <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Keterangan</th>
                                <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Referensi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <template x-for="m in historyMovements" :key="m.id">
                                <tr class="transition hover:bg-gray-50">
                                    <td class="px-4 py-3 text-gray-600 whitespace-nowrap" x-text="new Date(m.created_at).toLocaleDateString('id-ID', {day:'2-digit',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'})"></td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center rounded-lg px-2 py-0.5 text-xs font-semibold" :class="m.type === 'masuk' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'" x-text="m.type === 'masuk' ? '▲ Masuk' : '▼ Keluar'"></span>
                                    </td>
                                    <td class="px-4 py-3 text-center font-semibold" :class="m.type === 'masuk' ? 'text-green-600' : 'text-red-600'" x-text="(m.type === 'masuk' ? '+' : '-') + m.qty"></td>
                                    <td class="px-4 py-3 text-gray-600" x-text="m.keterangan || '-'"></td>
                                    <td class="px-4 py-3 text-gray-500 text-xs" x-text="m.reference || '-'"></td>
                                </tr>
                            </template>
                            <template x-if="historyMovements.length === 0">
                                <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">Belum ada history stok.</td></tr>
                            </template>
                        </tbody>
                    </table>
                </div>
                <div class="mt-6">
                    <button @click="showHistory = false" class="rounded-xl border border-gray-200 px-6 py-2.5 text-sm font-medium text-gray-600 transition hover:bg-gray-50">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@php
    $initialRows = $products->getCollection()->mapWithKeys(fn ($p) => [
        $p->id => [
            'hpp' => (float) ($p->hpp ?? 0),
            'harga_jual_speedshop' => (float) ($p->harga_jual_speedshop ?? 0),
            'harga_jual_reseler' => (float) ($p->harga_jual_reseler ?? 0),
            'harga_eceran_terendah' => (float) ($p->harga_eceran_terendah ?? 0),
        ],
    ]);
@endphp
@push('scripts')
<script>
function productsView() {
    const baseUrl = '{{ url("warehouse/products") }}';
    const csrf = '{{ csrf_token() }}';
    const initialRows = @json($initialRows);
    return {
        showHistory: false, historyProduct: {}, historyMovements: [],
        rowsData: initialRows,
        editingCell: { id: null, field: null },
        savingCellId: null,

        formatRupiah(n) {
            return Number(n || 0).toLocaleString('id-ID');
        },

        startEdit(id, field) {
            this.editingCell = { id, field };
            this.$nextTick(() => {
                const el = document.getElementById(`edit-${id}-${field}`);
                if (el) { el.focus(); el.select(); }
            });
        },

        async saveInline(id) {
            if (!this.rowsData[id]) return;
            this.editingCell = { id: null, field: null };
            this.savingCellId = id;
            try {
                const d = this.rowsData[id];
                const res = await fetch(`${baseUrl}/${id}/update-prices`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                    body: JSON.stringify({
                        hpp: Number(d.hpp) || 0,
                        harga_jual_speedshop: Number(d.harga_jual_speedshop) || 0,
                        harga_jual_reseler: Number(d.harga_jual_reseler) || 0,
                        harga_eceran_terendah: Number(d.harga_eceran_terendah) || 0,
                    })
                });
                const data = await res.json();
                if (res.ok && data.success) {
                    // Nilai sudah ter-update di rowsData
                } else if (data.errors) {
                    alert(Object.values(data.errors).flat().join('\n'));
                }
            } catch (e) { alert('Gagal menyimpan: ' + (e.message || 'Unknown error')); }
            this.savingCellId = null;
        },

        async openHistory(id) {
            this.historyProduct = {}; this.historyMovements = []; this.showHistory = true;
            const res = await fetch(`${baseUrl}/${id}/stock-history`, { headers: { 'Accept': 'application/json' } });
            const data = await res.json();
            this.historyProduct = data.product;
            this.historyMovements = data.movements;
        }
    }
}
</script>
@endpush
