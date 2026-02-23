@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<div class="flex min-h-screen">
    {{-- Left Panel --}}
    <div class="relative hidden overflow-hidden bg-slate-900 lg:flex lg:flex-col" style="width: 58.333%">
        {{-- Background image --}}
        <div class="absolute inset-0">
            <img src="{{ asset('images/bg-workshop.png') }}" alt="" class="h-full w-full object-cover">
        </div>
        <div class="absolute inset-0 bg-slate-900/70"></div>

        {{-- Content: logo top center, footer bottom center --}}
        <div class="relative z-10 flex flex-1 flex-col items-center justify-between p-10">
            <div>
                <img src="{{ asset('images/logo-full.webp') }}" alt="Modifikasi Ori" class="w-56 xl:w-64">
            </div>
            <p class="text-sm text-white/50">&copy; {{ date('Y') }} ModifikasiOri. All rights reserved.</p>
        </div>
    </div>

    {{-- Right Panel - Form --}}
    <div class="flex w-full flex-col items-center justify-center bg-white px-6" style="width: 41.667%">
        <div class="w-full max-w-md">
            {{-- Mobile brand --}}
            <div class="mb-8 flex items-center gap-3 lg:hidden">
                <img src="{{ asset('images/logo.png') }}" alt="ModifikasiOri" class="h-10 w-10 rounded-xl object-contain">
                <span class="text-lg font-bold text-gray-900">ModifikasiOri</span>
            </div>

            {{-- Logo --}}
            <div class="mb-6 flex justify-center">
                <img src="{{ asset('images/logo.png') }}" alt="ModifikasiOri" class="h-24 w-24 object-contain">
            </div>

            {{-- Header --}}
            <h2 class="text-2xl font-bold text-gray-900 text-center">Masuk</h2>
            <p class="mt-2 text-sm text-gray-500 text-center">Masuk ke dashboard admin untuk mengelola toko Anda.</p>

            {{-- Error --}}
            @if ($errors->any())
            <div class="mt-5 flex items-start gap-3 rounded-xl border border-red-100 bg-red-50 p-4">
                <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm text-red-700">{{ $errors->first() }}</p>
            </div>
            @endif

            {{-- Form --}}
            <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        placeholder="nama@email.com"
                        class="mt-2 block w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 placeholder-gray-400 transition-all focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 @error('email') border-red-300 bg-red-50 focus:border-red-500 focus:ring-red-500/20 @enderror"
                    >
                </div>

                <div x-data="{ show: false }">
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <div class="relative mt-2">
                        <input
                            :type="show ? 'text' : 'password'"
                            id="password"
                            name="password"
                            required
                            placeholder="Minimal 6 karakter"
                            class="block w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 pr-11 text-sm text-gray-900 placeholder-gray-400 transition-all focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 @error('password') border-red-300 bg-red-50 focus:border-red-500 focus:ring-red-500/20 @enderror"
                        >
                        <button type="button" @click="show = !show" style="position:absolute;right:12px;top:50%;transform:translateY(-50%)" class="z-10 text-gray-400 transition hover:text-gray-600">
                            <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <svg x-show="show" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12c1.292 4.338 5.31 7.5 10.066 7.5.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                        </button>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" id="remember" name="remember" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <label for="remember" class="text-sm text-gray-500">Ingat saya</label>
                </div>

                <button type="submit"
                        class="w-full rounded-xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white transition-all hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900/50 focus:ring-offset-2 active:scale-[0.98]">
                    Masuk
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
