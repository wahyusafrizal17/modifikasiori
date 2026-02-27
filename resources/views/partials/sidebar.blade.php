<aside class="sticky top-0 hidden h-screen w-[250px] flex-shrink-0 flex-col overflow-y-auto bg-white shadow-sm md:flex">
    <div class="px-6 py-5">
        <img src="{{ asset('images/logo-full.webp') }}" alt="ModifikasiOri" class="h-10 object-contain">
    </div>

    <nav class="flex-1 space-y-1 px-5 pb-6">
        <a href="{{ route('admin.dashboard') }}"
           class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.dashboard') ? 'bg-red-500 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
            Dashboard
        </a>

        @if(auth()->user()->isAdmin())
        {{-- Bahan Baku (Admin Only) --}}
        <p class="mt-6 mb-2 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-400">Bahan Baku</p>

        <a href="{{ route('admin.purchases.index') }}"
           class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.purchases.*') ? 'bg-red-500 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
            Pembelian
        </a>

        {{-- Produksi (Admin Only) --}}
        <p class="mt-6 mb-2 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-400">Produksi</p>

        <a href="{{ route('admin.productions.index') }}"
           class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.productions.*') ? 'bg-red-500 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
            Produksi
        </a>

        <a href="{{ route('admin.wip.index') }}"
           class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.wip.*') ? 'bg-red-500 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Work In Progress
        </a>

        {{-- Warehouse (Admin Only) --}}
        <p class="mt-6 mb-2 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-400">Warehouse</p>

        <a href="{{ route('admin.stock-mutations.index') }}"
           class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.stock-mutations.*') ? 'bg-red-500 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
            Mutasi Stok
        </a>
        @endif

        {{-- Stok Bengkel (Non-Admin) --}}
        @if(!auth()->user()->isAdmin())
        <p class="mt-6 mb-2 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-400">Stok</p>

        <a href="{{ route('admin.stock-receipt.index') }}"
           class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.stock-receipt.*') ? 'bg-red-500 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Terima Stok
        </a>

        <a href="{{ route('admin.purchases.index') }}"
           class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.purchases.*') ? 'bg-red-500 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
            Pembelian Luar
        </a>
        @endif

        {{-- Bengkel / Speed Shop --}}
        <p class="mt-6 mb-2 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-400">Bengkel</p>

        <a href="{{ route('admin.service-orders.index') }}"
           class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.service-orders.*') ? 'bg-red-500 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            Work Order
        </a>

        <a href="{{ route('admin.invoices.index') }}"
           class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.invoices.*') ? 'bg-red-500 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            Transaksi
        </a>

        <a href="{{ route('admin.laporan.index') }}"
           class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.laporan.*') ? 'bg-red-500 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Laporan
        </a>

        <a href="{{ route('admin.pelanggans.index') }}"
           class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.pelanggans.*') ? 'bg-red-500 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Pelanggan
        </a>

        <a href="{{ route('admin.kendaraans.index') }}"
           class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.kendaraans.*') ? 'bg-red-500 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            Kendaraan
        </a>

        <a href="{{ route('admin.mekaniks.index') }}"
           class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.mekaniks.*') ? 'bg-red-500 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Mekanik
        </a>

        @if(auth()->user()->role === 'Admin')
        {{-- Master Data --}}
        <p class="mt-6 mb-2 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-400">Master Data</p>

        <a href="{{ route('admin.products.index') }}"
           class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.products.*') ? 'bg-red-500 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            Products
        </a>

        <a href="{{ route('admin.jasa-servis.index') }}"
           class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.jasa-servis.*') ? 'bg-red-500 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17l-5.1-5.1M7 7h.01M3 12a9 9 0 1018 0 9 9 0 00-18 0z"/></svg>
            Jasa Servis
        </a>

        <a href="{{ route('admin.categories.index') }}"
           class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.categories.*') ? 'bg-red-500 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
            Kategori Produk
        </a>

        <a href="{{ route('admin.brands.index') }}"
           class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.brands.*') ? 'bg-red-500 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
            Brand Produk
        </a>

        <a href="{{ route('admin.suppliers.index') }}"
           class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.suppliers.*') ? 'bg-red-500 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            Suppliers
        </a>

        <a href="{{ route('admin.kotas.index') }}"
           class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.kotas.*') ? 'bg-red-500 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Kota
        </a>

        <a href="{{ route('admin.warehouses.index') }}"
           class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.warehouses.*') ? 'bg-red-500 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/></svg>
            Warehouse
        </a>

        <a href="{{ route('admin.users.index') }}"
           class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.users.*') ? 'bg-red-500 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            Users
        </a>
        @endif
    </nav>
</aside>
