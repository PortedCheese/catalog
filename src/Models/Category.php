<?php

namespace PortedCheese\Catalog\Models;

use App\Image;
use Illuminate\Database\Eloquent\Model;
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

        static::deleting(function ($category) {
            // Удаляем главное изображение.
            $category->clearMainImage();
            // Удаляем метатеги.
            $category->clearMetas();
            // Убираем поля.
            foreach ($category->fields as $field) {
                $category->fields()->detach($field);
                $field->checkCategoryOnDetach();
            }
        });

        static::created(function ($category) {
            // Создать метатеги по умолчанию.
            $category->createDefaultMetas();
            // Поля родителя.
            $category->setParentFields();
        });
    }

    /**
     * Дочернии категории.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Родительская категория.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Характеристики категории.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function fields()
    {
        return $this->belongsToMany(CategoryField::class)
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
}
