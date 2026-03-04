<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PackingDetail extends Model
{
    protected $fillable = ['packing_id', 'product_id', 'quantity'];

    public function packing(): BelongsTo
    {
        return $this->belongsTo(Packing::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PackingItem::class, 'packing_detail_id');
    }
}
