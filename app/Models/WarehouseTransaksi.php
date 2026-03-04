<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WarehouseTransaksi extends Model
{
    protected $table = 'warehouse_transaksis';

    protected $fillable = [
        'kode', 'user_id', 'warehouse_id', 'status',
        'approved_by', 'approved_at', 'rejected_reason', 'catatan',
    ];

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(WarehouseTransaksiItem::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public static function generateKode(): string
    {
        $prefix = 'WHT-' . now()->format('Ymd');
        $last = static::where('kode', 'like', $prefix . '%')->orderBy('kode', 'desc')->first();
        $seq = $last ? (int) substr($last->kode, -4) + 1 : 1;
        return $prefix . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}
