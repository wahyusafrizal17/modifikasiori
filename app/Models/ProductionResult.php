<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionResult extends Model
{
    use HasFactory;

    protected $fillable = ['production_id', 'product_id', 'qty', 'qc_status', 'qc_notes'];

    public function production()
    {
        return $this->belongsTo(Production::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
