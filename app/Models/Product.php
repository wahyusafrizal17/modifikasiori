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
        'jumlah',
        'harga_pembelian',
        'harga_jual',
        'warehouse_id',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function scopeForUser($query, $user = null)
    {
        $user ??= auth()->user();
        if ($user && !$user->isAdmin()) {
            $query->where('warehouse_id', $user->warehouse_id);
        }
        return $query;
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }
}
