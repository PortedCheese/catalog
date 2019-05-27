<?php

namespace PortedCheese\Catalog\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PortedCheese\Catalog\Events\CreateNewOrder;
use PortedCheese\Catalog\Http\Requests\OrderSingleProductRequest;

class OrderController extends Controller
{
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
}
