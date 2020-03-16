<?php

namespace PortedCheese\Catalog\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\ProductState;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductStateController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->authorizeResource(ProductState::class, "state");
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view("catalog::admin.states.products.index", [
            'states' => ProductState::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view("catalog::admin.states.products.create", [
            'colors' => ProductState::COLORS,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->storeValidator($request->all());
        ProductState::create($request->all());
        return redirect()
            ->route("admin.product-state.index")
            ->with('success', 'Метка успешно создана');
    }

    protected function storeValidator(array $data)
    {
        Validator::make($data, [
            'title' => ['required', 'min:2', "max:20", 'unique:product_states,title'],
            'slug' => ['nullable', 'min:2', "max:20", 'unique:product_states,slug'],
            'color' => ['required'],
        ], [], [
            'title' => 'Заголовок',
            "slug" => "Slug",
            'color' => 'Цвет',
        ])->validate();
    }

    public function show(Request $request, ProductState $state)
    {
        $query = $request->query;
        $products = $state->products();
        if ($query->get('title')) {
            $title = trim($query->get('title'));
            $products->where('title', 'LIKE', "%$title%");
        }
        $products->orderBy('created_at', 'desc');
        $perPage = siteconf()->get("catalog", "productStatesAdminPager");
        return view("catalog::admin.states.products.show", [
            'state' => $state,
            'products' => $products->paginate($perPage)->appends($request->input()),
            'query' => $query,
            'categories' => [],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param ProductState $state
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(ProductState $state)
    {
        return view("catalog::admin.states.products.edit", [
            'state' => $state,
            'colors' => ProductState::COLORS,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param ProductState $state
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, ProductState $state)
    {
        $this->updateValidator($request->all(), $state);
        $state->update($request->all());

        return redirect()
            ->route('admin.product-state.index')
            ->with('success', 'Метка успешно обновлена');
    }

    protected function updateValidator(array $data, ProductState $state)
    {
        $id = $state->id;
        Validator::make($data, [
            "title" => ["required", "min:2", "max:20", "unique:product_states,title,{$id}"],
            "slug" => ["nullable", "min:2", "max:20", "unique:product_states,slug,{$id}"],
            "color" => "required",
        ], [], [
            'title' => 'Заголовок',
            'color' => 'Цвет',
        ])->validate();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param ProductState $state
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(ProductState $state)
    {
        $state->delete();

        return redirect()
            ->back()
            ->with('success', 'Метка успешно удалена');
    }
}
