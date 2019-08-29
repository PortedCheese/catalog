<?php

namespace PortedCheese\Catalog\Models;

use App\Order;
use Illuminate\Database\Eloquent\Model;
use PortedCheese\Catalog\Http\Requests\OrderStateUpdateRequest;

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
     * Валидация на создание статуса заказа.
     *
     * @return array
     */
    public static function requestOrderStateStoreRules()
    {
        return [
            'title' => 'required|min:2|unique:order_states,title',
            'machine' => 'nullable|min:2|unique:order_states,machine',
        ];
    }

    /**
     * Названия полей в валидации создания статуса заказа.
     *
     * @return array
     */
    public static function requestOrderStateStoreAttributes()
    {
        return [
            'title' => 'Заголовок',
            'machine' => 'Ключ',
        ];
    }

    /**
     * Валидация обновления статуса заказа.
     *
     * @param OrderStateUpdateRequest $validator
     * @return array
     */
    public static function requestOrderStateUpdateRules(OrderStateUpdateRequest $validator)
    {
        $state = $validator->route()->parameter('state', NULL);
        $id = !empty($state) ? $state->id : NULL;
        return [
            'title' => "required|min:2|unique:order_states,title,{$id}",
        ];
    }

    /**
     * Названия полей в валидации обновления статуса заказа.
     *
     * @return array
     */
    public static function requestOrderStateUpdateAttributes()
    {
        return [
            'title' => 'Заголовок',
        ];
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
