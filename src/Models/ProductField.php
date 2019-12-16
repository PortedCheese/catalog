<?php

namespace PortedCheese\Catalog\Models;

use Illuminate\Database\Eloquent\Model;
use PortedCheese\Catalog\Events\ProductFieldUpdate;

class ProductField extends Model
{
    protected $fillable = [
        'field_id',
        'value',
        'product_id',
        'category_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function (\App\ProductField $model) {
            $model->runEvent();
        });

        static::updated(function (\App\ProductField $model) {
            $model->runEvent();
        });

        static::deleted(function (\App\ProductField $model) {
            $model->runEvent();
        });
    }

    /**
     * К какому полю относится значение.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function field()
    {
        return $this->belongsTo(\App\CategoryField::class, 'field_id');
    }

    /**
     * К какому товару относится.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(\App\Product::class);
    }

    /**
     * Категория к которой относится поле.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(\App\Category::class);
    }

    /**
     * Событие обновления характеристики.
     */
    private function runEvent()
    {
        $product = $this->product;
        if (! empty($product)) {
            event(new ProductFieldUpdate($product));
        }
    }
}
