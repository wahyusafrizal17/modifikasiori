<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MutasiItem extends Model
{
    protected $fillable = ['mutasi_id', 'product_id', 'quantity'];

    public function mutasi(): BelongsTo
    {
        return $this->belongsTo(Mutasi::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
