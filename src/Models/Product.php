<?php

namespace PortedCheese\Catalog\Models;

use App\Image;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use PortedCheese\Catalog\Events\ProductCategoryChange;
use PortedCheese\Catalog\Events\ProductListChange;
use PortedCheese\SeoIntegration\Models\Meta;

class Product extends Model
{
    const DEFAULT_SORT = "title";
    const DEFAULT_SORT_ORDER = "desc";

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

        static::deleting(function (\App\Product $model) {
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

            // Очистить кэш.
            $model->forgetFieldsCache();
            $model->forgetTeaserCache();

            // Очистить метки.
            $model->states()->detach();
        });

        static::deleted(function (\App\Product $model) {
            event(new ProductListChange($model));
        });

        static::updated(function (\App\Product $model) {
            // Очистить кэш.
            $model->forgetTeaserCache();

            $changes = $model->getChanges();
            if (! empty($changes['category_id'])) {
                $original = $model->getOriginal();
                if (! empty($original['category_id'])) {
                    $categoryId = $original['category_id'];
                }
                else {
                    $categoryId = false;
                }
                event(new ProductCategoryChange($model, $categoryId));
                // Если меняем категорию, то меняется список продуктов у двух категорий.
                event(new ProductListChange($model, $categoryId));
            }
        });

        static::created(function (\App\Product $model) {
            // Создать метатеги по умолчанию.
            $model->createDefaultMetas();

            event(new ProductListChange($model));
        });
    }

    /**
     * Сортировка товаров.
     *
     * @param Request $request
     * @param Builder $products
     */
    public static function addSortFromFilter(Request $request, Builder $products)
    {
        $defaultSort = true;
        $query = $request->query;
        if ($query->has("sort-by")) {
            $field = $query->get("sort-by");
            $order = "asc";
            if ($query->has("sort-order")) {
                $value = $query->get("sort-order");
                if (in_array($value, ['asc', 'desc'])) {
                    $order = $value;
                }
            }
            if (Schema::hasColumn("products", $field)) {
                $products->orderBy("products.{$field}", $order);
                $defaultSort = false;
            }
            elseif ($field == "price" && ! env("DISABLE_CATALOG_PRICE_SORT", false)) {
                $products->orderBy("product_price.price", $order);
                $defaultSort = false;
            }
        }
        if ($defaultSort) {
            $products->orderBy("products." . self::DEFAULT_SORT, self::DEFAULT_SORT_ORDER);
        }
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
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
                'content' => $this->short,
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
     * Получить тизер товара.
     *
     * @return string
     * @throws \Throwable
     */
    public function getTeaser()
    {
        $key = "product-getTeaser:{$this->id}";
        $cached = Cache::get($key);
        if (!empty($cached)) {
            return $cached;
        }
        $states = $this->states;
        $variation = \App\ProductVariation::query()
            ->select('price')
            ->where('product_id', $this->id)
            ->where('available', 1)
            ->orderBy('price')
            ->first();
        $view = view("catalog::site.products.teaser", [
            'product' => $this,
            'image' => $this->image,
            'hasStates' => $states->count(),
            'states' => $states,
            'variation' => $variation,
        ]);
        $html = $view->render();
        Cache::forever($key, $html);
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
        $key = "product-getFieldsInfo:{$this->id}";
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
        Cache::forget("product-getFieldsInfo:{$this->id}");
    }

    /**
     * Очистить кэш тизера.
     */
    public function forgetTeaserCache()
    {
        Cache::forget("product-getTeaser:{$this->id}");
    }
}
