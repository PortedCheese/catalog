<?php

namespace PortedCheese\Catalog\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\ProductState;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PortedCheese\Catalog\Http\Requests\ProductStateStoreRequest;
use PortedCheese\Catalog\Http\Requests\ProductStateUpdateRequest;

class ProductStateController extends Controller
{
    const PAGER = 20;

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

    public function show(Request $request, ProductState $state)
    {
        $query = $request->query;
        $products = $state->products();
        if ($query->get('title')) {
            $title = trim($query->get('title'));
            $products->where('title', 'LIKE', "%$title%");
        }
        $products->orderBy('created_at', 'desc');
        $perPage = env("CATALOG_PRODUCT_STATE_ADMIN_PAGER", self::PAGER);
        return view("catalog::admin.states.products.show", [
            'state' => $state,
            'products' => $products->paginate($perPage)->appends($request->input()),
            'query' => $query,
            'categories' => [],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ProductStateStoreRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ProductStateStoreRequest $request)
    {
        $userInput = $request->all();
        if (empty($userInput['slug'])) {
            $slug = Str::slug($userInput['title'], '-');
            $buf = $slug;
            $i = 1;
            while (ProductState::where('slug', $slug)->count()) {
                $slug = $buf . '-' . $i++;
            }
            $userInput['slug'] = $slug;
        }
        ProductState::create($userInput);
        return redirect()
            ->route("admin.product-state.index")
            ->with('success', 'Метка успешно создана');
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
     * @param ProductStateUpdateRequest $request
     * @param ProductState $state
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ProductStateUpdateRequest $request, ProductState $state)
    {
        $userInput = $request->all();
        if (empty($userInput['slug'])) {
            $slug = Str::slug($userInput['title'], '-');
            $buf = $slug;
            $i = 1;
            while (ProductState::where('slug', $slug)->count()) {
                $slug = $buf . '-' . $i++;
            }
            $userInput['slug'] = $slug;
        }
        $state->update($userInput);

        return redirect()
            ->route('admin.product-state.index')
            ->with('success', 'Метка успешно обновлена');
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
