<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WarehouseTransaksiItem extends Model
{
    protected $table = 'warehouse_transaksi_items';

    protected $fillable = ['warehouse_transaksi_id', 'supplier_id', 'product_id', 'harga_pembelian', 'qty'];

    public function warehouseTransaksi(): BelongsTo
    {
        return $this->belongsTo(WarehouseTransaksi::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
