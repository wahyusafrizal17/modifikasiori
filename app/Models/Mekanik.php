<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mekanik extends Model
{
    use HasFactory;

    protected $fillable = ['nama', 'no_hp', 'spesialisasi', 'status'];

    public function serviceOrders()
    {
        return $this->hasMany(ServiceOrder::class);
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }
}
