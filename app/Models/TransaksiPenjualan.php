<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransaksiPenjualan extends Model
{
    protected $table = 'transaksi_penjualan';

    protected $fillable = [
        'no_transaksi',
        'nama_pembeli',
        'no_hp',
        'jenis_pembayaran',
        'user_id',
        'warehouse_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(TransaksiPenjualanItem::class);
    }

    public static function generateNoTransaksi(): string
    {
        $prefix = 'TP-' . now()->format('Ymd');
        $last = static::where('no_transaksi', 'like', $prefix . '%')->orderBy('no_transaksi', 'desc')->first();
        $seq = $last ? (int) substr($last->no_transaksi, -4) + 1 : 1;
        return $prefix . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}
