<?php

namespace App\Http\Controllers;

use App\Http\Requests\MekanikRequest;
use App\Models\Mekanik;
use Illuminate\Http\Request;

class MekanikController extends Controller
{
    public function index(Request $request)
    {
        $query = Mekanik::query();
        if ($request->filled('search')) {
            $query->where('nama','like',"%{$request->search}%");
        }
        $mekaniks = $query->latest()->paginate(20)->withQueryString();
        return view('admin.mekaniks.index', compact('mekaniks'));
    }

    public function store(MekanikRequest $request)
    {
        Mekanik::create($request->validated());
        session()->flash('success','Mekanik berhasil ditambahkan.');
        return response()->json(['success'=>true]);
    }

    public function show(Mekanik $mekanik)
    {
        return response()->json($mekanik);
    }

    public function update(MekanikRequest $request, Mekanik $mekanik)
    {
        $mekanik->update($request->validated());
        session()->flash('success','Mekanik berhasil diperbarui.');
        return response()->json(['success'=>true]);
    }

    public function destroy(Mekanik $mekanik)
    {
        $mekanik->delete();
        session()->flash('success','Mekanik berhasil dihapus.');
        return response()->json(['success'=>true]);
    }
}
