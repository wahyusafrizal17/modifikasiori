<?php

namespace App\Http\Controllers;

use App\Models\Kota;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('kota');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(20)->withQueryString();
        $kotas = Kota::orderBy('nama')->get();

        return view('admin.users.index', compact('users', 'kotas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:255', 'unique:users'],
            'email' => ['required', 'email', 'max:255', 'unique:users'],
            'password' => ['required', Password::min(6)],
            'role' => ['required', 'string', 'in:Admin,User'],
            'kota_id' => ['nullable', 'exists:kotas,id'],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        User::create($validated);
        session()->flash('success', 'User berhasil ditambahkan.');

        return response()->json(['success' => true]);
    }

    public function show(User $user)
    {
        return response()->json($user);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:255', 'unique:users,username,' . $user->id],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', Password::min(6)],
            'role' => ['required', 'string', 'in:Admin,User'],
            'kota_id' => ['nullable', 'exists:kotas,id'],
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);
        session()->flash('success', 'User berhasil diperbarui.');

        return response()->json(['success' => true]);
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Tidak dapat menghapus akun sendiri.'], 422);
        }

        $user->delete();
        session()->flash('success', 'User berhasil dihapus.');

        return response()->json(['success' => true]);
    }
}
