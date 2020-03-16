<?php

namespace PortedCheese\Catalog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CategoryField extends Model
{
    protected $fillable = [
        'title',
        'machine',
        'type',
        'group_id',
    ];

    const TYPES = [
        'select' => 'Список',
        'checkbox' => "Галочки",
        'range' => "Диапазон",
    ];

    protected static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $machine = $model->machine;
            if (empty($machine)) {
                $machine = $model->title;
            }
            $machine = Str::slug($machine, '_');
            $machine = str_replace("-", "_", $machine);
            $buf = $machine;
            $i = 1;
            while (self::query()->where('machine', $machine)->count()) {
                $machine = $buf . '_' . $i++;
            }
            $model->machine = $machine;
        });
    }

    /**
     * Доступные поля для категории.
     *
     * @param Category $category
     * @return mixed
     */
    public static function getForCategory(\App\Category $category)
    {
        $ids = [];
        foreach ($category->fields as $field) {
            $ids[] = $field->id;
        }
        return \App\CategoryField::whereNotIn('id', $ids)->get();
    }

    /**
     * Зачения у поля.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function values()
    {
        return $this->hasMany(\App\ProductField::class, 'field_id');
    }

    /**
     * Поле может относится ко многим категориям.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categories()
    {
        return $this->belongsToMany(\App\Category::class)
            ->withPivot('title')
            ->withPivot('filter')
            ->withPivot("weight")
            ->withTimestamps();
    }

    /**
     * Может принадлежать к группе.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group()
    {
        return $this->belongsTo(\App\CategoryFieldGroup::class, 'group_id');
    }

    /**
     * Если поле больше нигде не используется, удаляем.
     *
     * @throws \Exception
     */
    public function checkCategoryOnDetach()
    {
        if (! $this->categories->count()) {
            $this->delete();
        }
    }

    /**
     * Аттрибут для вывода типа поля.
     *
     * @return mixed
     */
    public function getTypeHumanAttribute()
    {
        $type = $this->type;
        if (! empty(self::TYPES[$type])) {
            return self::TYPES[$type];
        }
        else {
            return $type;
        }
    }
}
