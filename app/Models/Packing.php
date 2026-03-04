<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Packing extends Model
{
    protected $fillable = ['kode', 'tanggal', 'user_id', 'catatan'];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(PackingDetail::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PackingItem::class);
    }

    public static function generateKode(): string
    {
        $today = now()->format('Ymd');
        $last = static::where('kode', 'like', "PCK-{$today}-%")->latest('id')->first();
        $seq = $last ? (int) substr($last->kode, -4) + 1 : 1;
        return "PCK-{$today}-" . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}
