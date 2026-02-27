<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Production extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_produksi', 'warehouse_id', 'tanggal',
        'status', 'catatan', 'user_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function materials()
    {
        return $this->hasMany(ProductionMaterial::class);
    }

    public function results()
    {
        return $this->hasMany(ProductionResult::class);
    }

    public static function generateCode(): string
    {
        $prefix = 'PRD-' . now()->format('Ymd') . '-';
        $last = static::where('kode_produksi', 'like', $prefix . '%')
            ->orderByDesc('kode_produksi')
            ->value('kode_produksi');
        $seq = $last ? (int) substr($last, -4) + 1 : 1;
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'proses' => '<span class="inline-flex items-center rounded-lg bg-blue-100 px-2.5 py-1 text-xs font-semibold text-blue-700">Proses</span>',
            'qc' => '<span class="inline-flex items-center rounded-lg bg-yellow-100 px-2.5 py-1 text-xs font-semibold text-yellow-700">QC</span>',
            'selesai' => '<span class="inline-flex items-center rounded-lg bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700">Selesai</span>',
            'gagal' => '<span class="inline-flex items-center rounded-lg bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700">Gagal</span>',
            default => '',
        };
    }
}
