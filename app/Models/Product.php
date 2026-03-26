<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'base_price',
        'image',
        'is_active',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // ─── Relationships ────────────────────────────────────────
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function inventory()
    {
        return $this->hasOne(Inventory::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // ─── Scopes ───────────────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock($query)
    {
        return $query->whereHas('inventory', fn($q) => $q->where('quantity', '>', 0));
    }

    // ─── Computed Helpers ─────────────────────────────────────
    public function isInStock(): bool
    {
        return ($this->inventory?->quantity ?? 0) > 0;
    }

    public function getStockCount(): int
    {
        return $this->inventory?->quantity ?? 0;
    }

    /**
     * +10% weekend surge price (Sat/Sun)
     */
    public function getEffectivePrice(): float
    {
        return $this->isWeekendSurgeActive()
            ? round((float) $this->base_price * 1.10, 2)
            : (float) $this->base_price;
    }

    public function isWeekendSurgeActive(): bool
    {
        return in_array(now()->dayOfWeek, [0, 6]);
    }
}
