<?php

namespace PortedCheese\Catalog\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Order;
use App\OrderState;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = $request->query;
        $orders = Order::query();
        if ($query->get('email', false)) {
            $email = trim($query->get('email'));
            $orders->where('user_data->email', 'LIKE', "%$email%");
        }
        if ($query->get('state', false)) {
            $state = $query->get('state');
            $orders->where('state_id', '=', $state);
        }
        if ($query->get('from', false)) {
            $value = $query->get('from');
            $from = date("Y-m-d", strtotime($value));
            $orders->where('created_at', '>=', $from);
        }
        if ($query->get('to', false)) {
            $value = $query->get('to');
            $to = date("Y-m-d", strtotime("+ 1 day", strtotime($value)));
            $orders->where('created_at', '<=', $to);
        }
        $orders->orderBy('created_at', 'desc');
        $perPage = siteconf()->get("catalog", "ordersAdminPager");
        return view("catalog::admin.orders.index", [
            'orders' => $orders->paginate($perPage)->appends($request->input()),
            'states' => OrderState::getList(),
            'query' => $query,
            'per' => $perPage,
            'page' => $query->get('page', 1) - 1,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param Order $order
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Order $order)
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
        return view("catalog::admin.orders.show", [
            'order' => $order,
            'items' => $items,
            'products' => $products,
            'categories' => $categories,
            'userData' => $order->user_data,
        ]);
    }

    /**
     * Обновление статуса.
     *
     * @param Request $request
     * @param Order $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'state' => 'required|exists:order_states,id',
        ]);

        $order->state_id = $request->get('state');
        $order->save();

        return redirect()
            ->back()
            ->with('success', 'Успешно обновлено');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Order $order
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Order $order)
    {
        $order->delete();

        return redirect()
            ->back()
            ->with('success', 'Заказ удален');
    }
}
