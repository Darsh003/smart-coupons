<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $table    = 'inventory';
    public $timestamps  = false;
    public $updated_at  = null;

    const CREATED_AT = null;
    const UPDATED_AT = 'updated_at';

    protected $fillable = ['product_id', 'quantity', 'low_stock_alert', 'updated_at'];

    protected $dates = ['updated_at'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
