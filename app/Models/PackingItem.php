<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackingItem extends Model
{
    protected $fillable = ['packing_id', 'packing_detail_id', 'bahan_siap_produksi_id', 'type', 'kemasan_id', 'quantity'];

    public function packing(): BelongsTo
    {
        return $this->belongsTo(Packing::class);
    }

    public function packingDetail(): BelongsTo
    {
        return $this->belongsTo(PackingDetail::class);
    }

    public function bahanSiapProduksi(): BelongsTo
    {
        return $this->belongsTo(BahanSiapProduksi::class);
    }

    public function kemasan(): BelongsTo
    {
        return $this->belongsTo(Kemasan::class);
    }
}
