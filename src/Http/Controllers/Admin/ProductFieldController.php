<?php

namespace PortedCheese\Catalog\Http\Controllers\Admin;

use App\Category;
use App\Http\Controllers\Controller;
use App\Product;
use App\ProductField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductFieldController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->authorizeResource(ProductField::class, "field");
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
        return view("catalog::admin.categories.products.fields.index", [
            'category' => $category,
            'product' => $product,
            'fields' => $category->getFieldsInfoAdmin(),
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
            'fields' => $category->getFieldsInfoAdmin(),
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

        ProductField::create($request->all());
        return redirect()
            ->route('admin.category.product.field.index', ['category' => $category, 'product' => $product])
            ->with('success', 'Значение добавлено');
    }

    protected function storeValidator(array $data)
    {
        Validator::make($data, [
            "value" => ["required", "min:1"],
            "field_id" => ["required", "exists:category_fields,id"],
        ], [], [
            "value" => "Значение",
            "field_id" => "Характеристика",
        ])->validate();
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
     * @param Request $request
     * @param Category $category
     * @param Product $product
     * @param ProductField $field
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Category $category, Product $product, ProductField $field)
    {
        $this->updateValidator($request->all());

        $field->update($request->all());
        return redirect()
            ->route('admin.category.product.field.index', [
                'category' => $category,
                'product' => $product,
            ])
            ->with('success', 'Значение обновлено');
    }

    protected function updateValidator(array $data)
    {
        Validator::make($data, [
            'value' => ['required', 'min:1'],
        ], [], [
            'value' => 'Значение',
        ])->validate();
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
