<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Speedshop extends Model
{
    use HasFactory;

    protected $fillable = ['nama', 'no_hp', 'alamat', 'kota_id', 'warehouse_id', 'mutasi_warehouse_id'];

    public function kota()
    {
        return $this->belongsTo(Kota::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    /** Warehouse yang dipakai sebagai tujuan mutasi (biasanya warehouse dengan nama sama) */
    public function mutasiWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'mutasi_warehouse_id');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
