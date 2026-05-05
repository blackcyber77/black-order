<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Schema;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'image',
        'is_available',
        'is_promo',
        'is_best_seller',
        'promo_type',
        'promo_title',
        'promo_original_price',
        'promo_sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_available' => 'boolean',
        'is_promo' => 'boolean',
        'is_best_seller' => 'boolean',
        'promo_original_price' => 'decimal:2',
        'promo_sort_order' => 'integer',
    ];



    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopePromoFirst($query)
    {
        static $promoColumnsAvailable = null;
        if ($promoColumnsAvailable === null) {
            $promoColumnsAvailable = Schema::hasColumns($this->getTable(), [
                'is_promo',
                'promo_sort_order',
                'is_best_seller',
            ]);
        }

        if (!$promoColumnsAvailable) {
            return $query->latest('id');
        }

        return $query
            ->orderByDesc('is_promo')
            ->orderByRaw('CASE WHEN is_promo = 1 THEN COALESCE(promo_sort_order, 9999) ELSE 9999 END ASC')
            ->orderByDesc('is_best_seller')
            ->latest('id');
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    public function getFormattedPromoOriginalPriceAttribute(): string
    {
        if (!$this->promo_original_price) {
            return '';
        }

        return 'Rp ' . number_format((float) $this->promo_original_price, 0, ',', '.');
    }
}
