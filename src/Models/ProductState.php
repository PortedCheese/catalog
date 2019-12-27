<?php

namespace PortedCheese\Catalog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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

        static::creating(function (\App\ProductState $model) {
            $model->fixSlug();
        });

        static::updating(function (\App\ProductState $model) {
            $model->fixSlug(true);
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

    /**
     * Поправить slug.
     *
     * @param bool $updating
     */
    public function fixSlug($updating = false)
    {
        if ($updating && ($this->original["slug"] == $this->slug)) {
            return;
        }
        if (empty($this->slug)) {
            $slug = $this->title;
        }
        else {
            $slug = $this->slug;
        }
        $slug = Str::slug($slug);
        $buf = $slug;
        $i = 1;
        if ($updating) {
            $id = $this->id;
        }
        else {
            $id = 0;
        }
        while (self::query()
            ->select("id")
            ->where("slug", $buf)
            ->where("id", "!=", $id)
            ->count())
        {
            $buf = $slug . "-" . $i++;
        }
        $this->slug = $buf;
    }
}
