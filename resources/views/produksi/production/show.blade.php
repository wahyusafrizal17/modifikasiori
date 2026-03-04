@extends('layouts.produksi')

@section('title', 'Detail Produksi - ' . $production->kode)

@section('content')
<div x-data="productionDetail()" class="space-y-6">
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('produksi.dashboard') }}" class="transition hover:text-gray-700">Home</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('produksi.production.index') }}" class="transition hover:text-gray-700">Produksi</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="font-medium text-gray-900">{{ $production->kode }}</span>
    </nav>

    @include('partials.flash')

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Left --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Info --}}
            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900">Informasi Produksi</h2>
                <p class="mt-1 text-sm text-gray-500">Detail proses produksi bahan baku</p>

                <div class="mt-5 flex flex-wrap items-end gap-3">
                    <div class="w-44">
                        <label class="mb-1.5 block text-xs font-semibold text-gray-600">Kode</label>
                        <div class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm font-medium text-gray-900">{{ $production->kode }}</div>
                    </div>
                    <div class="flex-1 min-w-[180px]">
                        <label class="mb-1.5 block text-xs font-semibold text-gray-600">Team Produksi</label>
                        <div class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-700">{{ $production->teamProduksi->nama }}</div>
                    </div>
                    <div class="w-44">
                        <label class="mb-1.5 block text-xs font-semibold text-gray-600">Tanggal</label>
                        <div class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-700">{{ $production->tanggal->format('d M Y') }}</div>
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap items-end gap-3">
                    <div class="flex-1 min-w-[180px]">
                        <label class="mb-1.5 block text-xs font-semibold text-gray-600">Dibuat oleh</label>
                        <div class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-700">{{ $production->user->name }}</div>
                    </div>
                    @if($production->catatan)
                    <div class="flex-1 min-w-[180px]">
                        <label class="mb-1.5 block text-xs font-semibold text-gray-600">Catatan</label>
                        <div class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-700">{{ $production->catatan }}</div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Outputs --}}
            @foreach($production->outputs as $oIdx => $output)
            <div class="rounded-xl border border-gray-100 bg-white shadow-sm overflow-hidden">
                <div class="flex items-center justify-between border-b border-gray-100 bg-gray-50/50 px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-100 text-sm font-bold text-red-600">{{ $oIdx + 1 }}</div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900">{{ $output->bahanSiapProduksi->nama }}</h3>
                            <p class="text-xs text-gray-500">{{ $output->bahanSiapProduksi->kode }} — Target: {{ $output->jumlah_target }} unit</p>
                        </div>
                    </div>
                    @if($production->isSelesai())
                        @if($output->jumlah_selesai > 0)
                            <span class="inline-flex items-center rounded-lg bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700">Selesai: {{ $output->jumlah_selesai }}</span>
                        @endif
                    @else
                        <span class="inline-flex items-center rounded-lg bg-yellow-100 px-2.5 py-1 text-xs font-semibold text-yellow-700">Proses</span>
                    @endif
                </div>

                <div class="p-6">
                    <div class="overflow-x-auto rounded-xl border border-gray-100">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">No.</th>
                                    <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Kode</th>
                                    <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Nama</th>
                                    <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 text-right">Harga</th>
                                    <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 text-center">Jumlah</th>
                                    <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($output->items as $i => $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-gray-500">{{ $i + 1 }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $item->bahanBaku->kode ?? '-' }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ $item->bahanBaku->nama ?? '-' }}</td>
                                    <td class="px-4 py-3 text-right text-gray-700">Rp {{ number_format($item->bahanBaku->harga ?? 0, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-center font-semibold text-gray-900">{{ $item->jumlah }}</td>
                                    <td class="px-4 py-3 text-right font-medium text-gray-900">Rp {{ number_format(($item->bahanBaku->harga ?? 0) * $item->jumlah, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($production->isSelesai())
                    <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div class="rounded-lg bg-green-50 p-3 text-center">
                            <p class="text-xs font-semibold text-green-600">Selesai</p>
                            <p class="mt-1 text-2xl font-bold text-green-700">{{ $output->jumlah_selesai ?? 0 }}</p>
                        </div>
                        <div class="rounded-lg bg-red-50 p-3 text-center">
                            <p class="text-xs font-semibold text-red-600">Gagal</p>
                            <p class="mt-1 text-2xl font-bold text-red-700">{{ $output->jumlah_gagal ?? 0 }}</p>
                        </div>
                    </div>
                    @if($output->alasan_gagal)
                    <div class="mt-3 rounded-lg border border-red-200 bg-red-50 p-3">
                        <p class="text-xs font-semibold text-red-600">Alasan Gagal</p>
                        <p class="mt-1 text-sm text-red-700">{{ $output->alasan_gagal }}</p>
                    </div>
                    @endif
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        {{-- Right --}}
        <div class="space-y-6">
            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900">Ringkasan</h2>

                <div class="mt-4 space-y-3">
                    <div class="flex items-center justify-between rounded-lg bg-blue-50 px-4 py-3">
                        <span class="text-sm font-medium text-blue-700">BSP Output</span>
                        <span class="text-sm font-bold text-blue-800">{{ $production->outputs->count() }} item</span>
                    </div>
                    <div class="flex items-center justify-between rounded-lg bg-gray-50 px-4 py-3">
                        <span class="text-sm font-medium text-gray-700">Total Bahan Baku</span>
                        <span class="text-sm font-bold text-gray-900">{{ $production->outputs->sum(fn($o) => $o->items->count()) }} item</span>
                    </div>
                    @if($production->isSelesai())
                    <div class="flex items-center justify-between rounded-lg bg-green-50 px-4 py-3">
                        <span class="text-sm font-medium text-green-700">Total Selesai</span>
                        <span class="text-sm font-bold text-green-800">{{ $production->outputs->sum('jumlah_selesai') ?? 0 }} unit</span>
                    </div>
                    @endif
                </div>

                {{-- Status --}}
                <div class="mt-5">
                    <label class="mb-3 block text-sm font-semibold text-gray-700">Status</label>
                    @php
                        $steps = [
                            ['label' => 'Dibuat', 'done' => true],
                            ['label' => 'Proses', 'done' => $production->isSelesai()],
                            ['label' => 'Selesai', 'done' => $production->isSelesai()],
                        ];
                        $currentStep = $production->isProses() ? 1 : 2;
                    @endphp
                    <div class="flex items-start justify-between">
                        @foreach($steps as $idx => $step)
                        <div class="flex flex-col items-center" style="width: {{ 100 / count($steps) }}%">
                            <div class="relative flex w-full items-center justify-center">
                                @if($idx > 0)
                                <div class="absolute right-1/2 h-0.5 w-full {{ $step['done'] ? 'bg-green-400' : 'bg-gray-200' }}"></div>
                                @endif
                                @if($idx < count($steps) - 1)
                                <div class="absolute left-1/2 h-0.5 w-full {{ $steps[$idx + 1]['done'] ? 'bg-green-400' : 'bg-gray-200' }}"></div>
                                @endif
                                @if($step['done'])
                                    <div class="relative z-10 flex h-8 w-8 items-center justify-center rounded-full bg-green-500 shadow-sm shadow-green-200">
                                        <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    </div>
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
                            <p class="mt-2 text-center text-xs font-medium {{ $step['done'] ? 'text-green-600' : ($idx === $currentStep ? 'text-yellow-600' : 'text-gray-400') }}">{{ $step['label'] }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Report Form --}}
                @if($production->isProses())
                <div class="mt-5 border-t border-gray-100 pt-5">
                    <h3 class="text-sm font-bold text-gray-900">Laporan Hasil</h3>
                    <p class="mt-1 text-xs text-gray-500">Laporkan hasil produksi per BSP</p>

                    <div class="mt-4 space-y-4">
                        @foreach($production->outputs as $oIdx => $output)
                        <div class="rounded-lg border border-gray-100 p-3">
                            <p class="text-xs font-bold text-gray-800 mb-2">{{ $output->bahanSiapProduksi->nama }} <span class="font-normal text-gray-500">(Target: {{ $output->jumlah_target }})</span></p>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="mb-1 block text-xs font-semibold text-green-600">Selesai</label>
                                    <input type="number" x-model.number="reportOutputs[{{ $oIdx }}].jumlah_selesai" min="0" class="w-full rounded-lg border border-gray-200 px-2.5 py-2 text-sm focus:border-green-400 focus:outline-none focus:ring-2 focus:ring-green-400/20" placeholder="0">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold text-red-600">Gagal</label>
                                    <input type="number" x-model.number="reportOutputs[{{ $oIdx }}].jumlah_gagal" min="0" class="w-full rounded-lg border border-gray-200 px-2.5 py-2 text-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20" placeholder="0">
                                </div>
                            </div>
                            <div x-show="reportOutputs[{{ $oIdx }}].jumlah_gagal > 0" x-cloak class="mt-2">
                                <textarea x-model="reportOutputs[{{ $oIdx }}].alasan_gagal" rows="2" class="w-full rounded-lg border border-gray-200 px-2.5 py-2 text-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20" placeholder="Alasan gagal..."></textarea>
                            </div>
                        </div>
                        @endforeach

                        <button @click="submitReport()" :disabled="loading"
                                class="flex w-full items-center justify-center gap-2 rounded-xl bg-green-500 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-green-600 disabled:opacity-50">
                            <svg x-show="!loading" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <svg x-show="loading" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            Submit Laporan
                        </button>
                    </div>
                </div>
                @endif

                @if($production->isSelesai())
                <div class="mt-5 rounded-lg border border-green-200 bg-green-50 p-3">
                    <p class="text-xs font-semibold text-green-800">Produksi selesai dilaporkan</p>
                    <p class="mt-1 text-xs text-green-700">Oleh {{ $production->reporter->name ?? '-' }} pada {{ $production->reported_at?->format('d M Y H:i') }}</p>
                </div>
                @endif

                <div class="mt-5">
                    <a href="{{ route('produksi.production.index') }}" class="flex w-full items-center justify-center gap-2 rounded-xl border border-gray-200 px-5 py-3 text-sm font-medium text-gray-600 transition hover:bg-gray-50">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Kembali ke Daftar
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function productionDetail() {
    return {
        loading: false,
        reportOutputs: [
            @foreach($production->outputs as $output)
            { output_id: {{ $output->id }}, jumlah_selesai: 0, jumlah_gagal: 0, alasan_gagal: '' },
            @endforeach
        ],

        async submitReport() {
            for (const o of this.reportOutputs) {
                if (o.jumlah_gagal > 0 && !o.alasan_gagal?.trim()) {
                    Swal.fire('Perhatian', 'Alasan gagal wajib diisi jika ada produksi yang gagal.', 'warning');
                    return;
                }
            }

            const lines = this.reportOutputs.map((o, i) => {
                return `BSP ${i + 1}: Selesai ${o.jumlah_selesai}, Gagal ${o.jumlah_gagal}`;
            }).join('<br>');

            const result = await Swal.fire({
                title: 'Submit Laporan Produksi?',
                html: `<div style="text-align:left;font-size:14px;">${lines}<p class="mt-2 text-gray-500">BSP yang selesai akan ditambahkan ke stok.</p></div>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#22c55e',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Submit!',
                cancelButtonText: 'Batal'
            });
            if (!result.isConfirmed) return;

            this.loading = true;

            const res = await fetch('{{ route("produksi.production.report", $production) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ outputs: this.reportOutputs })
            });

            if (!res.ok) {
                const d = await res.json().catch(() => ({}));
                this.loading = false;
                Swal.fire('Error', d.message || 'Gagal menyimpan.', 'error');
                return;
            }

            window.location.reload();
        }
    }
}
</script>
@endpush
