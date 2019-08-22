<?php

namespace PortedCheese\Catalog\Models;

use Illuminate\Database\Eloquent\Model;
use PortedCheese\Catalog\Events\ProductVariationUpdate;

class ProductVariation extends Model
{
    protected $fillable = [
        'product_id',
        'sku',
        'price',
        'sale_price',
        'description',
        'sale',
        'available',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            event(new ProductVariationUpdate($model));
        });

        static::updated(function ($model) {
            event(new ProductVariationUpdate($model));
        });

        static::deleted(function ($model) {
            event(new ProductVariationUpdate($model));
        });
    }

    /**
     * Относится к товару.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(\App\Product::class);
    }
}
