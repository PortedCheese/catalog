<?php

namespace PortedCheese\Catalog\Http\Controllers\Site;

use App\Category;
use App\Http\Controllers\Controller;
use App\Product;
use App\ProductVariation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PortedCheese\Catalog\Http\Services\ProductFilterService;

class CatalogController extends Controller
{

    /**
     * Категории верхнего уровня.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $collection = Category::where('parent_id', null)
            ->orderBy('weight', 'desc')
            ->get();
        return view("catalog::site.categories.index", [
            'categories' => $collection,
            'category' => null,
            'siteBreadcrumb' => [
                (object) [
                    'active' => true,
                    'url' => route(Category::PAGE_ROUTE),
                    'title' => Category::PAGE_NAME,
                ],
            ],
        ]);
    }

    /**
     * Просмотр категории.
     *
     * @param Category $category
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showCategory(Request $request, Category $category)
    {
        $categories = $category->children;
        $products = NULL;
        $filters = [];

        if (! $categories->count()) {
            $filter = new ProductFilterService($request, $category);
            $products = $filter->makeFilter();
            $filters = $category->getFilters();
        }

        return view("catalog::site.categories.index", [
            'categories' => $categories,
            'products' => $products,
            'filters' => $filters,
            'category' => $category,
            'siteBreadcrumb' => $category->getSiteBreadcrumb(),
            'query' => $request->query,
        ]);
    }

    /**
     * Просмотр товара.
     *
     * @param Category $category
     * @param Product $product
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showProduct(Category $category, Product $product)
    {
        if (!$product->published) {
            abort(404);
        }
        $collection = ProductVariation::query()
            ->where('product_id', $product->id)
            ->orderByDesc('available')
            ->orderBy('price')
            ->get();
        $variations = [];
        foreach ($collection as $item) {
            $variations[] = $item;
        }
        $states = $product->states;
        $data = [
            'category' => $category,
            'product' => $product,
            'siteBreadcrumb' => $category->getSiteBreadcrumb(true),
            'image' => $product->image,
            'fields' => $product->getFieldsInfo($category),
            'variations' => $variations,
            'gallery' => $product->images,
            'useCart' => siteconf()->get('catalog.useCart'),
            'hasStates' => $states->count(),
            'states' => $states,
        ];
        return view('catalog::site.products.show', $data);
    }
}
