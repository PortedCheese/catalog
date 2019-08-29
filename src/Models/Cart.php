<?php

namespace PortedCheese\Catalog\Models;

use App\User;
use App\Product;
use App\ProductVariation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'items',
    ];

    protected $casts = [
        'items' => 'array'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (\App\Cart $model) {
            $model->uuid = (string) Str::uuid();
        });

        static::updating(function (\App\Cart $model) {
            $model->total = $model->getTotal();
        });

        static::updated(function (\App\Cart $model) {
           $model->setCookie();
        });
    }

    /**
     * Валидация добавления в корзину.
     *
     * @return array
     */
    public static function requestAddToCartRules()
    {
        return [
            'quantity' => 'required|numeric|min:1',
            'variation' => 'required|exists:product_variations,id',
        ];
    }

    /**
     * Сообщения об ошибках добавления в корзину.
     *
     * @return array
     */
    public static function requestAddToCartMessages()
    {
        return [
            'quantity.required' => 'Количество не может быть пустым',
            'quantity.numeric' => 'Количество должно быть числом',
            'quantity.min' => "Количество должно быть минимум :min",
        ];
    }

    /**
     * Валидация обновления количества.
     *
     * @return array
     */
    public static function requestChangeQuantityRules()
    {
        return [
            'quantity' => 'required|numeric|min:1',
        ];
    }

    /**
     * Сообщения об ошибках при обновлении количества.
     *
     * @return array
     */
    public static function requestChangeQuantityMessages()
    {
        return [
            'quantity.required' => 'Количество не может быть пустым',
            'quantity.numeric' => 'Количество должно быть числом',
            'quantity.min' => "Количество должно быть минимум :min",
        ];
    }

    /**
     * Добавить в корзину.
     *
     * @param Product $product
     * @param $variationId
     * @param int $quantity
     * @return bool|mixed|Cart
     * @throws \Exception
     */
    public static function addToCard(Product $product, $variationId, $quantity = 1)
    {
        $cart = self::getCart();
        if (!$cart) {
            $cart = self::initCart();
        }
        $cart->addProductVariation($product->id, $variationId, $quantity);
        return $cart;
    }

    /**
     * Создание корзины.
     *
     * @return mixed
     */
    public static function initCart()
    {
        $userId = null;
        if (Auth::check()) {
            $userId = Auth::user()->id;
        }
        $cart = self::create([
            'user_id' => $userId,
        ]);
        $uuid = $cart->uuid;

        return $cart;
    }

    /**
     * Получить корзину.
     *
     * @return bool|Cart
     * @throws \Exception
     */
    public static function getCart()
    {
        $cookie = Cookie::get('cartUuid', false);
        // Ищем по куке.
        if ($cookie) {
            $cart = self::findByUuid($cookie);
            if ($cart) {
                // Если авторизован, нужно записать ему эту корзину.
                if (Auth::check() && empty($cart->user_id)) {
                    $user = Auth::user();
                    $userCart = self::findByUserId($user->id);
                    // Если у пользователя была корзина,
                    // но он заполнил новую под гостем,
                    // новая корзина приоритетней.
                    if ($userCart && $userCart->id != $cart->id) {
                        $userCart->delete();
                        $cart->user_id = $user->id;
                        $cart->save();
                    }
                }
                return $cart;
            }
        }
        // Ищем по пользователю.
        if (Auth::check()) {
            $user = Auth::user();
            $cart = self::findByUserId($user->id);
            if ($cart) {
                return $cart;
            }
        }
        return false;
    }

    /**
     * Найти по пользователю.
     *
     * @param $id
     * @return bool|self
     */
    public static function findByUserId($id)
    {
        try {
            return self::where('user_id', $id)->firstOrFail();
        }
        catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Найти корзину по uuid.
     *
     * @param $uiid
     * @return bool|self
     */
    public static function findByUuid($uiid)
    {
        try {
            return self::where('uuid', $uiid)->firstOrFail();
        }
        catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Владелец корзины.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Изменить количество вариации.
     *
     * @param $pid
     * @param $vid
     * @param $quantity
     */
    public function changeQuantity($pid, $vid, $quantity)
    {
        $items = $this->items;
        if (!empty($items[$pid]['variations'][$vid])) {
            $items[$pid]['variations'][$vid] = $quantity;
        }
        $this->items = $items;
        $this->save();
    }

    /**
     * Добавить вариацию.
     *
     * @param $pid
     * @param $vid
     * @param int $quantity
     */
    public function addProductVariation($pid, $vid, $quantity = 1)
    {
        $items = !empty($this->items) ? $this->items : [];
        if (empty($items[$pid])) {
            $items[$pid] = [
                'variations' => [],
                'id' => $pid,
            ];
        }
        if (empty($items[$pid]['variations'][$vid])) {
            $items[$pid]['variations'][$vid] = 0;
        }
        $items[$pid]['variations'][$vid] += $quantity;

        $this->items = $items;
        $this->save();
    }

    /**
     * Поставить куку.
     */
    public function setCookie()
    {
        $cookie = Cookie::make('cartUuid', $this->uuid, 60*24*30);
        Cookie::queue($cookie);
    }

    /**
     * Получить итого.
     *
     * @return float|int
     */
    public function getTotal()
    {
        $vids = [];
        foreach ($this->items as $pid => $value) {
            foreach ($value['variations'] as $vid => $quantity) {
                $vids[$vid] = $quantity;
            }
        }
        if (empty($vids)) {
            return 0;
        }
        $variations = \App\ProductVariation::query()
            ->select('id', 'price')
            ->whereIn('id', array_keys($vids))
            ->get();
        $total = 0;
        foreach ($variations as $variation) {
            $id = $variation->id;
            $price = $variation->price;
            $total += $vids[$id] * $price;
        }

        return $total;
    }

    /**
     * Посчитать количество добавленных элементов.
     *
     * @return int
     */
    public function getCount()
    {
        $count = 0;
        foreach ($this->items as $pid => $value) {
            foreach ($value['variations'] as $vid => $quantity) {
                $count += $quantity;
            }
        }
        return $count;
    }

    /**
     * Массив для вывода корзины.
     *
     * @return array
     */
    public function getForRender()
    {
        $products = [];
        $categories = [];
        foreach ($this->items as $item) {
            $pid = $item['id'];
            $variations = $item['variations'];
            $product = $this->getProduct($pid);
            if (empty($product)) {
                continue;
            }
            $items = [];
            foreach ($variations as $vid => $quantity) {
                $variation = $this->getVariation($pid, $vid);
                if (empty($variation)) {
                    continue;
                }
                $items[$vid] = (object) [
                    'model' => $variation,
                    'quantity' => $quantity,
                    'price' => $variation->price,
                    'total' => round($quantity * $variation->price, 2),
                    'description' => $variation->description,
                ];
            }
            $cid = $product->category_id;
            if (empty($categories[$cid])) {
                $category = $product->category;
                $categories[$cid] = $category;
            }
            else {
                $category = $categories[$cid];
            }
            $products[] = (object) [
                'model' => $product,
                'category' => $category,
                'items' => $items,
                'title' => $product->title,
                'image' => $product->image,
            ];
        }
        return $products;
    }

    /**
     * Удалить вариацию из корзины.
     *
     * @param $pid
     * @param $vid
     */
    public function removeVariation($pid, $vid)
    {
        $items = $this->items;
        if (!empty($items[$pid]['variations'][$vid])) {
            unset($items[$pid]['variations'][$vid]);
            if (empty($items[$pid]['variations'])) {
                $this->removeProduct($pid);
            }
            else {
                $this->items = $items;
                $this->save();
            }
        }
    }

    /**
     * Получаем вариацию товара.
     *
     * @param $pid
     * @param $vid
     * @return |null
     */
    private function getVariation($pid, $vid)
    {
        try {
            $variation = ProductVariation::findOrFail($vid);
        }
        catch (\Exception $e) {
            $this->removeVariation($pid, $vid);
            return null;
        }
        if (!$variation->available) {
            $this->removeVariation($pid, $vid);
            return null;
        }
        return $variation;
    }

    /**
     * Получаем товар.
     *
     * @param $pid
     * @return |null
     */
    private function getProduct($pid)
    {
        try {
            $product = Product::findOrFail($pid);
        }
        catch (\Exception $e) {
            $this->removeProduct($pid);
            return null;
        }
        if (!$product->published) {
            $this->removeProduct($pid);
            return null;
        }
        return $product;
    }

    /**
     * Удаляем товар.
     *
     * @param $pid
     */
    private function removeProduct($pid)
    {
        $items = $this->items;
        if (!empty($items[$pid])) {
            unset($items[$pid]);
            $this->items = $items;
            $this->save();
        }
    }
}
