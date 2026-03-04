@extends('layouts.admin')

@section('title', $title)

@section('content')
<div class="space-y-8">
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.dashboard') }}" class="transition hover:text-gray-700">Home</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="font-medium text-gray-900">{{ $title }}</span>
    </nav>

    <div class="flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-gray-200 bg-white py-24">
        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-red-50">
            <svg class="h-8 w-8 text-red-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        </div>
        <h2 class="mt-6 text-xl font-bold text-gray-900">{{ $title }}</h2>
        <p class="mt-2 text-sm text-gray-500">Halaman ini sedang dalam pengembangan.</p>
    </div>
</div>
@endsection
