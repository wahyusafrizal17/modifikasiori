<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionMaterial extends Model
{
    use HasFactory;

    protected $fillable = ['production_id', 'product_id', 'qty'];

    public function production()
    {
        return $this->belongsTo(Production::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
