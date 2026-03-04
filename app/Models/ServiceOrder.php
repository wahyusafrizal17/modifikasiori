<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'warehouse_id', 'kode_servis', 'pelanggan_id', 'kendaraan_id', 'mekanik_id',
        'keluhan', 'sumber_kedatangan', 'kategori_service', 'estimasi_biaya', 'status',
        'tanggal_masuk', 'tanggal_selesai', 'next_service_date',
    ];

    public const SUMBER_KEDATANGAN = [
        'relasi_teman' => 'Relasi/Teman',
        'whatsapp' => 'Whatsapp',
        'facebook' => 'Facebook',
        'ig' => 'IG',
        'tiktok' => 'Tiktok',
        'marketplace' => 'Marketplace',
        'datang_langsung' => 'Datang Langsung',
        'dll' => 'DLL',
    ];

    public const KATEGORI_SERVICE = [
        'reguler' => 'Reguler',
        'booking' => 'Booking',
        'express' => 'Express',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'tanggal_selesai' => 'date',
        'next_service_date' => 'date',
    ];

    public static function generateKode(): string
    {
        $date = now()->format('Ymd');
        $last = static::where('kode_servis', 'like', "SRV-{$date}-%")->latest('id')->first();
        $seq = $last ? (int) substr($last->kode_servis, -4) + 1 : 1;
        return sprintf('SRV-%s-%04d', $date, $seq);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class);
    }

    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class);
    }

    public function mekanik()
    {
        return $this->belongsTo(Mekanik::class);
    }

    public function jasaServis()
    {
        return $this->belongsToMany(JasaServis::class, 'service_order_jasa')
            ->withPivot('biaya')->withTimestamps();
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'service_order_products')
            ->withPivot('qty', 'harga')->withTimestamps();
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function getTotalJasaAttribute(): int
    {
        return $this->jasaServis->sum('pivot.biaya');
    }

    public function getTotalSparepartAttribute(): int
    {
        return $this->products->sum(fn ($p) => $p->pivot->qty * $p->pivot->harga);
    }

    public function getGrandTotalAttribute(): int
    {
        return $this->total_jasa + $this->total_sparepart;
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'antri' => 'bg-yellow-100 text-yellow-700',
            'proses' => 'bg-blue-100 text-blue-700',
            'selesai' => 'bg-green-100 text-green-700',
            'dibatalkan' => 'bg-red-100 text-red-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }
}
