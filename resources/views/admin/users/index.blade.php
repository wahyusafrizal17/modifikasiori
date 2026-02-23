@extends('layouts.admin')

@section('title', 'Users')

@section('content')
<div x-data="usersCrud()" class="space-y-6">
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.dashboard') }}" class="transition hover:text-gray-700">Home</a>
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="font-medium text-gray-900">Users</span>
    </nav>

    @include('partials.flash')

    <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-xl font-bold text-gray-900">Users</h2>
            <div class="flex items-center gap-3">
                <form method="GET" class="relative">
                    <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." class="h-10 w-56 rounded-xl border border-gray-200 bg-gray-50 pl-10 pr-4 text-sm text-gray-700 placeholder-gray-400 transition focus:border-red-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-400/20">
                </form>
                <button @click="openCreate()" class="inline-flex h-10 items-center gap-2 rounded-xl bg-green-500 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-green-600 active:scale-[0.98]">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Add New User
                </button>
            </div>
        </div>

        <div class="mt-6 overflow-x-auto rounded-xl border border-gray-100">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 w-16">No.</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Fullname</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Username</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Role</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500">Kota</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-gray-500 w-28"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $i => $user)
                    <tr class="transition hover:bg-gray-50">
                        <td class="px-5 py-4 text-gray-500">{{ $users->firstItem() + $i }}</td>
                        <td class="px-5 py-4 font-medium text-gray-900">{{ $user->name }}</td>
                        <td class="px-5 py-4 text-gray-700">{{ $user->username ?? '-' }}</td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center rounded-lg px-2.5 py-1 text-xs font-semibold {{ $user->role === 'Admin' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700' }}">{{ $user->role }}</span>
                        </td>
                        <td class="px-5 py-4 text-gray-700">{{ $user->kota->nama ?? '-' }}</td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <button @click="openEdit({{ $user->id }})" class="flex h-8 w-8 items-center justify-center rounded-lg bg-green-500 text-white shadow-sm transition hover:bg-green-600">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </button>
                                @if($user->id !== auth()->id())
                                <button @click="destroy({{ $user->id }})" class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-500 text-white shadow-sm transition hover:bg-red-600">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-5 py-12 text-center text-gray-400">Belum ada data user.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
        <div class="mt-5">{{ $users->links() }}</div>
        @endif
    </div>

    {{-- Modal --}}
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" @keydown.escape.window="showModal = false">
        <div x-show="showModal" x-transition.opacity class="fixed inset-0 bg-black/50" @click="showModal = false"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div x-show="showModal" x-transition class="relative w-full max-w-lg rounded-2xl bg-white p-8 shadow-xl" @click.stop>
                <h3 class="text-lg font-bold text-gray-900" x-text="isEdit ? 'Edit User' : 'Tambah User Baru'"></h3>
                <div class="mt-6 space-y-4">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Fullname</label>
                        <input type="text" x-model="form.name" class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm transition focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20" :class="errors.name && 'border-red-400'">
                        <template x-if="errors.name"><p class="mt-1 text-xs text-red-500" x-text="errors.name[0]"></p></template>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Username</label>
                        <input type="text" x-model="form.username" class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm transition focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20" :class="errors.username && 'border-red-400'">
                        <template x-if="errors.username"><p class="mt-1 text-xs text-red-500" x-text="errors.username[0]"></p></template>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" x-model="form.email" class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm transition focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20" :class="errors.email && 'border-red-400'">
                        <template x-if="errors.email"><p class="mt-1 text-xs text-red-500" x-text="errors.email[0]"></p></template>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">
                            Password
                            <span x-show="isEdit" class="text-gray-400 font-normal">(kosongkan jika tidak ingin mengubah)</span>
                        </label>
                        <input type="password" x-model="form.password" class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm transition focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20" :class="errors.password && 'border-red-400'" :required="!isEdit">
                        <template x-if="errors.password"><p class="mt-1 text-xs text-red-500" x-text="errors.password[0]"></p></template>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">Role</label>
                            <select x-model="form.role" class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm transition focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20" :class="errors.role && 'border-red-400'">
                                <option value="User">User</option>
                                <option value="Admin">Admin</option>
                            </select>
                            <template x-if="errors.role"><p class="mt-1 text-xs text-red-500" x-text="errors.role[0]"></p></template>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">Kota</label>
                            <select x-model="form.kota_id" class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm transition focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-400/20" :class="errors.kota_id && 'border-red-400'">
                                <option value="">Pilih Kota</option>
                                @foreach($kotas as $kota)
                                    <option value="{{ $kota->id }}">{{ $kota->nama }}</option>
                                @endforeach
                            </select>
                            <template x-if="errors.kota_id"><p class="mt-1 text-xs text-red-500" x-text="errors.kota_id[0]"></p></template>
                        </div>
                    </div>
                </div>
                <div class="mt-8 flex items-center gap-3">
                    <button @click="save()" :disabled="loading" class="rounded-xl bg-red-500 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-600 disabled:opacity-50">
                        <span x-show="!loading" x-text="isEdit ? 'Update' : 'Simpan'"></span>
                        <span x-show="loading">Menyimpan...</span>
                    </button>
                    <button @click="showModal = false" class="rounded-xl border border-gray-200 px-6 py-2.5 text-sm font-medium text-gray-600 transition hover:bg-gray-50">Batal</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function usersCrud() {
    return {
        showModal: false, isEdit: false, editId: null, loading: false, errors: {},
        form: { name: '', username: '', email: '', password: '', role: 'User', kota_id: '' },
        openCreate() {
            this.isEdit = false; this.editId = null; this.errors = {};
            this.form = { name: '', username: '', email: '', password: '', role: 'User', kota_id: '' };
            this.showModal = true;
        },
        async openEdit(id) {
            this.isEdit = true; this.editId = id; this.errors = {}; this.loading = true; this.showModal = true;
            const res = await fetch(`/admin/users/${id}`, { headers: { 'Accept': 'application/json' } });
            const data = await res.json();
            this.form = { name: data.name, username: data.username || '', email: data.email, password: '', role: data.role, kota_id: data.kota_id ? String(data.kota_id) : '' };
            this.loading = false;
        },
        async save() {
            this.loading = true; this.errors = {};
            const url = this.isEdit ? `/admin/users/${this.editId}` : '/admin/users';
            const payload = { ...this.form };
            if (!payload.kota_id) payload.kota_id = null;
            if (this.isEdit && !payload.password) payload.password = null;
            const res = await fetch(url, {
                method: this.isEdit ? 'PUT' : 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                body: JSON.stringify(payload)
            });
            if (!res.ok) { const d = await res.json(); this.errors = d.errors || {}; this.loading = false; return; }
            window.location.reload();
        },
        async destroy(id) {
            const result = await Swal.fire({ title: 'Hapus user?', text: 'Data yang dihapus tidak dapat dikembalikan.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#6b7280', confirmButtonText: 'Ya, hapus!', cancelButtonText: 'Batal' });
            if (!result.isConfirmed) return;
            const res = await fetch(`/admin/users/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' } });
            if (!res.ok) { const d = await res.json(); Swal.fire({ title: 'Gagal!', text: d.message || 'Gagal menghapus user.', icon: 'error', confirmButtonColor: '#ef4444' }); return; }
            window.location.reload();
        }
    }
}
</script>
@endpush
