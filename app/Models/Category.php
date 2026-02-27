<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['nama', 'warehouse_id'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
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
