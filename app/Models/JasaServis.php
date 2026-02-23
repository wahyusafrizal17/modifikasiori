<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JasaServis extends Model
{
    use HasFactory;

    protected $table = 'jasa_servis';

    protected $fillable = ['nama', 'biaya', 'warehouse_id'];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function scopeForUser($query, $user = null)
    {
        $user ??= auth()->user();
        if ($user && !$user->isAdmin()) {
            $query->where('warehouse_id', $user->warehouse_id);
        }
        return $query;
    }
}
