<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductionOutput extends Model
{
    protected $fillable = [
        'production_id', 'bahan_siap_produksi_id', 'jumlah_target',
        'jumlah_selesai', 'jumlah_gagal', 'alasan_gagal',
    ];

    public function production(): BelongsTo
    {
        return $this->belongsTo(Production::class);
    }

    public function bahanSiapProduksi(): BelongsTo
    {
        return $this->belongsTo(BahanSiapProduksi::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ProductionItem::class);
    }
}
