<?php

namespace PortedCheese\Catalog\Http\Controllers\Site;

use App\Cart;
use App\Http\Controllers\Controller;
use App\Order;
use App\OrderState;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PortedCheese\Catalog\Events\CreateNewOrder;
use PortedCheese\Catalog\Http\Requests\OrderFullCartRequest;
use PortedCheese\Catalog\Http\Requests\OrderSingleProductRequest;

class OrderController extends Controller
{
    const PAGER = 10;

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
        if (!empty($userInput["_token"])) {
            unset($userInput["_token"]);
        }

        $orderData = [
            'user_data' => $userInput,
        ];
        $order = Order::create($orderData);
        
        $cart = Cart::getCart();
        if (! $cart) {
            return redirect()
                ->route('site.cart.index')
                ->with('danger', "Корзина не найдена");
        }
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

    /**
     * Список заказов в профиле.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function userList(Request $request)
    {
        $query = $request->query;
        $orders = Order::query()
            ->where("user_id", Auth::user()->id)
            ->orderBy('created_at', 'desc');
        $perPage = env("CATALOG_ORDERS_PROFILE_PAGER", self::PAGER);
        return view("catalog::site.profile.index", [
            'orders' => $orders->paginate($perPage)->appends($request->input()),
            'states' => OrderState::getList(),
            'page' => $query->get('page', 1) - 1,
            'query' => $query,
            'per' => $perPage,
        ]);
    }

    /**
     * Просмотр заказа.
     *
     * @param Order $order
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showOrder(Order $order)
    {
        $items = $order->items;
        $products = [];
        $categories = [];
        foreach ($items as $item) {
            if (empty($products[$item->product_id])) {
                $product = $item->product;
                $products[$product->id] = $product;
                if (empty($categories[$product->category_id])) {
                    $category = $product->category;
                    $categories[$category->id] = $category;
                }
            }
        }
        return view("catalog::site.profile.show", [
            'order' => $order,
            'items' => $items,
            'products' => $products,
            'categories' => $categories,
            'states' => OrderState::getList(),
        ]);
    }
}
