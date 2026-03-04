@props(['exportRoute', 'exportLabel' => 'Download Excel', 'dari' => null, 'sampai' => null])
@php
    $dariVal = $dari ?? request('dari', now()->startOfMonth()->format('Y-m-d'));
    $sampaiVal = $sampai ?? request('sampai', now()->format('Y-m-d'));
    if ($dari instanceof \Carbon\Carbon) $dariVal = $dari->format('Y-m-d');
    if ($sampai instanceof \Carbon\Carbon) $sampaiVal = $sampai->format('Y-m-d');
@endphp
<form method="GET" action="{{ request()->url() }}" class="flex flex-wrap items-end gap-3">
    <div>
        <label class="mb-1 block text-xs font-semibold text-gray-600">Dari</label>
        <input type="date" name="dari" value="{{ $dariVal }}" class="rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20">
    </div>
    <div>
        <label class="mb-1 block text-xs font-semibold text-gray-600">Sampai</label>
        <input type="date" name="sampai" value="{{ $sampaiVal }}" class="rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20">
    </div>
    <button type="submit" class="rounded-xl bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-800">Filter</button>
    <a href="{{ route($exportRoute, ['dari' => $dariVal, 'sampai' => $sampaiVal]) }}" class="inline-flex items-center gap-2 rounded-xl bg-emerald-500 px-4 py-2 text-sm font-medium text-white transition hover:bg-emerald-600">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        {{ $exportLabel }}
    </a>
</form>
