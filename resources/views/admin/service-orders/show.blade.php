@extends('layouts.admin')

@section('title', 'Detail Work Order')

@section('content')
<div x-data="{ statusLoading: false }" class="space-y-6">
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.dashboard') }}" class="transition hover:text-gray-700">Home</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('admin.service-orders.index') }}" class="transition hover:text-gray-700">Work Order</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="font-medium text-gray-900">{{ $serviceOrder->kode_servis }}</span>
    </nav>

    @include('partials.flash')

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-900">{{ $serviceOrder->kode_servis }}</h2>
            <span class="mt-1 inline-flex rounded-lg px-2.5 py-1 text-xs font-semibold {{ $serviceOrder->status_badge }}">{{ ucfirst($serviceOrder->status) }}</span>
        </div>
        <div class="flex items-center gap-3">
            @if(!in_array($serviceOrder->status, ['selesai','dibatalkan']))
            <a href="{{ route('admin.service-orders.edit', $serviceOrder) }}" class="inline-flex h-10 items-center gap-2 rounded-xl bg-green-500 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-green-600">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit
            </a>
            @endif

            @if($serviceOrder->status === 'antri')
            <button @click="updateStatus('proses')" :disabled="statusLoading" class="inline-flex h-10 items-center gap-2 rounded-xl bg-blue-500 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-600 disabled:opacity-50">Mulai Proses</button>
            @endif

            @if($serviceOrder->status === 'proses')
            <button @click="updateStatus('selesai')" :disabled="statusLoading" class="inline-flex h-10 items-center gap-2 rounded-xl bg-green-500 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-green-600 disabled:opacity-50">Selesai</button>
            @endif

            @if(in_array($serviceOrder->status, ['antri','proses']))
            <button @click="updateStatus('dibatalkan')" :disabled="statusLoading" class="inline-flex h-10 items-center gap-2 rounded-xl bg-red-500 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-600 disabled:opacity-50">Batalkan</button>
            @endif

            @if($serviceOrder->status === 'selesai' && !$serviceOrder->invoice)
            <button onclick="document.getElementById('invoiceModal').classList.remove('hidden')" class="inline-flex h-10 items-center gap-2 rounded-xl bg-amber-500 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-amber-600">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Buat Invoice
            </button>
            @endif

            @if($serviceOrder->invoice)
            <a href="{{ route('admin.invoices.show', $serviceOrder->invoice) }}" class="inline-flex h-10 items-center gap-2 rounded-xl bg-amber-500 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-amber-600">Lihat Invoice</a>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Info --}}
        <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
            <h3 class="text-base font-bold text-gray-900">Informasi</h3>
            <dl class="mt-4 space-y-3 text-sm">
                <div><dt class="text-gray-400">Pelanggan</dt><dd class="mt-0.5 font-medium text-gray-900">{{ $serviceOrder->pelanggan->nama }}</dd></div>
                <div><dt class="text-gray-400">No. HP</dt><dd class="mt-0.5 text-gray-700">{{ $serviceOrder->pelanggan->no_hp ?? '-' }}</dd></div>
                <div><dt class="text-gray-400">Kendaraan</dt><dd class="mt-0.5 text-gray-700">{{ $serviceOrder->kendaraan->nomor_polisi }} — {{ $serviceOrder->kendaraan->merk }} {{ $serviceOrder->kendaraan->tipe }}</dd></div>
                <div><dt class="text-gray-400">Mekanik</dt><dd class="mt-0.5 text-gray-700">{{ $serviceOrder->mekanik->nama ?? '-' }}</dd></div>
                <div><dt class="text-gray-400">Tanggal Masuk</dt><dd class="mt-0.5 text-gray-700">{{ $serviceOrder->tanggal_masuk->format('d M Y') }}</dd></div>
                <div><dt class="text-gray-400">Tanggal Selesai</dt><dd class="mt-0.5 text-gray-700">{{ $serviceOrder->tanggal_selesai?->format('d M Y') ?? '-' }}</dd></div>
                @if($serviceOrder->next_service_date)
                <div><dt class="text-gray-400">Next Service</dt><dd class="mt-0.5 text-gray-700">{{ $serviceOrder->next_service_date->format('d M Y') }}</dd></div>
                @endif
            </dl>
        </div>

        {{-- Keluhan + Items --}}
        <div class="space-y-6 lg:col-span-2">
            @if($serviceOrder->keluhan)
            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                <h3 class="text-base font-bold text-gray-900">Keluhan</h3>
                <p class="mt-2 text-sm text-gray-700 leading-relaxed">{{ $serviceOrder->keluhan }}</p>
            </div>
            @endif

            {{-- Jasa --}}
            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                <h3 class="text-base font-bold text-gray-900">Jasa Servis</h3>
                <div class="mt-4 overflow-x-auto rounded-xl border border-gray-100">
                    <table class="w-full text-left text-sm">
                        <thead><tr class="bg-gray-50">
                            <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Jasa</th>
                            <th class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wider text-gray-500">Biaya</th>
                        </tr></thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($serviceOrder->jasaServis as $jasa)
                            <tr><td class="px-4 py-3 text-gray-700">{{ $jasa->nama }}</td><td class="px-4 py-3 text-right font-medium text-gray-900">Rp {{ number_format($jasa->pivot->biaya, 0, ',', '.') }}</td></tr>
                            @empty
                            <tr><td colspan="2" class="px-4 py-6 text-center text-gray-400">Tidak ada jasa.</td></tr>
                            @endforelse
                        </tbody>
                        <tfoot><tr class="bg-gray-50"><td class="px-4 py-3 font-bold text-gray-900">Total Jasa</td><td class="px-4 py-3 text-right font-bold text-gray-900">Rp {{ number_format($serviceOrder->total_jasa, 0, ',', '.') }}</td></tr></tfoot>
                    </table>
                </div>
            </div>

            {{-- Sparepart --}}
            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                <h3 class="text-base font-bold text-gray-900">Sparepart</h3>
                <div class="mt-4 overflow-x-auto rounded-xl border border-gray-100">
                    <table class="w-full text-left text-sm">
                        <thead><tr class="bg-gray-50">
                            <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Produk</th>
                            <th class="px-4 py-3 text-center text-xs font-bold uppercase tracking-wider text-gray-500">Qty</th>
                            <th class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wider text-gray-500">Harga</th>
                            <th class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wider text-gray-500">Subtotal</th>
                        </tr></thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($serviceOrder->products as $prod)
                            <tr>
                                <td class="px-4 py-3 text-gray-700">{{ $prod->nama_produk }}</td>
                                <td class="px-4 py-3 text-center text-gray-600">{{ $prod->pivot->qty }}</td>
                                <td class="px-4 py-3 text-right text-gray-600">Rp {{ number_format($prod->pivot->harga, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right font-medium text-gray-900">Rp {{ number_format($prod->pivot->qty * $prod->pivot->harga, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400">Tidak ada sparepart.</td></tr>
                            @endforelse
                        </tbody>
                        <tfoot><tr class="bg-gray-50"><td colspan="3" class="px-4 py-3 font-bold text-gray-900">Total Sparepart</td><td class="px-4 py-3 text-right font-bold text-gray-900">Rp {{ number_format($serviceOrder->total_sparepart, 0, ',', '.') }}</td></tr></tfoot>
                    </table>
                </div>
            </div>

            {{-- Grand Total --}}
            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-lg font-bold text-gray-900">Grand Total</span>
                    <span class="text-2xl font-bold text-red-600">Rp {{ number_format($serviceOrder->grand_total, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Invoice Modal --}}
    @if($serviceOrder->status === 'selesai' && !$serviceOrder->invoice)
    <div id="invoiceModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="fixed inset-0 bg-black/50" onclick="document.getElementById('invoiceModal').classList.add('hidden')"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-md rounded-2xl bg-white p-8 shadow-xl" onclick="event.stopPropagation()">
                <h3 class="text-lg font-bold text-gray-900">Buat Invoice</h3>
                <form action="{{ route('admin.invoices.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="service_order_id" value="{{ $serviceOrder->id }}">
                    <div class="mt-5 space-y-4 text-sm">
                        <div class="flex justify-between"><span class="text-gray-500">Total Jasa</span><span class="font-medium">Rp {{ number_format($serviceOrder->total_jasa, 0, ',', '.') }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Total Sparepart</span><span class="font-medium">Rp {{ number_format($serviceOrder->total_sparepart, 0, ',', '.') }}</span></div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">Diskon (Rp)</label>
                            <input type="number" name="diskon" value="0" min="0" class="h-10 w-full rounded-xl border border-gray-200 bg-gray-50 px-4 text-sm outline-none transition focus:border-red-300 focus:ring-2 focus:ring-red-100">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">Metode Pembayaran <span class="text-red-500">*</span></label>
                            <select name="metode_pembayaran" class="h-10 w-full rounded-xl border border-gray-200 bg-gray-50 px-4 text-sm outline-none transition focus:border-red-300 focus:ring-2 focus:ring-red-100">
                                <option value="cash">Cash</option>
                                <option value="transfer">Transfer</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">Catatan</label>
                            <textarea name="catatan" rows="2" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm outline-none transition focus:border-red-300 focus:ring-2 focus:ring-red-100"></textarea>
                        </div>
                    </div>
                    <div class="mt-6 flex items-center gap-3">
                        <button type="submit" class="rounded-xl bg-amber-500 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-amber-600">Buat Invoice</button>
                        <button type="button" onclick="document.getElementById('invoiceModal').classList.add('hidden')" class="rounded-xl border border-gray-200 px-6 py-2.5 text-sm font-medium text-gray-600 transition hover:bg-gray-50">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
async function updateStatus(status) {
    const result = await Swal.fire({ title: 'Ubah Status?', text: `Status akan diubah menjadi "${status}".`, icon: 'question', showCancelButton: true, confirmButtonColor: '#3b82f6', cancelButtonColor: '#6b7280', confirmButtonText: 'Ya, ubah!', cancelButtonText: 'Batal' });
    if (!result.isConfirmed) return;
    const res = await fetch('{{ route("admin.service-orders.update-status", $serviceOrder) }}', {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
        body: JSON.stringify({ status })
    });
    if (res.ok) window.location.reload();
}
</script>
@endpush
