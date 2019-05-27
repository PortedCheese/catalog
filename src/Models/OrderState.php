<?php

namespace PortedCheese\Catalog\Models;

use App\Order;
use Illuminate\Database\Eloquent\Model;

class OrderState extends Model
{
    protected $fillable = [
        'title',
        'machine',
    ];

    /**
     * Получить статус нового заказа.
     *
     * @return mixed
     */
    public static function getNewState()
    {
        try {
            $state = self::where('machine', 'new')->firstOrFail();
        }
        catch (\Exception $e) {
            $state = self::create([
                'title' => 'Новый',
                'machine' => 'new',
            ]);
        }
        return $state;
    }

    /**
     * Список статусов.
     *
     * @return array
     */
    public static function getList()
    {
        $list = [];
        foreach (self::all() as $state) {
            $list[$state->id] = (object) [
                'title' => $state->title,
                'machine' => $state->machine,
            ];
        }
        return $list;
    }

    /**
     * Заказы с этим статусом.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'state_id');
    }

    /**
     * Подгружать по machine.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'machine';
    }
}
