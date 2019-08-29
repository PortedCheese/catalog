<?php

namespace PortedCheese\Catalog\Models;

use App\User;
use App\OrderState;
use App\OrderItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use PortedCheese\Catalog\Notifications\NewOrderClient;
use PortedCheese\Catalog\Notifications\NewOrderUser;

class Order extends Model
{
    use Notifiable;

    protected $fillable = [
        'user_id',
        'user_data',
        'state_id',
        'total',
    ];

    protected $casts = [
        'user_data' => 'array'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->state_id)) {
                $state = OrderState::getNewState();
                $model->state_id = $state->id;
            }
        });

        static::deleting(function ($model) {
            foreach ($model->items as $item) {
                $item->delete();
            }
        });
    }

    /**
     * Валидация оформления заказа.
     *
     * @return array
     */
    public static function requestOrderFullCartRules()
    {
        return [
            'name' => 'required|min:2',
            'email' => 'nullable|required_without:phone|email',
            'phone' => 'required_without:email',
        ];
    }

    /**
     * Названия полей при валидации офрмления заказа.
     *
     * @return array
     */
    public static function requestOrderFullCartAttributes()
    {
        return [
            'name' => 'Имя',
            'email' => "E-mail",
            'phone' => 'Телефон',
        ];
    }

    /**
     * Сообщения при валидации оформления заказа.
     *
     * @return array
     */
    public static function requestOrderFullCartMessages()
    {
        return [
            'name.required' => 'Поле :attribute обязательно для заполнения',
            'name.min' => "Поле :attribute должно быть минимум :min символа",
            'email.required_without' => "Поле :attribute обязательно когда :values не заполнено.",
            'email.email' => "Поле :attribute должно быть валидным e-mail адресом",
            'phone.required_without' => "Поле :attribute обязательно когда :values не заполнено.",
        ];
    }

    /**
     * Валидация для заказа одного товара.
     *
     * @return array
     */
    public static function requestOrderSingleProductRules()
    {
        return [
            'name' => 'required|min:2',
            'email' => 'nullable|required_without:phone|email',
            'phone' => 'required_without:email',
            'variation' => 'required|exists:product_variations,id',
        ];
    }

    /**
     * Названия полей для заказа одного товара.
     *
     * @return array
     */
    public static function requestOrderSingleProductAttributes()
    {
        return [
            'name' => 'Имя',
            'email' => "E-mail",
            'phone' => 'Телефон',
        ];
    }

    /**
     * Сообщения об ошибках на заказ одного товара.
     *
     * @return array
     */
    public static function requestOrderSingleProductMessages()
    {
        return [
            'name.required' => 'Поле :attribute обязательно для заполнения',
            'name.min' => "Поле :attribute должно быть минимум :min символа",
            'email.required_without' => "Поле :attribute обязательно когда :values не заполнено.",
            'email.email' => "Поле :attribute должно быть валидным e-mail адресом",
            'phone.required_without' => "Поле :attribute обязательно когда :values не заполнено.",
        ];
    }

    /**
     * Может быть много позиций.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Пользователь.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Статус заказа.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function state()
    {
        return $this->belongsTo(OrderState::class, 'state_id');
    }

    /**
     * Сообщение которое посылается пользователю при оформлении заказа.
     *
     * @return NewOrderUser
     */
    public function getNewOrderUserNotification()
    {
        return new NewOrderUser($this);
    }

    /**
     * Сообщение которое посылается клиенту при оформлении заказа.
     *
     * @return NewOrderClient
     */
    public function getNewOrderClientNotification()
    {
        return new NewOrderClient($this);
    }

    /**
     * Route notifications for the mail channel.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return string
     */
    public function routeNotificationForMail($notification)
    {
        // TODO: change.
        return env("CATALOG_ORDER_NOTIFY_EMAIL", "dev@gis4biz.ru");
    }

    /**
     * Добавить вариацию к заказу.
     *
     * @param $variationsInfo
     */
    public function addVariations($variationsInfo)
    {
        $ids = array_keys($variationsInfo);
        $exists = OrderItem::where('order_id', $this->id)
            ->whereIn('variation_id', $ids)
            ->get();
        foreach ($exists as $exist) {
            $id = $exist->id;
            $quantity = $variationsInfo[$id];
            unset($variationsInfo[$id]);
            $exist->increaseQuantity($quantity);
        }

        foreach ($variationsInfo as $id => $quantity) {
            OrderItem::addItem($this, $id, $quantity);
        }
    }

    /**
     * Пересчитать стоимость.
     */
    public function recalculateTotal()
    {
        $total = 0;
        $items = OrderItem::query()
            ->select('total')
            ->where('order_id', $this->id)
            ->get();
        foreach ($items as $item) {
            $total += $item->total;
        }
        $this->total = $total;
        $this->save();
    }
}
