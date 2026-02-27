@extends('layouts.admin')

@section('title', 'Produksi')

@section('content')
<div x-data="productionsPage()" class="space-y-6">
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.dashboard') }}" class="transition hover:text-gray-700">Home</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="font-medium text-gray-900">Produksi</span>
    </nav>

    @include('partials.flash')

    <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-xl font-bold text-gray-900">Produksi</h2>
            <div class="flex flex-wrap items-center gap-3">
                <form method="GET" class="flex items-center gap-2">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode produksi..." class="h-10 w-56 rounded-xl border border-gray-200 bg-gray-50 pl-10 pr-4 text-sm text-gray-700 placeholder-gray-400 transition focus:border-red-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-400/20">
                    </div>
                    <select name="status" onchange="this.form.submit()" class="h-10 rounded-xl border border-gray-200 bg-gray-50 px-3 text-sm text-gray-700 focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20">
                        <option value="">Semua Status</option>
                        <option value="proses" {{ request('status') === 'proses' ? 'selected' : '' }}>Proses</option>
                        <option value="qc" {{ request('status') === 'qc' ? 'selected' : '' }}>QC</option>
                        <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="gagal" {{ request('status') === 'gagal' ? 'selected' : '' }}>Gagal</option>
                    </select>
                </form>
                <a href="{{ route('admin.productions.create') }}" class="inline-flex h-10 items-center gap-2 rounded-xl bg-red-500 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-600 active:scale-[0.98]">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Buat Produksi
                </a>
            </div>
        </div>

        <div class="mt-6 overflow-x-auto rounded-xl border border-gray-100">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 w-16">No.</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Kode Produksi</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Tanggal</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Status</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Dibuat oleh</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 w-28"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($productions as $i => $production)
                    <tr class="transition hover:bg-gray-50">
                        <td class="px-5 py-4 text-gray-500">{{ $productions->firstItem() + $i }}</td>
                        <td class="px-5 py-4 font-medium text-gray-900">{{ $production->kode_produksi }}</td>
                        <td class="px-5 py-4 text-gray-700">{{ $production->tanggal->format('d/m/Y') }}</td>
                        <td class="px-5 py-4">{!! $production->status_badge !!}</td>
                        <td class="px-5 py-4 text-gray-600">{{ $production->user->name ?? '-' }}</td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.productions.show', $production) }}" class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-500 text-white shadow-sm transition hover:bg-blue-600" title="Lihat Detail">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                @if($production->status !== 'selesai')
                                <button @click="destroy({{ $production->id }})" class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-500 text-white shadow-sm transition hover:bg-red-600" title="Hapus">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-5 py-12 text-center text-gray-400">Belum ada data produksi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($productions->hasPages())
        <div class="mt-5">{{ $productions->links() }}</div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function productionsPage() {
    return {
        async destroy(id) {
            const result = await Swal.fire({
                title: 'Hapus produksi?',
                text: 'Data yang dihapus tidak dapat dikembalikan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            });
            if (!result.isConfirmed) return;
            await fetch(`/admin/productions/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });
            window.location.reload();
        }
    }
}
</script>
@endpush
