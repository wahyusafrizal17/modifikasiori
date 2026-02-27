<header class="flex h-16 items-center justify-between bg-white px-8 shadow-sm">
    <div></div>

    <div class="flex items-center gap-4">
        {{-- Warehouse Switcher (Admin only) --}}
        @if(auth()->user()->isAdmin())
        @php
            $warehouses = \App\Models\Warehouse::orderBy('nama')->get();
            $activeWh = auth()->user()->activeWarehouse();
        @endphp
        <div x-data="warehouseSwitcher()" class="relative">
            <button @click="open = !open" class="flex items-center gap-2.5 rounded-xl border border-gray-200 bg-gray-50 px-4 py-2 text-sm transition hover:border-red-200 hover:bg-red-50">
                <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/></svg>
                <span class="font-medium text-gray-700" x-text="activeWarehouseName">{{ $activeWh?->nama ?? 'Pilih Warehouse' }}</span>
                <svg class="h-3.5 w-3.5 text-gray-400 transition" :class="open && 'rotate-180'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </button>

            <div x-show="open" x-cloak @click.away="open = false"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-1"
                 class="absolute right-0 mt-2 w-72 rounded-xl border border-gray-100 bg-white py-2 shadow-xl z-50">
                <p class="px-4 py-2 text-[10px] font-bold uppercase tracking-widest text-gray-400">Switch Warehouse</p>
                @foreach($warehouses as $wh)
                <button @click="switchWarehouse({{ $wh->id }}, '{{ $wh->nama }}')"
                        class="flex w-full items-center gap-3 px-4 py-2.5 text-left text-sm transition hover:bg-gray-50"
                        :class="{{ $wh->id }} === activeWarehouseId && 'bg-red-50 text-red-600 font-semibold'">
                    <span class="flex h-7 w-7 items-center justify-center rounded-lg text-xs font-bold"
                          :class="{{ $wh->id }} === activeWarehouseId ? 'bg-red-500 text-white' : 'bg-gray-100 text-gray-500'">
                        {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                    </span>
                    <span>{{ $wh->nama }}</span>
                    <template x-if="{{ $wh->id }} === activeWarehouseId">
                        <svg class="ml-auto h-4 w-4 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    </template>
                </button>
                @endforeach
            </div>
        </div>
        @endif

        {{-- User dropdown --}}
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="flex items-center gap-3 rounded-xl px-3 py-2 transition hover:bg-white/60">
                <div class="text-right">
                    <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                </div>
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-9 w-9 rounded-full object-cover">
            </button>

            <div x-show="open" @click.away="open = false" x-transition
                 class="absolute right-0 mt-2 w-48 rounded-xl border border-gray-100 bg-white py-2 shadow-lg z-50">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex w-full items-center gap-2 px-4 py-2.5 text-sm font-medium text-red-500 transition hover:bg-gray-50">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>

@if(auth()->user()->isAdmin())
<script>
function warehouseSwitcher() {
    return {
        open: false,
        activeWarehouseId: {{ auth()->user()->activeWarehouseId() ?? 'null' }},
        activeWarehouseName: '{{ auth()->user()->activeWarehouse()?->nama ?? "Pilih Warehouse" }}',

        async switchWarehouse(id, nama) {
            if (id === this.activeWarehouseId) {
                this.open = false;
                return;
            }

            try {
                const res = await fetch('{{ route("admin.switch-warehouse") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ warehouse_id: id }),
                });

                const data = await res.json();
                if (data.success) {
                    this.activeWarehouseId = id;
                    this.activeWarehouseName = nama;
                    this.open = false;
                    window.location.reload();
                }
            } catch (e) {
                console.error('Switch warehouse failed', e);
            }
        }
    }
}
</script>
@endif
