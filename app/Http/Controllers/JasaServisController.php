<?php

namespace App\Http\Controllers;

use App\Exports\JasaServisExport;
use App\Exports\JasaServisTemplateExport;
use App\Imports\JasaServisImport;
use App\Models\JasaServis;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class JasaServisController extends Controller
{
    public function index(Request $request)
    {
        $query = JasaServis::forUser();

        if ($request->filled('search')) {
            $query->where('nama', 'like', "%{$request->search}%");
        }

        $jasaServis = $query->orderBy('nama')->paginate(20)->withQueryString();

        return view('admin.jasa-servis.index', compact('jasaServis'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'biaya' => ['required', 'numeric', 'min:0'],
        ]);

        $validated['warehouse_id'] = auth()->user()->activeWarehouseId();
        JasaServis::create($validated);
        session()->flash('success', 'Jasa servis berhasil ditambahkan.');

        return response()->json(['success' => true]);
    }

    public function show(JasaServis $jasaServi)
    {
        return response()->json($jasaServi);
    }

    public function update(Request $request, JasaServis $jasaServi)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'biaya' => ['required', 'numeric', 'min:0'],
        ]);

        $jasaServi->update($validated);
        session()->flash('success', 'Jasa servis berhasil diperbarui.');

        return response()->json(['success' => true]);
    }

    public function destroy(JasaServis $jasaServi)
    {
        $jasaServi->delete();
        session()->flash('success', 'Jasa servis berhasil dihapus.');

        return response()->json(['success' => true]);
    }

    public function export()
    {
        return Excel::download(new JasaServisExport, 'jasa-servis-' . now()->format('Y-m-d-His') . '.xlsx');
    }

    public function template()
    {
        return Excel::download(new JasaServisTemplateExport, 'template-jasa-servis.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:5120'],
        ]);

        try {
            Excel::import(new JasaServisImport, $request->file('file'));
            session()->flash('success', 'Import jasa servis berhasil.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Import gagal: ' . $e->getMessage());
        }

        return redirect()->route('admin.jasa-servis.index');
    }
}
