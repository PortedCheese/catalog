<?php

namespace PortedCheese\Catalog\Models;

use App\Order;
use App\ProductVariation;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'sku',
        'price',
        'quantity',
        'total',
        'description',
        'title',
        'product_id',
        'variation_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($model) {
            $model->total = $model->price * $model->quantity;
        });

        static::creating(function ($model) {
            $model->total = $model->price * $model->quantity;
        });
    }

    /**
     * Добавить позицию заказа.
     *
     * @param Order $order
     * @param $variation
     * @param int $quantity
     * @return bool
     */
    public static function addItem(Order $order, $variation, $quantity = 1)
    {
        if (is_numeric($variation)) {
            try {
                $variation = ProductVariation::findOrFail($variation);
            }
            catch (\Exception $e) {
                return false;
            }
        }
        $product = $variation->product;

        try {
            $orderItem = self::create([
                'order_id' => $order->id,
                'sku' => $variation->sku,
                'price' => $variation->price,
                'quantity' => $quantity,
                'description' => $variation->description,
                'title' => $product->title,
                'product_id' => $product->id,
                'variation_id' => $variation->id,
            ]);
        }
        catch (\Exception $e) {
            return false;
        }

        return $orderItem;
    }

    /**
     * Относится к заказу.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(\App\Order::class);
    }

    /**
     * Относится к товару.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(\App\Product::class);
    }

    /**
     * Увеличить количество.
     *
     * @param $quantity
     * @return bool
     */
    public function increaseQuantity($quantity)
    {
        $this->quantity += $quantity;
        $this->save();
        return true;
    }

    /**
     * Уменьшить количество.
     *
     * @param $quantity
     * @return bool
     */
    public function decreaseQuantity($quantity)
    {
        if ($this->quantity > $quantity) {
            $this->quantity -= $quantity;
            $this->save();
            return true;
        }
        return false;
    }

}
