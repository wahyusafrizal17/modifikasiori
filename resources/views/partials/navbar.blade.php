@php
    $adminSection = session('admin_section', 'admin');
    $adminWarehouseId = session('admin_warehouse_id');
    $isAdmin = auth()->user() && auth()->user()->isAdmin();

    if ($isAdmin) {
        $warehouses = \App\Models\Warehouse::orderBy('nama')->get();
        $activeWarehouse = $adminWarehouseId ? $warehouses->firstWhere('id', $adminWarehouseId) : null;

        $sectionLabels = [
            'admin' => 'Super Admin',
            'produksi' => 'Produksi',
            'warehouse' => 'Warehouse',
            'speedshop' => 'Speedshop',
        ];

        $currentLabel = $sectionLabels[$adminSection] ?? 'Super Admin';
        if ($activeWarehouse && in_array($adminSection, ['warehouse', 'speedshop'])) {
            $currentLabel = $sectionLabels[$adminSection] . ' · ' . $activeWarehouse->nama;
        }
    }
@endphp

<header class="flex h-16 items-center justify-between bg-white px-8 shadow-sm">
    <div class="flex items-center gap-3">
        {{-- Tombol buka menu (muncul saat sidebar ditutup, di sebelah kiri section switcher) --}}
        <button x-show="!sidebarOpen" x-cloak @click="sidebarOpen = true" class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-600 shadow-sm transition hover:bg-gray-50 hover:text-gray-900" title="Buka menu sidebar">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        {{-- Section Switcher (Admin only) --}}
        @if($isAdmin)
            <div class="relative" x-data="{ switchOpen: false }">
                <button @click="switchOpen = !switchOpen" class="flex items-center gap-2 rounded-xl border border-gray-200 px-3 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                    <svg class="h-4 w-4 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/></svg>
                    <span class="max-w-[180px] truncate">{{ $currentLabel }}</span>
                    <svg class="h-4 w-4 text-gray-400 transition" :class="switchOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>

                <div x-show="switchOpen" @click.away="switchOpen = false" x-cloak
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 translate-y-1"
                     class="absolute left-0 mt-2 max-h-96 w-72 overflow-y-auto rounded-xl border border-gray-100 bg-white shadow-xl z-50">

                    {{-- Produksi --}}
                    <form method="POST" action="{{ route('admin.switch-section') }}">
                        @csrf
                        <input type="hidden" name="section" value="produksi">
                        <button type="submit" class="flex w-full items-center gap-3 px-4 py-2.5 text-sm font-medium transition {{ $adminSection === 'produksi' ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-50' }}">
                            @if($adminSection === 'produksi')
                                <svg class="h-4 w-4 text-red-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            @else
                                <span class="h-4 w-4"></span>
                            @endif
                            Produksi
                        </button>
                    </form>

                    {{-- Warehouse branches --}}
                    <p class="mt-1 border-t border-gray-100 px-4 pt-2.5 pb-1 text-[10px] font-bold uppercase tracking-widest text-gray-400">Warehouse</p>
                    @foreach($warehouses as $wh)
                        <form method="POST" action="{{ route('admin.switch-section') }}">
                            @csrf
                            <input type="hidden" name="section" value="warehouse">
                            <input type="hidden" name="warehouse_id" value="{{ $wh->id }}">
                            <button type="submit" class="flex w-full items-center gap-3 px-4 py-2 text-sm font-medium transition {{ $adminSection === 'warehouse' && $adminWarehouseId == $wh->id ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-50' }}">
                                @if($adminSection === 'warehouse' && $adminWarehouseId == $wh->id)
                                    <svg class="h-4 w-4 text-red-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                @else
                                    <span class="h-4 w-4"></span>
                                @endif
                                <span class="truncate">{{ $wh->nama }}</span>
                            </button>
                        </form>
                    @endforeach

                    {{-- Speedshop branches --}}
                    <p class="mt-1 border-t border-gray-100 px-4 pt-2.5 pb-1 text-[10px] font-bold uppercase tracking-widest text-gray-400">Speedshop</p>
                    @foreach($warehouses as $wh)
                        <form method="POST" action="{{ route('admin.switch-section') }}">
                            @csrf
                            <input type="hidden" name="section" value="speedshop">
                            <input type="hidden" name="warehouse_id" value="{{ $wh->id }}">
                            <button type="submit" class="flex w-full items-center gap-3 px-4 py-2 text-sm font-medium transition {{ $adminSection === 'speedshop' && $adminWarehouseId == $wh->id ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-50' }}">
                                @if($adminSection === 'speedshop' && $adminWarehouseId == $wh->id)
                                    <svg class="h-4 w-4 text-red-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                @else
                                    <span class="h-4 w-4"></span>
                                @endif
                                <span class="truncate">{{ $wh->nama }}</span>
                            </button>
                        </form>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <div class="flex items-center gap-3">
        {{-- Notification --}}
        <div class="relative" x-data="notifDropdown()" x-init="fetchNotifications()">
            <button @click="toggle()" class="relative rounded-xl border border-gray-200 p-2.5 text-gray-500 transition hover:bg-gray-50 hover:text-gray-700">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                <span x-show="unreadCount > 0" class="absolute right-1.5 top-1.5 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[9px] font-bold text-white ring-2 ring-white" x-text="unreadCount > 9 ? '9+' : unreadCount"></span>
            </button>

            <div x-show="open" @click.away="open = false" x-cloak
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-1"
                 class="absolute right-0 mt-2 w-80 rounded-xl border border-gray-100 bg-white shadow-xl z-50">
                <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
                    <p class="text-sm font-bold text-gray-900">Notifikasi</p>
                    <button x-show="unreadCount > 0" @click="markAllRead()" class="text-xs font-medium text-red-500 hover:text-red-600">Tandai semua dibaca</button>
                </div>
                <div class="max-h-72 overflow-y-auto">
                    <template x-if="notifications.length === 0">
                        <div class="flex flex-col items-center justify-center py-10 px-4">
                            <svg class="h-10 w-10 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            <p class="mt-3 text-sm font-medium text-gray-400">Belum ada notifikasi</p>
                        </div>
                    </template>
                    <template x-for="notif in notifications" :key="notif.id">
                        <a :href="notif.link || '#'" @click="markRead(notif)" class="flex gap-3 border-b border-gray-50 px-4 py-3 transition hover:bg-gray-50" :class="!notif.read_at && 'bg-red-50/50'">
                            <div class="flex-shrink-0 mt-0.5">
                                <div class="flex h-8 w-8 items-center justify-center rounded-full" :class="notif.type === 'stock_in_approved' ? 'bg-green-100' : (notif.type === 'stock_in_rejected' ? 'bg-red-100' : (notif.type === 'mutasi_produk' ? 'bg-blue-100' : 'bg-yellow-100'))">
                                    <svg x-show="notif.type === 'stock_in_approved'" class="h-4 w-4 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    <svg x-show="notif.type === 'stock_in_rejected'" class="h-4 w-4 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    <svg x-show="notif.type === 'mutasi_produk'" class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                    <svg x-show="!['stock_in_approved','stock_in_rejected','mutasi_produk'].includes(notif.type)" class="h-4 w-4 text-yellow-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-gray-900 truncate" x-text="notif.title"></p>
                                <p class="mt-0.5 text-xs text-gray-500 line-clamp-2" x-text="notif.message"></p>
                                <p class="mt-1 text-[10px] text-gray-400" x-text="timeAgo(notif.created_at)"></p>
                            </div>
                            <div x-show="!notif.read_at" class="flex-shrink-0 mt-2">
                                <span class="h-2 w-2 rounded-full bg-red-500 block"></span>
                            </div>
                        </a>
                    </template>
                </div>
            </div>
        </div>

        {{-- Profile --}}
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="flex items-center gap-3 rounded-xl px-3 py-2 transition hover:bg-gray-50">
                <div class="text-right">
                    <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                </div>
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-9 w-9 rounded-full object-cover">
            </button>

            <div x-show="open" @click.away="open = false" x-cloak
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-1"
                 class="absolute right-0 mt-2 w-48 rounded-xl border border-gray-100 bg-white py-2 shadow-xl z-50">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex w-full items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-medium text-red-500 transition hover:bg-red-50">
                        <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>

