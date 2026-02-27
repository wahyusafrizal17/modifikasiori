<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseSwitchController extends Controller
{
    public function switch(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'warehouse_id' => ['required', 'exists:warehouses,id'],
        ]);

        session(['active_warehouse_id' => (int) $request->warehouse_id]);

        $warehouse = Warehouse::find($request->warehouse_id);

        return response()->json([
            'success' => true,
            'warehouse' => [
                'id' => $warehouse->id,
                'nama' => $warehouse->nama,
            ],
        ]);
    }
}
