<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMutation extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_mutasi', 'from_warehouse_id', 'to_warehouse_id',
        'tanggal', 'status', 'catatan', 'user_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function fromWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id');
    }

    public function toWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(StockMutationItem::class);
    }

    public static function generateCode(): string
    {
        $prefix = 'MUT-' . now()->format('Ymd') . '-';
        $last = static::where('kode_mutasi', 'like', $prefix . '%')
            ->orderByDesc('kode_mutasi')
            ->value('kode_mutasi');
        $seq = $last ? (int) substr($last, -4) + 1 : 1;
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'draft' => '<span class="inline-flex items-center rounded-lg bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-700">Draft</span>',
            'in_transit' => '<span class="inline-flex items-center rounded-lg bg-yellow-100 px-2.5 py-1 text-xs font-semibold text-yellow-700">Dalam Pengiriman</span>',
            'received' => '<span class="inline-flex items-center rounded-lg bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700">Diterima</span>',
            default => '',
        };
    }
}
