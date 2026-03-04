<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Production extends Model
{
    protected $fillable = [
        'kode', 'tanggal', 'team_produksi_id', 'user_id', 'status',
        'reported_by', 'reported_at', 'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'reported_at' => 'datetime',
    ];

    public function teamProduksi(): BelongsTo
    {
        return $this->belongsTo(TeamProduksi::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function outputs(): HasMany
    {
        return $this->hasMany(ProductionOutput::class);
    }

    public function isProses(): bool
    {
        return $this->status === 'proses';
    }

    public function isSelesai(): bool
    {
        return $this->status === 'selesai';
    }

    public static function generateKode(): string
    {
        $today = now()->format('Ymd');
        $last = static::where('kode', 'like', "PRD-{$today}-%")->latest('id')->first();
        $seq = $last ? (int) substr($last->kode, -4) + 1 : 1;
        return "PRD-{$today}-" . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}
