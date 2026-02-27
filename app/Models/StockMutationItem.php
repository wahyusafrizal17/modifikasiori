<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMutationItem extends Model
{
    use HasFactory;

    protected $fillable = ['stock_mutation_id', 'product_id', 'qty'];

    public function stockMutation()
    {
        return $this->belongsTo(StockMutation::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
