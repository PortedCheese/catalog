<?php

namespace PortedCheese\Catalog\Models;

use Illuminate\Database\Eloquent\Model;
use PortedCheese\Catalog\Http\Requests\ProductStateUpdateRequest;

class ProductState extends Model
{

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

        static::deleting(function ($model) {
            // Убираем товары.
            foreach ($model->products as $product) {
                $model->products()->detach($product);
            }
        });
    }

    /**
     * Валидация создания тега товара.
     *
     * @return array
     */
    public static function requestProductStateStoreRules()
    {
        return [
            'title' => 'required|min:2|unique:product_states,title',
            'slug' => 'nullable|min:2|unique:product_states,slug',
            'color' => 'required',
        ];
    }

    /**
     * Названия полей в валидации создания тега.
     *
     * @return array
     */
    public static function requestProductStateStoreAttributes()
    {
        return [
            'title' => 'Заголовок',
            'color' => 'Цвет',
        ];
    }

    /**
     * Валидация обновления тега.
     *
     * @param ProductStateUpdateRequest $validator
     * @return array
     */
    public static function requestProductStateUpdateRules(ProductStateUpdateRequest $validator)
    {
        $state = $validator->route()->parameter('state', NULL);
        $id = !empty($state) ? $state->id : NULL;
        return [
            'title' => "required|min:2|unique:product_states,title,{$id}",
            'slug' => "nullable|min:2|unique:product_states,slug,{$id}",
            'color' => 'required',
        ];
    }

    /**
     * Названия полей в валидации обновления тега.
     *
     * @return array
     */
    public static function requestProductStateUpdateAttributes()
    {
        return [
            'title' => 'Заголовок',
            'color' => 'Цвет',
        ];
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
