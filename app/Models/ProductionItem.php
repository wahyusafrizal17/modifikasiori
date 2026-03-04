<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionItem extends Model
{
    protected $fillable = ['production_output_id', 'bahan_baku_id', 'jumlah'];

    public function productionOutput(): BelongsTo
    {
        return $this->belongsTo(ProductionOutput::class);
    }

    public function bahanBaku(): BelongsTo
    {
        return $this->belongsTo(BahanBaku::class);
    }
}
