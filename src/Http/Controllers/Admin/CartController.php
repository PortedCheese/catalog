<?php

namespace PortedCheese\Catalog\Http\Controllers\Admin;

use App\Cart;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Список корзин.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = $request->query;
        $perPage = siteconf()->get("catalog", "cartsAdminPager");
        $carts = Cart::query()
            ->orderByDesc('updated_at')
            ->paginate($perPage)
            ->appends($request->input());
        $users = [];
        foreach ($carts as $cart) {
            $userId = $cart->user_id;
            if (empty($users[$userId])) {
                $users[$userId] = $cart->user;
            }
        }
        return view("catalog::admin.carts.index", [
            'carts' => $carts,
            'users' => $users,
            'page' => $query->get('page', 1) - 1,
            'query' => $query,
            'per' => $perPage,
        ]);
    }

    public function show(Cart $cart)
    {
        return view("catalog::admin.carts.show", [
            'cart' => $cart,
        ]);
    }

    public function destroy(Cart $cart)
    {
        $cart->delete();
        return redirect()
            ->back()
            ->with('success', 'Корзина удалена');
    }
}
