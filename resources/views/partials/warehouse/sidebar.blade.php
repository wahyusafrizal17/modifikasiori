<aside class="sticky top-0 flex hidden h-screen w-[250px] flex-shrink-0 flex-col overflow-hidden bg-white shadow-sm md:flex">
    <div class="flex-shrink-0 px-6 py-5">
        <img src="{{ asset('images/logo-full.webp') }}" alt="Modifikasi Ori" class="h-10 object-contain">
    </div>

    <nav class="min-h-0 flex-1 space-y-1 overflow-y-auto px-5 pb-4">
        <a href="{{ route('warehouse.dashboard') }}"
           class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium transition {{ request()->routeIs('warehouse.dashboard') ? 'bg-red-500 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
            Dashboard
        </a>

        <p class="mt-4 mb-2 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-400">Warehouse</p>

        <div class="relative" x-data="{ open: {{ request()->routeIs('warehouse.transaksi.*', 'warehouse.transaksi-speedshop*') ? 'true' : 'false' }} }">
            <button @click="open = !open" class="flex w-full items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium transition {{ request()->routeIs('warehouse.transaksi.*', 'warehouse.transaksi-speedshop*') ? 'bg-red-500 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                Stock IN
                <svg class="ml-auto h-4 w-4 transition-transform" :class="open && 'rotate-180'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="mt-1 space-y-0.5 pl-4">
                <a href="{{ route('warehouse.transaksi.index') }}" class="flex items-center gap-3 rounded-lg px-4 py-2 text-sm transition {{ request()->routeIs('warehouse.transaksi.*') && !request()->routeIs('warehouse.transaksi-speedshop') ? 'bg-red-50 font-medium text-red-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    Transaksi
                </a>
                <a href="{{ route('warehouse.transaksi-speedshop') }}" class="flex items-center gap-3 rounded-lg px-4 py-2 text-sm transition {{ request()->routeIs('warehouse.transaksi-speedshop*') ? 'bg-red-50 font-medium text-red-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    Transaksi Speedshop
                </a>
            </div>
        </div>

        <div class="relative" x-data="{ open: {{ request()->routeIs('warehouse.transaksi-offline*', 'warehouse.transaksi-online*') ? 'true' : 'false' }} }">
            <button @click="open = !open" class="flex w-full items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium transition {{ request()->routeIs('warehouse.transaksi-offline*', 'warehouse.transaksi-online*') ? 'bg-red-500 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                Transaksi
                <svg class="ml-auto h-4 w-4 transition-transform" :class="open && 'rotate-180'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="mt-1 space-y-0.5 pl-4">
                <a href="{{ route('warehouse.transaksi-offline.index') }}" class="flex items-center gap-3 rounded-lg px-4 py-2 text-sm transition {{ request()->routeIs('warehouse.transaksi-offline*') ? 'bg-red-50 font-medium text-red-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    Offline
                </a>
                <a href="{{ route('warehouse.transaksi-online.index') }}" class="flex items-center gap-3 rounded-lg px-4 py-2 text-sm transition {{ request()->routeIs('warehouse.transaksi-online*') ? 'bg-red-50 font-medium text-red-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    Online
                </a>
            </div>
        </div>

        <a href="{{ route('warehouse.products.index') }}"
           class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium transition {{ request()->routeIs('warehouse.products.*') ? 'bg-red-500 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            Product
        </a>
    </nav>

    <div class="flex-shrink-0 border-t border-gray-100 p-4">
        <button @click="sidebarOpen = false" class="flex w-full items-center justify-center gap-2 rounded-xl bg-gray-100 px-4 py-3 text-sm font-medium text-gray-700 transition hover:bg-gray-200">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/></svg>
            Tutup Menu
        </button>
    </div>
</aside>
