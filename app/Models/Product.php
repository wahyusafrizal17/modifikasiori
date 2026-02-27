<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_produk',
        'nama_produk',
        'category_id',
        'brand_id',
        'jumlah',
        'harga_pembelian',
        'harga_jual',
        'warehouse_id',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
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

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }
}
