<?php

declare(strict_types=1);

namespace App\Domain\Product\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'compare_at_price',
        'cost_per_item',
        'sku',
        'barcode',
        'stock',
        'quantity',
        'track_inventory',
        'continue_selling',
        'weight',
        'weight_unit',
        'image',
        'images',
        'status',
        'featured',
        'is_active',
        'is_featured',
        'category_id',
        'meta_title',
        'meta_description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'compare_at_price' => 'decimal:2',
        'cost_per_item' => 'decimal:2',
        'quantity' => 'integer',
        'stock' => 'integer',
        'track_inventory' => 'boolean',
        'continue_selling' => 'boolean',
        'weight' => 'decimal:2',
        'images' => 'array',
        'status' => 'string',
        'featured' => 'boolean',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    /**
     * Scope for active products.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orWhere('status', 'active');
    }

    /**
     * Scope for featured products.
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true)->orWhere('featured', true);
    }

    /**
     * Scope for in stock products.
     */
    public function scopeInStock(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->where('track_inventory', false)
              ->orWhere(function ($q2) {
                  $q2->where('track_inventory', true)
                     ->where('quantity', '>', 0);
              });
        });
    }

    /**
     * Check if product is in stock.
     */
    public function isInStock(): bool
    {
        if (!$this->track_inventory) {
            return true;
        }

        return $this->quantity > 0;
    }

    /**
     * Check if product is active.
     */
    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    /**
     * Get the discount percentage.
     */
    public function getDiscountPercentageAttribute(): ?float
    {
        if (!$this->compare_at_price || $this->compare_at_price <= $this->price) {
            return null;
        }

        return round((($this->compare_at_price - $this->price) / $this->compare_at_price) * 100, 2);
    }

    /**
     * Get the primary image.
     */
    public function getPrimaryImageAttribute(): ?string
    {
        return $this->images[0] ?? null;
    }
}
