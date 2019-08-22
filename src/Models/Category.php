<?php

namespace PortedCheese\Catalog\Models;

use App\Image;
use App\ProductField;
use App\Product;
use App\ProductVariation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use PortedCheese\Catalog\Events\CategoryFieldUpdate;
use PortedCheese\Catalog\Jobs\CategoryCache;
use PortedCheese\SeoIntegration\Models\Meta;

class Category extends Model
{
    const PAGE_NAME = "Каталог";
    const PAGE_ROUTE = 'site.catalog.index';

    protected $fillable = [
        'title',
        'description',
        'slug',
        'parent_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function (self $model) {
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
            $model->forgetTeaserCache();
            $model->forgetChildrenListCache();
        });

        static::created(function (self $model) {
            // Создать метатеги по умолчанию.
            $model->createDefaultMetas();
            // Поля родителя.
            $model->setParentFields();
            // Очистка кэша.
            $model->forgetChildrenListCache();
        });

        static::updated(function (self $model) {
            // Очистка кэша.
            $model->forgetTeaserCache();
            $model->forgetBreadcrumbCache();
            $model->forgetChildrenListCache();
        });
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
        $collection = self::where('parent_id', $id)
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
     * Список дочерних категорий.
     *
     * @param bool $includeSelf
     * @return array|mixed
     */
    public function getChildren($includeSelf = false)
    {
        $key = "category-getChildren:{$this->id}";
        $children = Cache::rememberForever($key, function () {
            $children = [];
            foreach (self::where("parent_id", $this->id)->get() as $category) {
                $children[] = $category->id;
                $categories = $category->getChildren();
                if (! empty($categories)) {
                    foreach ($categories as $item) {
                        $children[] = $item;
                    }
                }
            }
            return $children;
        });
        if ($includeSelf) {
            $children[] = $this->id;
        }
        return $children;
    }

    /**
     * Информация о полях категории.
     *
     * @return mixed
     */
    public function getFieldsInfo($filter = false)
    {
        $key = "category-getFieldsInfo:{$this->id}";
        $cached = Cache::get($key);
        if (!empty($cached)) {
            if ($filter) {
                return $this->getOnlyFilter($cached);
            }
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

        if ($filter) {
            return $this->getOnlyFilter($fields);
        }
        return $fields;
    }

    /**
     * Получить все поля для фильтра.
     *
     * @return mixed
     */
    public function getChildrenFieldsFilterInfo()
    {
        $key = "category-getChildrenFieldsFilterInfo:{$this->id}";
        return Cache::rememberForever($key, function () {
            $fields = $this->getFieldsInfo(true);
            $ids = [];
            foreach ($fields as $field) {
                $ids[] = $field->id;
            }
            foreach ($this->children as $child) {
                foreach ($child->getChildrenFieldsFilterInfo() as $field) {
                    if (! in_array($field->id, $ids)) {
                        $fields[] = $field;
                        $ids[] = $field->id;
                    }
                }
            }
            return $fields;
        });
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

    /**
     * Получить тизер категории.
     *
     * @return string
     * @throws \Throwable
     */
    public function getTeaser()
    {
        $key = "category-getTeaser:{$this->id}";
        $cached = Cache::get($key);
        if (!empty($cached)) {
            return $cached;
        }
        $view = view("catalog::site.categories.teaser", ['category' => $this]);
        $html = $view->render();
        Cache::forever($key, $html);
        return $html;
    }

    /**
     * Хлебные крошки для сайта.
     *
     * @param $productPage
     * @param $parent
     * @return array
     */
    public function getSiteBreadcrumb($productPage = false, $parent = false)
    {
        $key = "category-getSiteBreadcrumb:{$this->id}";

        $breadcrumb = Cache::rememberForever($key, function () {
            $breadcrumb = [];

            if (!empty($this->parent)) {
                $breadcrumb = $this->parent->getSiteBreadcrumb(false, true);
            }
            else {
                $breadcrumb[] = (object) [
                    'title' => self::PAGE_NAME,
                    'url' => route(self::PAGE_ROUTE),
                    'active' => false,
                ];
            }

            $breadcrumb[] = (object) [
                'title' => $this->title,
                'url' => route('site.catalog.category.show', ['category' => $this]),
                'active' => false,
            ];

            return $breadcrumb;
        });

        if ($productPage) {
            $routeParams = Route::current()->parameters();
            $product = $routeParams['product'];
            $breadcrumb[] = (object) [
                'title' => $product->title,
                'url' => route('admin.category.product.show', ['category' => $this, 'product' => $product]),
                'active' => true,
            ];
        }
        elseif (! $parent) {
            $length = count($breadcrumb);
            $breadcrumb[$length - 1]->active = true;
        }
        return $breadcrumb;
    }

    /**
     * Получаем фильтры для категории.
     *
     * @param bool $includeSubs
     * @return mixed
     */
    public function getFilters($includeSubs = false)
    {
        if ($includeSubs) {
            $fieldsInfo = $this->getChildrenFieldsFilterInfo();
        }
        else {
            $fieldsInfo = $this->getFieldsInfo(true);
        }

        $fieldValues = $this->getProductValues($includeSubs);

        // Обход полученных значений и распределение по полям.
        $this->setProductValuesToFilter($fieldsInfo, $fieldValues);

        $this->addPriceFilter($fieldsInfo, $includeSubs);

        return $fieldsInfo;
    }

    /**
     * Очистить кэш информации полей.
     */
    public function forgetFieldsCache()
    {
        Cache::forget("category-getFieldsInfo:{$this->id}");
        $this->addCacheJob("getFieldsInfo");
    }

    /**
     * Очистить кэш информации полей для фильтров.
     */
    public function forgetChildrenFieldsCache()
    {
        Cache::forget("category-getChildrenFieldsFilterInfo:{$this->id}");
        $parent = $this->parent;
        if (! empty($parent)) {
            $parent->forgetChildrenFieldsCache();
        }
        else {
            $this->addCacheJob("getChildrenFieldsFilterInfo");
        }
    }

    /**
     * Очистить кэш тизера.
     */
    public function forgetTeaserCache()
    {
        Cache::forget("category-getTeaser:{$this->id}");
        $this->addCacheJob("getTeaser");
    }

    /**
     * Очистить кэш хлебных крошек.
     */
    public function forgetBreadcrumbCache()
    {
        Cache::forget("category-getSiteBreadcrumb:{$this->id}");
        $this->addCacheJob("getSiteBreadcrumb");
    }

    /**
     * Очистить кэш дочерних категорий.
     */
    public function forgetChildrenListCache()
    {
        Cache::forget("category-getChildren:{$this->id}");
        $parent = $this->parent;
        if (! empty($parent)) {
            $parent->forgetChildrenListCache();
        }
        else {
            $this->addCacheJob("getChildren");
        }
    }

    /**
     * Очистить кэш значений товаров для фильтра.
     */
    public function forgetProductValuesCache()
    {
        $key = "category-getProductValues:{$this->id}";
        Cache::forget("$key-1");
        Cache::forget("$key-0");
        $parent = $this->parent;
        if (! empty($parent)) {
            $parent->forgetProductValuesCache();
        }
        else {
            $this->addCacheJob("getProductValues");
        }
    }

    /**
     * Очистить кэш продуктов для фильтра.
     */
    public function forgetFilterPIdsCache()
    {
        $key = "category-getPIds:{$this->id}";
        Cache::forget("$key-1");
        Cache::forget("$key-0");
        $parent = $this->parent;
        if (! empty($parent)) {
            $parent->forgetFilterPIdsCache();
        }
        else {
            $this->addCacheJob("getPIds");
        }
    }

    /**
     * Очистить цены товаров.
     */
    public function forgetFilterVariationsCache()
    {
        $key = "category-addPriceFilter:{$this->id}";
        Cache::forget("$key-1");
        Cache::forget("$key-0");
        $parent = $this->parent;
        if (! empty($parent)) {
            $parent->forgetFilterVariationsCache();
        }
        else {
            $this->addCacheJob("addPriceFilter");
        }
    }

    /**
     * Добавить фильтр по цене.
     *
     * @param $fieldsInfo
     * @param $includeSubs
     */
    private function addPriceFilter(&$fieldsInfo, $includeSubs)
    {
        $key = "category-addPriceFilter:{$this->id}";
        if ($includeSubs) {
            $key .= "-1";
        }
        else {
            $key .= "-0";
        }
        $prices = Cache::rememberForever($key, function () use ($includeSubs) {
            // Добавляем цену.
            $pIds = $this->getPIds($includeSubs);
            $variations = ProductVariation::query()
                ->select(['id', 'price'])
                ->whereIn('product_id', $pIds)
                ->where('available', '=', 1)
                ->get();
            $prices = [];
            foreach ($variations as $variation) {
                $price = false;
                if (!empty($variation->price)) {
                    $price = $variation->price;
                }
                if ($price && !in_array($price, $prices)) {
                    $prices[] = $price;
                }
            }
            return $prices;
        });
        if (!empty($prices)) {
            array_unshift($fieldsInfo, (object) [
                'id' => 0,
                'title' => 'Цена',
                'filter' => 1,
                'type' => 'range',
                'machine' => 'product_price',
                'values' => $prices,
            ]);
        }
    }

    /**
     * Только поля включенные в фильтр.
     *
     * @param $fields
     * @return array
     */
    private function getOnlyFilter($fields)
    {
        $filtered = [];
        foreach ($fields as $field) {
            if ($field->filter) {
                $filtered[] = $field;
            }
        }
        return $filtered;
    }

    /**
     * Заполняем фильтр.
     *
     * @param $fieldsInfo
     * @param $fieldValues
     */
    private function setProductValuesToFilter(&$fieldsInfo, $fieldValues)
    {
        // Записываем значения для полей.
        foreach ($fieldsInfo as &$field) {
            if (!isset($field->values)) {
                $field->values = [];
            }
            $fieldId = $field->id;
            if (empty($fieldValues[$fieldId])) {
                continue;
            }
            $field->values = $fieldValues[$fieldId];
        }

        // Убираем пустые.
        foreach ($fieldsInfo as $key => $item) {
            if (empty($item->values)) {
                unset($fieldsInfo[$key]);
            }
        }
    }

    /**
     * Получить значения товаров.
     *
     * @param $includeSubs
     * @return array
     */
    private function getProductValues($includeSubs)
    {
        $key = "category-getProductValues:{$this->id}";
        if ($includeSubs) {
            $key .= "-1";
        }
        else {
            $key .= "-0";
        }
        return Cache::rememberForever($key, function () use ($includeSubs) {
            $pIds = $this->getPIds($includeSubs);

            // Ищем значения у этих товаров.
            $productValues = ProductField::query()
                ->select(['field_id', 'value'])
                ->whereIn('product_id', $pIds)
                ->orderBy('product_id')
                ->get();
            $fieldValues = [];
            // Группируем по полю.
            foreach ($productValues as $productValue) {
                $fieldId = $productValue->field_id;
                if (empty($fieldValues[$fieldId])) {
                    $fieldValues[$fieldId] = [];
                }
                if (!in_array($productValue->value, $fieldValues[$fieldId])) {
                    $fieldValues[$fieldId][] = $productValue->value;
                }
            }

            return $fieldValues;
        });
    }

    /**
     * Ищем товары категории.
     *
     * @param $includeSubs
     * @return array
     */
    private function getPIds($includeSubs)
    {
        $key = "category-getPIds:{$this->id}";
        if ($includeSubs) {
            $key .= "-1";
        }
        else {
            $key .= "-0";
        }
        return Cache::rememberForever($key, function () use ($includeSubs) {
            $query = Product::query()
                ->select('id');
            if ($includeSubs) {
                $query->whereIn("category_id", $this->getChildren(true));
            }
            else {
                $query->where('category_id', $this->id);
            }
            $products = $query->get();
            $pIds = [];

            foreach ($products as $product) {
                $pIds[] = $product->id;
            }

            return $pIds;
        });
    }

    /**
     * Добавить задачу в очередь.
     *
     * @param $method
     */
    private function addCacheJob($method)
    {
        if (Schema::hasTable('jobs')) {
            CategoryCache::dispatch($this, $method)
                ->onQueue("catalogCache")
                ->delay(now()->addSeconds(2));
        }
    }
}