@once
@push('scripts')
<script>
function notifDropdown() {
    return {
        open: false,
        notifications: [],
        unreadCount: 0,

        toggle() {
            this.open = !this.open;
            if (this.open) this.fetchNotifications();
        },

        async fetchNotifications() {
            const res = await fetch('/notifications', { headers: { 'Accept': 'application/json' } });
            const data = await res.json();
            this.notifications = data.notifications;
            this.unreadCount = data.unread_count;
        },

        async markRead(notif) {
            if (!notif.read_at) {
                await fetch(`/notifications/${notif.id}/read`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                notif.read_at = new Date().toISOString();
                this.unreadCount = Math.max(0, this.unreadCount - 1);
            }
        },

        async markAllRead() {
            await fetch('/notifications/mark-all-read', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            });
            this.notifications.forEach(n => n.read_at = new Date().toISOString());
            this.unreadCount = 0;
        },

        timeAgo(dateStr) {
            const diff = (Date.now() - new Date(dateStr).getTime()) / 1000;
            if (diff < 60) return 'Baru saja';
            if (diff < 3600) return Math.floor(diff / 60) + ' menit lalu';
            if (diff < 86400) return Math.floor(diff / 3600) + ' jam lalu';
            return Math.floor(diff / 86400) + ' hari lalu';
        }
    }
}
</script>
@endpush
@endonce
