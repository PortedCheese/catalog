<?php

namespace PortedCheese\Catalog\Http\Controllers\Admin;

use App\Category;
use App\Http\Controllers\Controller;
use App\Product;
use App\ProductVariation;
use Illuminate\Http\Request;
use PortedCheese\Catalog\Http\Requests\ProductVariationStoreRequest;
use PortedCheese\Catalog\Http\Requests\ProductVariationUpdateRequest;

class ProductVariationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
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
     * @return \Illuminate\Http\Response
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductVariationStoreRequest $request, Category $category, Product $product)
    {
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
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
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProductVariationUpdateRequest $request, Category $category, Product $product, ProductVariation $variation)
    {
        $userInput = $request->all();
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
