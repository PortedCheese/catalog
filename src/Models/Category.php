<?php

namespace PortedCheese\Catalog\Models;

use App\Image;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use PortedCheese\Catalog\Events\CategoryFieldUpdate;
use PortedCheese\SeoIntegration\Models\Meta;

class Category extends Model
{
    protected $fillable = [
        'title',
        'description',
        'slug',
        'parent_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {
            // Удаляем главное изображение.
            $model->clearMainImage();
            // Удаляем метатеги.
            $model->clearMetas();
            // Убираем поля.
            foreach ($model->fields as $field) {
                $model->fields()->detach($field);
                $field->checkCategoryOnDetach();
            }
            // Очистка кэша.
            $model->forgetFieldsCache();
        });

        static::created(function ($model) {
            // Создать метатеги по умолчанию.
            $model->createDefaultMetas();
            // Поля родителя.
            $model->setParentFields();
        });
    }

    /**
     * У категории много товаров.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
        return $this->hasMany(\App\Product::class);
    }

    /**
     * Дочернии категории.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(\App\Category::class, 'parent_id');
    }

    /**
     * Родительская категория.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(\App\Category::class, 'parent_id');
    }

    /**
     * Характеристики категории.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function fields()
    {
        return $this->belongsToMany(\App\CategoryField::class)
            ->withPivot('title')
            ->withPivot('filter')
            ->withTimestamps();
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
     * Категории в виде дерева.
     *
     * @return array
     */
    public static function getTree()
    {
        $tree = [];
        $categories = DB::table('categories')
            ->select(['id', 'title', 'slug', 'parent_id'])
            ->orderBy('parent_id')
            ->get();
        $noParent = [];
        foreach ($categories as $category) {
            $tree[$category->id] = (object) [
                'title' => $category->title,
                'slug' => $category->slug,
                'parent' => $category->parent_id,
                'children' => [],
            ];
            if (empty($category->parent_id)) {
                $noParent[] = $category->id;
            }
        }
        foreach ($tree as $id => $item) {
            if (empty($item->parent)) {
                continue;
            }
            $tree[$item->parent]->children[$id] = $item;
        }
        foreach ($noParent as $id) {
            self::removeChildren($tree, $id);
        }
        return $tree;
    }

    /**
     * Убираем подкатегории.
     *
     * @param $tree
     * @param $id
     */
    private static function removeChildren(&$tree, $id)
    {
        if (empty($tree[$id])) {
            return;
        }
        $item = $tree[$id];
        foreach ($item->children as $key => $child) {
            self::removeChildren($tree, $key);
            if (!empty($tree[$key])) {
                unset($tree[$key]);
            }
        }
    }

    /**
     * Создать метатеги по умолчанию.
     */
    public function createDefaultMetas()
    {
        $result = Meta::getModel('categories', $this->id, "title");
        if ($result['success'] && !empty($this->title)) {
            $meta = Meta::create([
                'name' => 'title',
                'content' => $this->title,
            ]);
            $meta->metable()->associate($this);
            $meta->save();
        }
        $result = Meta::getModel('categories', $this->id, "description");
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
     * Задать поля для дочерних категорий.
     */
    public function addChildFields()
    {
        foreach ($this->children as $child) {
            $child->setParentFields();
            $child->addChildFields();
            event(new CategoryFieldUpdate($child));
        }
    }

    /**
     * Скопировать поля у родителя.
     */
    public function setParentFields()
    {
        if (! $parent = $this->parent) {
            return;
        }
        $parentFileds = $parent->fields;
        if (! $parentFileds->count()) {
            return;
        }
        $ids = [];
        foreach ($this->fields as $field) {
            $ids[] = $field->id;
        }
        foreach ($parentFileds as $parentFiled) {
            $pivot = $parentFiled->pivot;
            if (empty($pivot)) {
                continue;
            }
            $data = [
                'title' => $pivot->title,
                'filter' => $pivot->filter,
            ];
            if (in_array($parentFiled->id, $ids)) {
                $parentFiled->categories()
                    ->updateExistingPivot($this, $data);
            }
            else {
                $parentFiled->categories()
                    ->attach($this, $data);
            }
        }
    }

    /**
     * Категории уровня родительской категории.
     *
     * @return array
     */
    public function getParents()
    {
        $id = $this->id;
        $collection = Category::where('parent_id', $id)
            ->orderBy('weight', 'desc')
            ->get();
        $parents = [];
        foreach ($collection as $item) {
            if ($item->id == $id) {
                continue;
            }
            $parents[$item->id] = $item->title;
        }
        return $parents;
    }

    /**
     * Информация о полях категории.
     *
     * @return mixed
     */
    public function getFieldsInfo()
    {
        $key = "category-fields-info:{$this->id}";
        $cached = Cache::get($key);
        if (!empty($cached)) {
            return $cached;
        }

        $fields = Cache::rememberForever($key, function () {
            $fields = [];
            foreach ($this->fields as $field) {
                $pivot = $field->pivot;
                $fields[$field->id] = (object) [
                    'id' => $field->id,
                    'title' => $pivot->title,
                    'filter' => $pivot->filter,
                    'type' => $field->type,
                    'machine' => $field->machine,
                ];
            }
            return $fields;
        });

        return $fields;
    }

    /**
     * Очистить кэш информации полей.
     */
    public function forgetFieldsCache()
    {
        Cache::forget("category-fields-info:{$this->id}");
    }

    /**
     * Хлебные крошки для админки.
     *
     * @return array
     */
    public function getAdminBreadcrumb($productPage = false)
    {
        $breadcrumb = [];
        // TODO: add cache.
        if (!empty($this->parent)) {
            $breadcrumb = $this->parent->getAdminBreadcrumb();
        }
        else {
            $breadcrumb[] = (object) [
                'title' => 'Категории',
                'url' => route('admin.category.index'),
                'active' => false,
            ];
        }
        $routeParams = Route::current()->parameters();
        $productPage = $productPage && !empty($routeParams['product']);
        $active = !empty($routeParams['category']) &&
            $routeParams['category']->id == $this->id &&
            !$productPage;
        $breadcrumb[] = (object) [
            'title' => $this->title,
            'url' => route('admin.category.show', ['category' => $this]),
            'active' => $active,
        ];
        if ($productPage) {
            $product = $routeParams['product'];
            $breadcrumb[] = (object) [
                'title' => $product->title,
                'url' => route('admin.category.product.show', ['category' => $this, 'product' => $product]),
                'active' => true,
            ];
        }
        return $breadcrumb;
    }
}
