<?php

namespace PortedCheese\Catalog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
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

        self::updated(function (\App\CategoryFieldGroup $model) {
            $model->forgetGroupCache();
        });

        self::deleted(function (\App\CategoryFieldGroup $model) {
            $model->forgetGroupCache();
        });
    }

    /**
     * Найти данные по id.
     *
     * @param $id
     * @return mixed
     */
    public static function getById($id)
    {
        $key = "category-field-group-getById:{$id}";
        return Cache::rememberForever($key, function () use ($id) {
            try {
                return self::findOrFail($id);
            }
            catch (\Exception $e) {
                return false;
            }
        });
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

    /**
     * Очистить кэш информации о группе.
     */
    public function forgetGroupCache()
    {
        Cache::forget("category-field-group-getById:{$this->id}");
    }
}
