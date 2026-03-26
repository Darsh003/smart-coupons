<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class CartItem extends Model
{
    protected $fillable = [
        'user_id', 'session_id', 'product_id',
        'quantity', 'unit_price', 'base_price', 'is_weekend_price',
    ];

    protected $casts = [
        'unit_price'      => 'decimal:2',
        'base_price'      => 'decimal:2',
        'is_weekend_price'=> 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class)->with(['inventory', 'category']);
    }

    // ─── Scope: filter by current user or session ───────────────
    public function scopeForCurrentUser(Builder $query): Builder
    {
        if (auth()->check()) {
            return $query->where('user_id', auth()->id());
        }
        return $query->where('session_id', session()->getId());
    }

    // ─── Computed ────────────────────────────────────────────────
    public function getLineTotalAttribute(): float
    {
        return round((float) $this->unit_price * $this->quantity, 2);
    }
}
