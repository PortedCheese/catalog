<?php

namespace PortedCheese\Catalog\Http\Controllers\Admin;

use App\Category;
use App\Http\Controllers\Controller;
use App\Product;
use App\ProductField;
use Illuminate\Http\Request;
use PortedCheese\Catalog\Events\ProductFieldUpdate;
use PortedCheese\Catalog\Http\Requests\ProductFieldStoreRequest;
use PortedCheese\Catalog\Http\Requests\ProductFieldUpdateRequest;

class ProductFieldController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Category $category
     * @param Product $product
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Category $category, Product $product)
    {
        return view("catalog::admin.categories.products.fields.index", [
            'category' => $category,
            'product' => $product,
            'fields' => $category->getFieldsInfo(),
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
        return view("catalog::admin.categories.products.fields.create", [
            'category' => $category,
            'product' => $product,
            'fields' => $category->getFieldsInfo(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ProductFieldStoreRequest $request
     * @param Category $category
     * @param Product $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ProductFieldStoreRequest $request, Category $category, Product $product)
    {
        ProductField::create($request->all());
        return redirect()
            ->route('admin.category.product.field.index', ['category' => $category, 'product' => $product])
            ->with('success', 'Значение добавлено');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Category $category
     * @param Product $product
     * @param ProductField $field
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Category $category, Product $product, ProductField $field)
    {
        return view("catalog::admin.categories.products.fields.edit", [
            'category' => $category,
            'product' => $product,
            'field' => $field,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ProductFieldUpdateRequest $request
     * @param Category $category
     * @param Product $product
     * @param ProductField $field
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ProductFieldUpdateRequest $request, Category $category, Product $product, ProductField $field)
    {
        $field->update($request->all());
        return redirect()
            ->route('admin.category.product.field.index', [
                'category' => $category,
                'product' => $product,
            ])
            ->with('success', 'Значение обновлено');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Category $category
     * @param Product $product
     * @param ProductField $field
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Category $category, Product $product, ProductField $field)
    {
        $field->delete();
        return redirect()
            ->back()
            ->with('success', 'Значение характеристики удалено');
    }
}
