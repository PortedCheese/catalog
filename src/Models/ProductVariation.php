<?php

namespace PortedCheese\Catalog\Models;

use Illuminate\Database\Eloquent\Model;
use PortedCheese\Catalog\Events\ProductVariationUpdate;
use PortedCheese\Catalog\Http\Requests\ProductVariationUpdateRequest;

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
     * Валидация создания вариации.
     *
     * @return array
     */
    public static function requestProductVariationStoreRules()
    {
        return [
            'sku' => 'required|min:2|unique:product_variations,sku',
            'product_id' => 'required|exists:products,id',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'description' => 'required|min:2',
        ];
    }

    /**
     * Названия полей для валидации добавления вариации.
     *
     * @return array
     */
    public static function requestProductVariationStoreAttributes()
    {
        return [
            'sku' => 'Артикул',
            'product_id' => "Товар",
            'price' => 'Цена',
            'sale_price' => 'Цена со скидкой',
            'description' => 'Описание',
        ];
    }

    /**
     * Валидация обновления вариации.
     *
     * @param ProductVariationUpdateRequest $validator
     * @return array
     */
    public static function requestProductVariationUpdateRules(ProductVariationUpdateRequest $validator)
    {
        $variation = $validator->route()->parameter('variation', NULL);
        $id = !empty($variation) ? $variation->id : NULL;
        return [
            'sku' => "required|min:2|unique:product_variations,sku,{$id}",
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'description' => 'required|min:2',
        ];
    }

    /**
     * Названия полей для обновления вариации.
     *
     * @return array
     */
    public static function requestProductVariationUpdateAttributes()
    {
        return [
            'sku' => 'Артикул',
            'price' => 'Цена',
            'sale_price' => 'Цена со скидкой',
            'description' => 'Описание',
        ];
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

    /**
     * Формат цены.
     *
     * @return string
     */
    public function getHumanPriceAttribute()
    {
        return number_format($this->price, 0, ",", " ");
    }

    /**
     * Формат цены.
     *
     * @return string
     */
    public function getHumanSalePriceAttribute()
    {
        return number_format($this->sale_price, 0, ",", " ");
    }
}
