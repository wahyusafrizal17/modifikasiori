<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = ['nama', 'no_hp', 'alamat', 'kota_id'];

    public function kota()
    {
        return $this->belongsTo(Kota::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function jasaServis()
    {
        return $this->hasMany(JasaServis::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }
}
