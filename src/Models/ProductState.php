<?php

namespace PortedCheese\Catalog\Models;

use Illuminate\Database\Eloquent\Model;
use PortedCheese\BaseSettings\Traits\HasSlug;

class ProductState extends Model
{
    use HasSlug;

    const COLORS = [
        'primary',
        'secondary',
        'danger',
        'success',
        'warning',
        'info',
        'dark',
    ];

    protected $fillable = [
        'title',
        'slug',
        'color',
    ];

    protected static function boot()
    {
        parent::boot();
        static::slugBoot();

        static::updated(function (\App\ProductState $model) {
            foreach ($model->products as $product) {
                $product->forgetTeaserCache();
            }
        });

        static::deleting(function (\App\ProductState $model) {
            // Убираем товары.
            foreach ($model->products as $product) {
                $model->products()->detach($product);
            }
        });
    }

    /**
     * У метки может быть много товаров.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function products()
    {
        return $this->belongsToMany(\App\Product::class)
            ->withTimestamps();
    }
}
