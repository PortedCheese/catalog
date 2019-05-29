<?php

namespace PortedCheese\Catalog\Http\Controllers\Site;

use App\Cart;
use App\Http\Controllers\Controller;
use App\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PortedCheese\Catalog\Events\CreateNewOrder;
use PortedCheese\Catalog\Http\Requests\OrderFullCartRequest;
use PortedCheese\Catalog\Http\Requests\OrderSingleProductRequest;

class OrderController extends Controller
{
    /**
     * Заказать товар.
     *
     * @param OrderSingleProductRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function makeProductOrder(OrderSingleProductRequest $request)
    {
        $userInput = $request->all();
        $variationId = $userInput['variation'];
        unset($userInput['variation']);

        $orderData = [
            'user_data' => $userInput,
        ];
        if (Auth::check()) {
            $orderData['user_id'] = Auth::user()->id;
        }
        $order = Order::create($orderData);
        $order->addVariations([
            $variationId => 1
        ]);
        $order->recalculateTotal();
        event(new CreateNewOrder($order));
        return response()
            ->json([
                'success' => true,
                'message' => "Ваш заказ получен. В ближайшее время с Вами свяжется менеджер.",
            ]);
    }

    /**
     * Оформление корзины.
     *
     * @param OrderFullCartRequest $request
     */
    public function makeCartOrder(OrderFullCartRequest $request)
    {
        $userInput = $request->all();

        $orderData = [
            'user_data' => $userInput,
        ];
        if (Auth::check()) {
            $orderData['user_id'] = Auth::user()->id;
        }
        $order = Order::create($orderData);
        
        $cart = Cart::getCart();
        $variations = [];
        foreach ($cart->items as $product) {
            foreach ($product['variations'] as $vid => $quantity) {
                $variations[$vid] = $quantity;
            }
        }
        $order->addVariations($variations);
        $order->recalculateTotal();
        $cart->delete();

        event(new CreateNewOrder($order));

        return redirect()
            ->route('site.cart.index')
            ->with("success", "Заказ успешно оформлен");
    }
}
