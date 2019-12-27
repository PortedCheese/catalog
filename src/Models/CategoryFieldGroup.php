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

        static::creating(function (\App\CategoryFieldGroup $model) {
            $model->fixSlug();
        });

        static::updated(function (\App\CategoryFieldGroup $model) {
            $model->forgetGroupCache();
        });

        static::deleted(function (\App\CategoryFieldGroup $model) {
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
     * Поправить машинное имя.
     *
     * @param bool $updating
     */
    public function fixSlug($updating = false)
    {
        if ($updating && ($this->original["machine"] == $this->machine)) {
            return;
        }
        if (empty($this->machine)) {
            $slug = $this->title;
        }
        else {
            $slug = $this->machine;
        }
        $slug = Str::slug($slug, "_");
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
            ->where("machine", $buf)
            ->where("id", "!=", $id)
            ->count())
        {
            $buf = $slug . "-" . $i++;
        }
        $buf = str_replace("-", "_", $buf);
        $this->machine = $buf;
    }

    /**
     * Очистить кэш информации о группе.
     */
    public function forgetGroupCache()
    {
        Cache::forget("category-field-group-getById:{$this->id}");
    }
}
