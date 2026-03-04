@extends('layouts.produksi')

@section('title', 'Detail Stock IN - ' . $stockIn->kode)

@section('content')
<div x-data="stockInDetail()" class="space-y-6">
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('produksi.dashboard') }}" class="transition hover:text-gray-700">Home</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('produksi.stock-in.index') }}" class="transition hover:text-gray-700">Stock IN</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="font-medium text-gray-900">{{ $stockIn->kode }}</span>
    </nav>

    @include('partials.flash')

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Left --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Info Card --}}
            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900">Informasi Stock IN</h2>
                <p class="mt-1 text-sm text-gray-500">Detail pengajuan stock masuk bahan baku dan kemasan</p>

                <div class="mt-5 flex flex-wrap items-end gap-3">
                    <div class="w-40">
                        <label class="mb-1.5 block text-xs font-semibold text-gray-600">Kode</label>
                        <div class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm font-medium text-gray-900">{{ $stockIn->kode }}</div>
                    </div>
                    <div class="flex-1 min-w-[220px]">
                        <label class="mb-1.5 block text-xs font-semibold text-gray-600">Diajukan oleh</label>
                        <div class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-700">{{ $stockIn->user->name }}</div>
                    </div>
                    <div class="w-44">
                        <label class="mb-1.5 block text-xs font-semibold text-gray-600">Tanggal</label>
                        <div class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-700">{{ $stockIn->created_at->format('d M Y H:i') }}</div>
                    </div>
                </div>

                @if($stockIn->catatan)
                <div class="mt-4">
                    <label class="mb-1.5 block text-xs font-semibold text-gray-600">Catatan</label>
                    <div class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-700">{{ $stockIn->catatan }}</div>
                </div>
                @endif

            </div>

            {{-- Items Table --}}
            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-900">Daftar Item</h2>
                    <span class="inline-flex items-center rounded-lg bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600">{{ $stockIn->items->count() }} item</span>
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
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($stockIn->items as $i => $item)
                            <tr class="transition hover:bg-gray-50">
                                <td class="px-5 py-4 text-gray-500">{{ $i + 1 }}</td>
                                <td class="px-4 py-4">
                                    @if(str_contains($item->itemable_type, 'BahanBaku'))
                                        <span class="inline-flex items-center rounded-lg bg-blue-100 px-2.5 py-1 text-xs font-semibold text-blue-700">Bahan Baku</span>
                                    @else
                                        <span class="inline-flex items-center rounded-lg bg-purple-100 px-2.5 py-1 text-xs font-semibold text-purple-700">Kemasan</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 font-medium text-gray-900">{{ $item->itemable->kode ?? '-' }}</td>
                                <td class="px-5 py-4 text-gray-700">{{ $item->itemable->nama ?? '-' }}</td>
                                <td class="px-5 py-4 text-right text-gray-700">Rp {{ number_format($item->itemable->harga ?? 0, 0, ',', '.') }}</td>
                                <td class="px-5 py-4 text-center">
                                    <span class="inline-flex items-center rounded-lg bg-green-100 px-3 py-1 text-sm font-bold text-green-700">+{{ $item->jumlah }}</span>
                                </td>
                                <td class="px-5 py-4 text-right font-medium text-gray-900">Rp {{ number_format(($item->itemable->harga ?? 0) * $item->jumlah, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Right --}}
        <div class="space-y-6">
            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900">Ringkasan</h2>

                <div class="mt-4 space-y-3">
                    <div class="flex items-center justify-between rounded-lg bg-blue-50 px-4 py-3">
                        <span class="text-sm font-medium text-blue-700">Bahan Baku</span>
                        <span class="text-sm font-bold text-blue-800">{{ $stockIn->items->filter(fn($i) => str_contains($i->itemable_type, 'BahanBaku'))->count() }} item</span>
                    </div>
                    <div class="flex items-center justify-between rounded-lg bg-purple-50 px-4 py-3">
                        <span class="text-sm font-medium text-purple-700">Kemasan</span>
                        <span class="text-sm font-bold text-purple-800">{{ $stockIn->items->filter(fn($i) => str_contains($i->itemable_type, 'Kemasan'))->count() }} item</span>
                    </div>
                    <div class="flex items-center justify-between rounded-lg bg-gray-50 px-4 py-3">
                        <span class="text-sm font-medium text-gray-700">Total Qty</span>
                        <span class="text-sm font-bold text-gray-900">{{ $stockIn->items->sum('jumlah') }}</span>
                    </div>
                    <div class="flex items-center justify-between rounded-lg bg-green-50 px-4 py-3">
                        <span class="text-sm font-medium text-green-700">Total Harga</span>
                        <span class="text-sm font-bold text-green-800">Rp {{ number_format($stockIn->items->sum(fn($i) => ($i->itemable->harga ?? 0) * $i->jumlah), 0, ',', '.') }}</span>
                    </div>
                </div>

                {{-- Step Progress --}}
                <div class="mt-5">
                    <label class="mb-3 block text-sm font-semibold text-gray-700">Status</label>
                    @php
                        $steps = [
                            ['label' => 'Diajukan', 'done' => true],
                            ['label' => 'Menunggu Verifikasi', 'done' => in_array($stockIn->status, ['approved', 'rejected'])],
                            ['label' => $stockIn->status === 'rejected' ? 'Ditolak' : 'Disetujui', 'done' => in_array($stockIn->status, ['approved', 'rejected'])],
                        ];
                        $isRejected = $stockIn->status === 'rejected';
                        $currentStep = match($stockIn->status) {
                            'pending' => 1,
                            'approved', 'rejected' => 2,
                        };
                    @endphp
                    <div class="flex items-start justify-between">
                        @foreach($steps as $idx => $step)
                        <div class="flex flex-col items-center" style="width: {{ 100 / count($steps) }}%">
                            <div class="relative flex w-full items-center justify-center">
                                {{-- Line before --}}
                                @if($idx > 0)
                                <div class="absolute right-1/2 h-0.5 w-full {{ $step['done'] ? ($isRejected && $idx === count($steps) - 1 ? 'bg-red-400' : 'bg-green-400') : 'bg-gray-200' }}"></div>
                                @endif
                                {{-- Line after --}}
                                @if($idx < count($steps) - 1)
                                <div class="absolute left-1/2 h-0.5 w-full {{ $steps[$idx + 1]['done'] ? ($isRejected ? 'bg-red-400' : 'bg-green-400') : 'bg-gray-200' }}"></div>
                                @endif
                                {{-- Circle --}}
                                @if($step['done'])
                                    @if($isRejected && $idx === count($steps) - 1)
                                    <div class="relative z-10 flex h-8 w-8 items-center justify-center rounded-full bg-red-500 shadow-sm shadow-red-200">
                                        <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </div>
                                    @else
                                    <div class="relative z-10 flex h-8 w-8 items-center justify-center rounded-full bg-green-500 shadow-sm shadow-green-200">
                                        <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                    @endif
                                @elseif($idx === $currentStep)
                                    <div class="relative z-10 flex h-8 w-8 items-center justify-center rounded-full border-2 border-yellow-400 bg-yellow-50">
                                        <div class="h-2.5 w-2.5 rounded-full bg-yellow-400 animate-pulse"></div>
                                    </div>
                                @else
                                    <div class="relative z-10 flex h-8 w-8 items-center justify-center rounded-full bg-gray-200">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                @endif
                            </div>
                            <p class="mt-2 text-center text-xs font-medium {{ $step['done'] ? ($isRejected && $idx === count($steps) - 1 ? 'text-red-600' : 'text-green-600') : ($idx === $currentStep ? 'text-yellow-600' : 'text-gray-400') }}">{{ $step['label'] }}</p>
                        </div>
                        @endforeach
                    </div>
                    @if($stockIn->approver)
                    <p class="mt-3 text-center text-xs text-gray-500">
                        oleh {{ $stockIn->approver->name }} &middot; {{ $stockIn->approved_at?->format('d M Y H:i') }}
                    </p>
                    @endif

                    @if($stockIn->rejected_reason)
                    @php
                        $parts = explode(': ', $stockIn->rejected_reason, 2);
                        $rejectCategory = $parts[0];
                        $rejectNote = $parts[1] ?? '-';
                    @endphp
                    <div class="mt-4 rounded-lg border border-red-200 bg-red-50 p-3 space-y-2">
                        <div>
                            <p class="text-xs font-semibold text-red-600">Alasan Penolakan</p>
                            <p class="mt-0.5 text-sm font-medium text-red-700">{{ $rejectCategory }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-red-600">Keterangan</p>
                            <p class="mt-0.5 text-sm text-red-700">{{ $rejectNote }}</p>
                        </div>
                    </div>
                    @endif
                </div>

                @if($stockIn->isPending() && (auth()->user()->isManager() || auth()->user()->isAdmin()))
                <div class="mt-5 space-y-3">
                    <button @click="approveStockIn()" :disabled="loading"
                            class="flex w-full items-center justify-center gap-2 rounded-xl bg-green-500 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-green-600 disabled:cursor-not-allowed disabled:opacity-50">
                        <svg x-show="!loading" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <svg x-show="loading" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        Approve - Setujui
                    </button>

                    <div x-show="showRejectForm" x-cloak class="space-y-3">
                        <label class="block text-xs font-semibold text-gray-600">Alasan Penolakan</label>
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2.5 cursor-pointer transition hover:bg-gray-50" :class="rejectCategory === 'Kesalahan Bahan Baku' && 'border-red-400 bg-red-50'">
                                <input type="radio" x-model="rejectCategory" value="Kesalahan Bahan Baku" class="h-4 w-4 text-red-500 focus:ring-red-400">
                                <span class="text-sm text-gray-700">Kesalahan Bahan Baku</span>
                            </label>
                            <label class="flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2.5 cursor-pointer transition hover:bg-gray-50" :class="rejectCategory === 'Kesalahan Operator' && 'border-red-400 bg-red-50'">
                                <input type="radio" x-model="rejectCategory" value="Kesalahan Operator" class="h-4 w-4 text-red-500 focus:ring-red-400">
                                <span class="text-sm text-gray-700">Kesalahan Operator</span>
                            </label>
                        </div>
                        <textarea x-model="rejectNote" rows="2" class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm transition focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20" placeholder="Keterangan tambahan (opsional)..."></textarea>
                        <p x-show="rejectError" class="text-xs text-red-500" x-text="rejectError"></p>
                        <div class="flex gap-2">
                            <button @click="confirmReject()" :disabled="loading" class="flex-1 rounded-xl bg-red-500 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-red-600 disabled:opacity-50">Konfirmasi Tolak</button>
                            <button @click="showRejectForm = false; rejectCategory = ''; rejectNote = ''; rejectError = ''" class="rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-600 transition hover:bg-gray-50">Batal</button>
                        </div>
                    </div>

                    <button x-show="!showRejectForm" @click="showRejectForm = true"
                            class="flex w-full items-center justify-center gap-2 rounded-xl border border-gray-200 px-5 py-3 text-sm font-medium text-gray-600 transition hover:bg-gray-50">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        Reject - Tolak
                    </button>
                </div>
                @else
                <div class="mt-5">
                    <a href="{{ route('produksi.stock-in.index') }}" class="flex w-full items-center justify-center gap-2 rounded-xl border border-gray-200 px-5 py-3 text-sm font-medium text-gray-600 transition hover:bg-gray-50">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Kembali ke Daftar
                    </a>
                </div>
                @endif
            </div>

            <a href="{{ route('produksi.stock-in.pdf', $stockIn) }}" target="_blank" class="flex w-full items-center justify-center gap-2 rounded-xl bg-red-500 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-red-600">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak PDF
            </a>

            @if($stockIn->isPending() && (auth()->user()->isManager() || auth()->user()->isAdmin()))
            <div class="rounded-xl border border-yellow-200 bg-yellow-50 p-4">
                <div class="flex gap-3">
                    <svg class="h-5 w-5 flex-shrink-0 text-yellow-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div>
                        <p class="text-sm font-semibold text-yellow-800">Menunggu Verifikasi</p>
                        <p class="mt-1 text-xs text-yellow-700">Periksa item yang diajukan, lalu approve atau reject pengajuan ini.</p>
                    </div>
                </div>
            </div>

            <a href="{{ route('produksi.stock-in.index') }}" class="flex w-full items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white px-5 py-3 text-sm font-medium text-gray-600 shadow-sm transition hover:bg-gray-50">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Daftar
            </a>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function stockInDetail() {
    return {
        loading: false,
        showRejectForm: false,
        rejectCategory: '',
        rejectNote: '',
        rejectError: '',

        async approveStockIn() {
            const result = await Swal.fire({
                title: 'Approve Stock IN?',
                text: 'Stok bahan baku/kemasan akan bertambah sesuai jumlah yang diajukan.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#22c55e',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Approve!',
                cancelButtonText: 'Batal'
            });
            if (!result.isConfirmed) return;

            this.loading = true;
            const res = await fetch('{{ route("produksi.stock-in.approve", $stockIn) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });

            if (!res.ok) {
                const d = await res.json();
                this.loading = false;
                Swal.fire('Error', d.message || 'Gagal approve.', 'error');
                return;
            }

            window.location.reload();
        },

        async confirmReject() {
            if (!this.rejectCategory) {
                this.rejectError = 'Pilih alasan penolakan terlebih dahulu.';
                return;
            }

            const reason = this.rejectNote.trim()
                ? `${this.rejectCategory}: ${this.rejectNote.trim()}`
                : this.rejectCategory;

            const result = await Swal.fire({
                title: 'Reject Stock IN?',
                text: 'Stock IN akan ditolak dan stok tidak akan berubah.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Tolak!',
                cancelButtonText: 'Batal'
            });
            if (!result.isConfirmed) return;

            this.loading = true;

            const res = await fetch('{{ route("produksi.stock-in.reject", $stockIn) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ rejected_reason: reason })
            });

            if (!res.ok) {
                const d = await res.json();
                this.loading = false;
                Swal.fire('Error', d.message || 'Gagal menolak.', 'error');
                return;
            }

            window.location.reload();
        }
    }
}
</script>
@endpush
