<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = ['nama', 'no_hp', 'alamat', 'kota_id', 'warehouse_id'];

    public function kota()
    {
        return $this->belongsTo(Kota::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function scopeForUser($query, $user = null)
    {
        $user ??= auth()->user();
        if ($user) {
            $query->where('warehouse_id', $user->activeWarehouseId());
        }
        return $query;
    }
}
