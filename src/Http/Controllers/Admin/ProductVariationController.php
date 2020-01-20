<?php

namespace PortedCheese\Catalog\Http\Controllers\Admin;

use App\Category;
use App\Http\Controllers\Controller;
use App\Product;
use App\ProductVariation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductVariationController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->authorizeResource(ProductVariation::class, "variation");
    }

    /**
     * Display a listing of the resource.
     *
     * @param Category $category
     * @param Product $product
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Category $category, Product $product)
    {
        return view("catalog::admin.categories.products.variations.index", [
            'product' => $product,
            'category' => $category,
            'variations' => $product->variations,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Category $category
     * @param Product $product
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Category $category, Product $product)
    {
        return view("catalog::admin.categories.products.variations.create", [
            'product' => $product,
            'category' => $category,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param Category $category
     * @param Product $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Category $category, Product $product)
    {
        $this->storeValidator($request->all());

        $userInput = $request->all();
        if ($request->has('sale')) {
            $userInput['sale'] = 1;
        }
        else {
            $userInput['sale'] = 0;
        }
        ProductVariation::create($userInput);
        return redirect()
            ->route('admin.category.product.variation.index', [
                'category' => $category,
                'product' => $product,
            ])
            ->with('success', 'Вариация добавлена');
    }

    protected function storeValidator(array $data)
    {
        Validator::make($data, [
            'sku' => ['nullable', 'min:2', "max:250", 'unique:product_variations,sku'],
            'product_id' => ['required', 'exists:products,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0'],
            'description' => ['required', 'min:2'],
        ], [], [
            'sku' => 'Артикул',
            'product_id' => "Товар",
            'price' => 'Цена',
            'sale_price' => 'Цена со скидкой',
            'description' => 'Описание',
        ])->validate();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Category $category
     * @param Product $product
     * @param ProductVariation $variation
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Category $category, Product $product, ProductVariation $variation)
    {
        return view("catalog::admin.categories.products.variations.edit", [
            'product' => $product,
            'category' => $category,
            'variation' => $variation,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Category $category
     * @param Product $product
     * @param ProductVariation $variation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Category $category, Product $product, ProductVariation $variation)
    {
        $userInput = $request->all();

        $this->updateValidator($request->all(), $variation);

        if ($request->has('sale')) {
            $userInput['sale'] = 1;
        }
        else {
            $userInput['sale'] = 0;
        }
        if ($request->has('available')) {
            $userInput['available'] = 1;
        }
        else {
            $userInput['available'] = 0;
        }
        $variation->update($userInput);
        return redirect()
            ->route('admin.category.product.variation.index', [
                'category' => $category,
                'product' => $product,
            ])
            ->with('success', 'Вариация обновлена');
    }

    protected function updateValidator(array $data, ProductVariation $variation)
    {
        $id = $variation->id;
        Validator::make($data, [
            'sku' => ["nullable", "min:2", "max:250", "unique:product_variations,sku,{$id}"],
            'price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0'],
            'description' => ['required', 'min:2'],
        ], [], [
            'sku' => 'Артикул',
            'price' => 'Цена',
            'sale_price' => 'Цена со скидкой',
            'description' => 'Описание',
        ])->validate();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Category $category
     * @param Product $product
     * @param ProductVariation $variation
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Category $category, Product $product, ProductVariation $variation)
    {
        $variation->delete();
        return redirect()
            ->back()
            ->with('success', 'Вариация удалена');
    }
}
