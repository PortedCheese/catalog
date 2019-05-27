<?php

namespace PortedCheese\Catalog\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\OrderState;
use Illuminate\Http\Request;
use PortedCheese\Catalog\Http\Requests\OrderStateStoreRequest;
use PortedCheese\Catalog\Http\Requests\OrderStateUpdateRequest;

class OrderStateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("catalog::admin.states.orders.index", [
            'states' => OrderState::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $default = OrderState::all()->count() ? false : 'new';
        return view("catalog::admin.states.orders.create", [
            'default' => $default,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(OrderStateStoreRequest $request)
    {
        OrderState::create($request->all());
        return redirect()
            ->route("admin.order-state.index")
            ->with('success', 'Статус заказа добавлен');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(OrderState $state)
    {
        return view("catalog::admin.states.orders.edit", [
            'state' => $state,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(OrderStateUpdateRequest $request, OrderState $state)
    {
        $state->update($request->all());
        return redirect()
            ->route('admin.order-state.index')
            ->with('success', 'Статус успешно обновлен');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(OrderState $state)
    {
        if ($state->machine == 'new') {
            return redirect()
                ->back()
                ->with('danger', 'Невозможно удалить статус new');
        }
        if ($state->orders->count()) {
            return redirect()
                ->back()
                ->with('danger', 'Есть заказы с данным статусом');
        }
        $state->delete();
        return redirect()
            ->back()
            ->with('success', 'Статус успешно удален');
    }
}
