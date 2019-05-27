<?php

namespace PortedCheese\Catalog\Models;

use App\Image;
use function foo\func;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use PortedCheese\SeoIntegration\Models\Meta;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'short',
        'description',
        'published',
        'state',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {
            // Удаляем главное изображение.
            $model->clearMainImage();
            // Удаляем метатеги.
            $model->clearMetas();
            // Чистим галлерею.
            $model->clearImages();
            // Очистить вариации.
            $model->clearVariations();
            // Очистить значения полей.
            $model->clearFields();
            // Очистить кэш значений полей.
            $model->forgetFieldsCache();
            // Очистить метки.
            $model->states()->detach();
        });

        static::created(function ($model) {
            // Создать метатеги по умолчанию.
            $model->createDefaultMetas();
        });
    }

    /**
     * Может находится во многих позициях заказа.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderItems()
    {
        return $this->hasMany(\App\OrderItem::class);
    }

    /**
     * У товара может быть несколько меток.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function states()
    {
        return $this->belongsToMany(\App\ProductState::class)
            ->withTimestamps();
    }

    /**
     * Значения полей.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function fields()
    {
        return $this->hasMany(\App\ProductField::class);
    }

    /**
     * Может быть несколько вариаций.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function variations()
    {
        return $this->hasMany(\App\ProductVariation::class);
    }

    /**
     * Товар относится к категории.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(\App\Category::class);
    }

    /**
     * Может быть изображение.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function image()
    {
        return $this->belongsTo(Image::class, 'main_image');
    }

    /**
     * Галлерея.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function images() {
        return $this->morphMany(Image::class, 'imageable');
    }

    /**
     * Метатеги.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function metas() {
        return $this->morphMany(Meta::class, 'metable');
    }

    /**
     * Подгружать по slug.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Создать метатеги по умолчанию.
     */
    public function createDefaultMetas()
    {
        $result = Meta::getModel('products', $this->id, "title");
        if ($result['success'] && !empty($this->title)) {
            $meta = Meta::create([
                'name' => 'title',
                'content' => $this->title,
            ]);
            $meta->metable()->associate($this);
            $meta->save();
        }
        $result = Meta::getModel('products', $this->id, "description");
        if ($result['success'] && !empty($this->short)) {
            $meta = Meta::create([
                'name' => 'description',
                'content' => $this->description,
            ]);
            $meta->metable()->associate($this);
            $meta->save();
        }
    }

    /**
     * Удаляем созданные теги.
     */
    public function clearMetas()
    {
        foreach ($this->metas as $meta) {
            $meta->delete();
        }
    }

    /**
     * Изменить/создать главное изображение.
     *
     * @param $request
     */
    public function uploadMainImage($request)
    {
        if ($request->hasFile('main_image')) {
            $this->clearMainImage();
            $path = $request->file('main_image')->store('categories');
            $image = Image::create([
                'path' => $path,
                'name' => 'categories-' . $this->id,
            ]);
            $this->image()->associate($image);
            $this->save();
        }
    }

    /**
     * Удалить изображение.
     */
    public function clearMainImage()
    {
        $image = $this->image;
        if (!empty($image)) {
            $image->delete();
        }
        $this->image()->dissociate();
        $this->save();
    }

    /**
     * Удалить все изображения.
     */
    public function clearImages()
    {
        foreach ($this->images as $image) {
            $image->delete();
        }
    }

    /**
     * Удалить все вариации.
     */
    public function clearVariations()
    {
        foreach ($this->variations as $variation) {
            $variation->delete();
        }
    }

    /**
     * Удалить значения полей.
     */
    public function clearFields()
    {
        foreach ($this->fields as $field) {
            $field->delete();
        }
    }

    /**
     * Получить тизер категории.
     *
     * @return string
     * @throws \Throwable
     */
    public function getTeaser()
    {
        $cached = Cache::get("product-teaser:{$this->id}");
        if (!empty($cached)) {
            return $cached;
        }
        $view = view("catalog::site.products.teaser", ['product' => $this]);
        $html = $view->render();
//        Cache::forever("category-teaser:{$this->id}", $html);
        return $html;
    }

    /**
     * Информация по заполненным полям.
     *
     * @param null $category
     * @return array
     */
    public function getFieldsInfo($category = null)
    {
        $key = "product-fields:{$this->id}";
        $productFieldsInfo = Cache::get($key);
        if (empty($productFieldsInfo)) {
            $productFieldsInfo =  Cache::rememberForever($key, function () {

                $fieldsInfo = [];
                foreach ($this->fields as $field) {
                    $fieldId = $field->field_id;
                    if (empty($fieldsInfo[$fieldId])) {
                        $fieldsInfo[$fieldId] = (object) [
                            'values' => [],
                            'id' => $field->id,
                            'title' => '',
                        ];
                    }
                    $fieldsInfo[$fieldId]->values[] = $field->value;
                }

                return $fieldsInfo;
            });
        }

        if (empty($category)) {
            $category = $this->category;
        }

        $categoryFieldsInfo = $category->getFieldsInfo();

        foreach ($productFieldsInfo as $id => &$item) {
            if (empty($categoryFieldsInfo[$id])) {
                unset($productFieldsInfo[$id]);
            }
            $item->title = $categoryFieldsInfo[$id]->title;
        }

        return $productFieldsInfo;
    }

    /**
     * Очистить кэш информации полей.
     */
    public function forgetFieldsCache()
    {
        Cache::forget("product-fields:{$this->id}");
    }
}
