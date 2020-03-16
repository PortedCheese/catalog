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

        static::creating(function (\App\ProductVariation $model) {
            $model->fixSku();
        });

        static::created(function ($model) {
            event(new ProductVariationUpdate($model));
        });

        static::updating(function (\App\ProductVariation $model) {
            $model->fixSku(true);
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

    /**
     * Получить вариации для товара на вывод.
     *
     * @param $productId
     * @return array
     */
    public static function getByProductIdForRender($productId)
    {
        $collection = \App\ProductVariation::query()
            ->where('product_id', $productId)
            ->orderByDesc('available')
            ->orderBy('price')
            ->get();
        $variations = [];
        foreach ($collection as $item) {
            $array = $item->toArray();
            $array['human_price'] = $item->human_price;
            $array['human_sale_price'] = $item->human_sale_price;
            $variations[] = $array;
        }
        return $variations;
    }

    /**
     * Поправить sku.
     *
     * @param bool $updating
     */
    public function fixSku($updating = false)
    {
        if ($updating && ($this->original["sku"] == $this->sku)) {
            return;
        }
        if (empty($this->sku)) {
            $product = $this->product;
            $category = $product->category;
            $sku = "{$category->slug}#{$product->slug}";
        }
        else {
            $sku = $this->sku;
        }
        $sku = str_replace(" ", "#", $sku);
        $buf = $sku;
        $i = 1;
        if ($updating) {
            $id = $this->id;
        }
        else {
            $id = 0;
        }
        while (self::query()
            ->select("id")
            ->where("sku", $buf)
            ->where("id", "!=", $id)
            ->count())
        {
            $buf = $sku . "-" . $i++;
        }
        $this->sku = $buf;
    }
}
