<?php

namespace PortedCheese\Catalog\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\OrderState;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->storeValidator($request->all());

        OrderState::create($request->all());
        return redirect()
            ->route("admin.order-state.index")
            ->with('success', 'Статус заказа добавлен');
    }

    private function storeValidator(array $data)
    {
        Validator::make($data, [
            "title" => ["required", "min:2", "unique:order_states,title"],
            "machine" => ["nullable", "min:2", "unique:order_states,machine"],
        ], [], [
            "title" => "Заголовок",
            "machine" => "Ключ",
        ])->validate();
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
     * @param Request $request
     * @param OrderState $state
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, OrderState $state)
    {
        $this->updateValidator($request->all(), $state);

        $state->update($request->all());
        return redirect()
            ->route('admin.order-state.index')
            ->with('success', 'Статус успешно обновлен');
    }

    private function updateValidator(array $data, OrderState $state)
    {
        $id = $state->id;
        Validator::make($data, [
            "title" => ["required", "min:2", "unique:order_states,title,{$id}"],
        ], [], [
            "title" => "Заголовок",
        ])->validate();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param OrderState $state
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
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
