<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiOnline extends Model
{
    protected $table = 'transaksi_online';

    protected $fillable = [
        'no_resi',
        'user_id',
        'warehouse_id',
    ];

    public function items()
    {
        return $this->hasMany(TransaksiOnlineItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
