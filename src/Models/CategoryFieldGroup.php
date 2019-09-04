<?php

namespace PortedCheese\Catalog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CategoryFieldGroup extends Model
{
    protected $fillable = [
        'title',
        'machine',
        'weight',
    ];

    protected static function boot()
    {
        parent::boot();

        self::creating(function (\App\CategoryFieldGroup $model) {
            $machine = $model->machine;
            $machine = Str::slug($machine, "_");
            $machine = str_replace("-", "_", $machine);
            $buf = $machine;
            $i = 1;
            while (self::where("machine", $machine)->count()) {
                $machine = $buf . "_" . $i++;
            }
            $model->machine = $machine;
        });
    }

    /**
     * Валидация добавления группы характеристик.
     *
     * @return array
     */
    public static function requestCategoryFieldGroupCreateRules()
    {
        return [
            'title' => 'required|min:2|max:200',
            'machine' => 'nullable|min:4|max:100|unique:category_field_groups,machine',
            "weight" => "required|numeric|min:1",
        ];
    }

    public static function requestCategoryFieldGroupUpdateRules()
    {
        return [
            "title" => "required|min:2|max:200",
            "weight" => "required|numeric|min:1",
        ];
    }

    /**
     * Может относится ко многим полям.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function fields()
    {
        return $this->hasMany(\App\CategoryField::class, "group_id");
    }
}
