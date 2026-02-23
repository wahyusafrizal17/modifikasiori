<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_invoice', 'service_order_id', 'total_jasa', 'total_sparepart',
        'diskon', 'grand_total', 'metode_pembayaran', 'tanggal', 'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public static function generateKode(): string
    {
        $date = now()->format('Ymd');
        $last = static::where('kode_invoice', 'like', "INV-{$date}-%")->latest('id')->first();
        $seq = $last ? (int) substr($last->kode_invoice, -4) + 1 : 1;
        return sprintf('INV-%s-%04d', $date, $seq);
    }

    public function serviceOrder()
    {
        return $this->belongsTo(ServiceOrder::class);
    }
}
