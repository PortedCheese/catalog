<?php

namespace PortedCheese\Catalog\Models;

use Illuminate\Database\Eloquent\Model;
use PortedCheese\BaseSettings\Traits\HasSlug;
use PortedCheese\BaseSettings\Traits\ShouldSlug;

class ProductState extends Model
{
    use ShouldSlug;

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

    protected static function booting()
    {
        parent::booting();

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
