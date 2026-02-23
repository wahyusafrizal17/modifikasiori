<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wip extends Model
{
    protected $fillable = [
        'kode_wip',
        'product_id',
        'qty',
        'status',
        'tanggal_mulai',
        'tanggal_selesai',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public static function generateKode(): string
    {
        $today = now()->format('Ymd');
        $last = static::where('kode_wip', 'like', "WIP-{$today}-%")->orderByDesc('kode_wip')->first();
        $seq = $last ? ((int) substr($last->kode_wip, -4)) + 1 : 1;

        return "WIP-{$today}-" . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'proses' => '<span class="inline-flex items-center rounded-lg bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700">Proses</span>',
            'selesai' => '<span class="inline-flex items-center rounded-lg bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700">Selesai</span>',
            'dibatalkan' => '<span class="inline-flex items-center rounded-lg bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700">Dibatalkan</span>',
            default => '',
        };
    }
}
