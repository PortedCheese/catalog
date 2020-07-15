<?php

namespace PortedCheese\Catalog\Http\Controllers\Site;

use App\Cart;
use App\Http\Controllers\Controller;
use App\Order;
use App\OrderState;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use PortedCheese\Catalog\Events\CreateNewOrder;

class OrderController extends Controller
{
    const PAGER = 10;

    /**
     * Заказать товар.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function makeProductOrder(Request $request)
    {
        $this->makeProductOrderValidator($request->all());

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

    protected function makeProductOrderValidator(array $data)
    {
        Validator::make($data, [
            'name' => ['required', 'min:2'],
            'email' => ['nullable', 'required_without:phone', 'email'],
            'phone' => ['required_without:email'],
            'variation' => ['required', 'exists:product_variations,id'],
        ], [
            'name.required' => 'Поле :attribute обязательно для заполнения',
            'name.min' => "Поле :attribute должно быть минимум :min символа",
            'email.required_without' => "Поле :attribute обязательно когда :values не заполнено.",
            'email.email' => "Поле :attribute должно быть валидным e-mail адресом",
            'phone.required_without' => "Поле :attribute обязательно когда :values не заполнено.",
        ], [
            'name' => 'Имя',
            'email' => "E-mail",
            'phone' => 'Телефон',
        ])->validate();
    }

    /**
     * Оформление корзины.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function makeCartOrder(Request $request)
    {
        $this->makeCartOrderValidator($request->all());

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

    protected function makeCartOrderValidator(array $data)
    {
        Validator::make($data, [
            'name' => ['required', 'min:2'],
            'email' => ['nullable', 'required_without:phone', 'email'],
            'phone' => ['required_without:email'],
            "privacy_policy" => ["accepted"],
        ], [
            'name.required' => 'Поле :attribute обязательно для заполнения',
            'name.min' => "Поле :attribute должно быть минимум :min символа",
            'email.required_without' => "Поле :attribute обязательно когда :values не заполнено.",
            'email.email' => "Поле :attribute должно быть валидным e-mail адресом",
            'phone.required_without' => "Поле :attribute обязательно когда :values не заполнено.",
            "privacy_policy.accepted" => "Требуется согласие с политикой конфиденциальности",
        ], [
            'name' => 'Имя',
            'email' => "E-mail",
            'phone' => 'Телефон',
        ])->validate();
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
        $perPage = siteconf()->get("catalog", "ordersProfilePager");
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
