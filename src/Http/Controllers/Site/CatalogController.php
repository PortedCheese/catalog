<?php

namespace PortedCheese\Catalog\Http\Controllers\Site;

use App\Category;
use App\Http\Controllers\Controller;
use App\Product;
use App\ProductVariation;
use Illuminate\Http\Request;
use PortedCheese\Catalog\Http\Services\ProductFilterService;
use PortedCheese\SeoIntegration\Models\Meta;

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
            'pageMetas' => Meta::getByPageKey('catalog'),
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
     * @param Request $request
     * @param Category $category
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showCategory(Request $request, Category $category)
    {
        $categories = $category->children;

        $filter = new ProductFilterService($request, $category);
        $products = $filter->makeFilter();
        $filters = $category->getFilters(true);

        return view("catalog::site.categories.index", [
            'categories' => $categories,
            'products' => $products,
            'filters' => $filters,
            'category' => $category,
            'siteBreadcrumb' => $category->getSiteBreadcrumb(),
            'query' => $request->query,
            'pageMetas' => Meta::getByModelKey($category),
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

        $states = $product->states;
        $data = [
            'category' => $category,
            'product' => $product,
            'siteBreadcrumb' => $category->getSiteBreadcrumb(true),
            'image' => $product->image,
            'fields' => $product->getFieldsInfo($category),
            'groups' => $product->getGroupedFieldsInfo($category),
            'variations' => ProductVariation::getByProductIdForRender($product->id),
            'gallery' => $product->images()->orderBy("weight")->get(),
            'useCart' => siteconf()->get('catalog', "useCart"),
            'hasStates' => $states->count(),
            'states' => $states,
            'pageMetas' => Meta::getByModelKey($product),
        ];
        return view('catalog::site.products.show', $data);
    }
}
