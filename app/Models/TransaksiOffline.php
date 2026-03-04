<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransaksiOffline extends Model
{
    protected $table = 'transaksi_offline';

    protected $fillable = [
        'no_transaksi',
        'tujuan',
        'nama_toko',
        'alamat',
        'no_hp',
        'petugas_id',
        'jenis_pembayaran',
        'user_id',
        'warehouse_id',
    ];

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

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
        return $this->hasMany(TransaksiOfflineItem::class);
    }

    public static function generateNoTransaksi(): string
    {
        $prefix = 'TO-' . now()->format('Ymd');
        $last = static::where('no_transaksi', 'like', $prefix . '%')->orderBy('no_transaksi', 'desc')->first();
        $seq = $last ? (int) substr($last->no_transaksi, -4) + 1 : 1;
        return $prefix . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}
