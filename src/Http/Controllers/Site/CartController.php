<?php

namespace PortedCheese\Catalog\Http\Controllers\Site;

use App\Cart;
use App\Http\Controllers\Controller;
use App\Product;
use App\ProductVariation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use PortedCheese\Catalog\Http\Requests\AddToCartRequest;
use PortedCheese\Catalog\Http\Requests\ChangeQuantityRequest;

class CartController extends Controller
{
    /**
     * Старинца корзины.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function index()
    {
        return view("catalog::site.cart.index", [
            'cart' => Cart::getCart(),
        ]);
    }

    /**
     * Оформление заказа.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     * @throws \Exception
     */
    public function checkout()
    {
        $cart = Cart::getCart();
        if (!$cart || !$cart->getCount()) {
            return redirect()
                ->route("site.cart.index");
        }
        $user = false;
        if (Auth::check()) {
            $user = Auth::user();
        }
        return view("catalog::site.cart.checkout", [
            'cart' => $cart,
            'user' => $user,
        ]);
    }

    /**
     * Добавить в корзину.
     *
     * @param AddToCartRequest $request
     * @param Product $product
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function addToCart(AddToCartRequest $request, Product $product)
    {
        $cart = Cart::addToCard($product, $request->get('variation'), $request->get('quantity'));

        return response()
            ->json([
                'success' => true,
                'message' => "Добавлено.",
                'cart' => (object) [
                    'total' => $cart->total,
                    'count' => $cart->getCount(),
                ],
            ]);
    }

    /**
     * Удалить из корзины.
     *
     * @param Product $product
     * @param ProductVariation $variation
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function deleteFromCart(Product $product, ProductVariation $variation)
    {
        $cart = Cart::getCart();
        if ($cart) {
            $cart->removeVariation($product->id, $variation->id);
        }
        return redirect()
            ->back()
            ->with('success', "Товар удален из корзины");
    }

    /**
     * Изменить количество.
     *
     * @param ChangeQuantityRequest $request
     * @param Product $product
     * @param ProductVariation $variation
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function changeQuantity(ChangeQuantityRequest $request, Product $product, ProductVariation $variation)
    {
        $cart = Cart::getCart();
        $quantity = $request->get('quantity');
        if ($cart) {
            $cart->changeQuantity($product->id, $variation->id, $quantity);
        }
        return response()
            ->json([
                'success' => true,
                'message' => "Изменено.",
                'cart' => (object) [
                    'total' => $cart->total,
                    'count' => $cart->getCount(),
                ],
                'itemTotal' => round($variation->price * $quantity, 2),
                'itemPrice' => $variation->price,
            ]);
    }
}
