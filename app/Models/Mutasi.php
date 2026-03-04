<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mutasi extends Model
{
    protected $table = 'mutasis';

    protected $fillable = [
        'kode', 'nomor_surat_jalan', 'tanggal', 'user_id', 'warehouse_id', 'sumber', 'status', 'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
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
        return $this->hasMany(MutasiItem::class);
    }

    public function isDikirim(): bool
    {
        return $this->status === 'dikirim';
    }

    public function isDiterima(): bool
    {
        return $this->status === 'diterima';
    }

    public function isFromProduksi(): bool
    {
        return ($this->sumber ?? 'produksi') === 'produksi';
    }

    public function isFromWarehouse(): bool
    {
        return ($this->sumber ?? 'produksi') === 'warehouse';
    }

    public static function generateKode(): string
    {
        $today = now()->format('Ymd');
        $last = static::where('kode', 'like', "MTS-{$today}-%")->latest('id')->first();
        $seq = $last ? (int) substr($last->kode, -4) + 1 : 1;
        return "MTS-{$today}-" . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}
