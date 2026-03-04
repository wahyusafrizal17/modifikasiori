<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BahanSiapProduksi extends Model
{
    protected $table = 'bahan_siap_produksis';

    protected $fillable = ['kode', 'nama', 'stok'];

    public static function generateKode(): string
    {
        $last = static::where('kode', 'like', 'BSP-%')->latest('id')->first();
        $seq = $last ? (int) substr($last->kode, -4) + 1 : 1;
        return 'BSP-' . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}
