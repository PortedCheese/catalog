<?php

namespace PortedCheese\Catalog\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryField extends Model
{
    protected $fillable = [
        'title',
        'machine',
        'type',
    ];

    const TYPES = [
        'select' => 'Список',
        'checkbox' => "Галочки",
        'range' => "Диапазон",
    ];

    /**
     * Поле может относится ко многим категориям.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class)
            ->withPivot('title')
            ->withPivot('filter')
            ->withTimestamps();
    }

    /**
     * Если поле больше нигде не используется, удаляем.
     * @throws \Exception
     */
    public function checkCategoryOnDetach()
    {
        if (! $this->categories->count()) {
            $this->delete();
        }
    }

    /**
     * Доступные поля для категории.
     *
     * @param Category $category
     * @return mixed
     */
    public static function getForCategory(Category $category)
    {
        $ids = [];
        foreach ($category->fields as $field) {
            $ids[] = $field->id;
        }
        return CategoryField::whereNotIn('id', $ids)->get();
    }
}
