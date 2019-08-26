<?php

namespace PortedCheese\Catalog\Http\Controllers\Admin;

use App\Category;
use App\Http\Controllers\Controller;
use App\Product;
use App\ProductState;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PortedCheese\Catalog\Http\Requests\ProductStoreRequest;
use PortedCheese\Catalog\Http\Requests\ProductUpdateCategoryRequest;
use PortedCheese\Catalog\Http\Requests\ProductUpdateRequest;

class ProductController extends Controller
{
    const PAGER = 20;

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param Category $category
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request, Category $category = null)
    {
        $query = $request->query;
        $products = Product::query();
        if (!empty($category)) {
            $products->where('category_id', $category->id);
            $formRoute = route('admin.category.product.index', ['category' => $category]);
        }
        else {
            $formRoute = route('admin.product.index');
        }
        if ($query->get('title')) {
            $title = trim($query->get('title'));
            $products->where('title', 'LIKE', "%$title%");
        }
        if ($query->has('published')) {
            $value = $query->get('published', 'all');
            if (in_array($value, ['1', '0'])) {
                $products->where('published', '=', $query->get('published'));
            }
        }
        $products->orderBy('created_at', 'desc');
        $perPage = env("CATALOG_PRODUCT_ADMIN_PAGER", self::PAGER);
        return view('catalog::admin.categories.products.index', [
            'category' => $category,
            'products' => $products->paginate($perPage)->appends($request->input()),
            'query' => $query,
            'formRoute' => $formRoute,
            'categories' => [],
            'all' => empty($category),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Category $category
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Category $category)
    {
        return view("catalog::admin.categories.products.create", [
            'category' => $category,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ProductStoreRequest $request
     * @param Category $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ProductStoreRequest $request, Category $category)
    {
        $userInput = $request->all();
        if (empty($userInput['slug'])) {
            $slug = Str::slug($userInput['title'], '-');
            $buf = $slug;
            $i = 1;
            while (Product::where('slug', $slug)->count()) {
                $slug = $buf . '-' . $i++;
            }
            $userInput['slug'] = $slug;
        }
        $product = Product::create($userInput);
        $product->uploadMainImage($request);
        return redirect()
            ->route("admin.category.product.show", ['category' => $category, 'product' => $product])
            ->with('success', 'Товар успешно добавлен');
    }

    /**
     * Display the specified resource.
     *
     * @param Category $category
     * @param Product $product
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Category $category, Product $product)
    {
        $categories = [];
        foreach (Category::all()->sortBy('title') as $item) {
            $categories[$item->id] = $item->title;
        }
        return view("catalog::admin.categories.products.show", [
            'category' => $category,
            'product' => $product,
            'categories' => $categories,
            'image' => $product->image,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Category $category
     * @param Product $product
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Category $category, Product $product)
    {
        $productStateIds = [];
        foreach ($product->states as $state) {
            $productStateIds[] = $state->id;
        }
        return view("catalog::admin.categories.products.edit", [
            'category' => $category,
            'product' => $product,
            'image' => $product->image,
            'states' => ProductState::all(),
            'productStateIds' => $productStateIds,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ProductUpdateRequest $request
     * @param Category $category
     * @param Product $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ProductUpdateRequest $request, Category $category, Product $product)
    {
        $userInput = $request->all();
        if (empty($userInput['slug'])) {
            $slug = Str::slug($userInput['title'], '-');
            $buf = $slug;
            $i = 1;
            while (Product::where('slug', $slug)->count()) {
                $slug = $buf . '-' . $i++;
            }
            $userInput['slug'] = $slug;
        }
        $product->update($userInput);
        $product->uploadMainImage($request);
        // Обновляем метки.
        $stateIds = [];
        foreach ($userInput as $key => $value) {
            if (strstr($key, 'check-') !== FALSE) {
                $stateIds[] = $value;
            }
        }
        $product->states()->sync($stateIds);
        return redirect()
            ->route("admin.category.product.show", ['category' => $product->category, 'product' => $product])
            ->with('success', 'Товар успешно обновлен');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Category $category
     * @param Product $product
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Category $category, Product $product)
    {
        if ($count = $product->orderItems->count()) {
            return redirect()
                ->back()
                ->with('danger', "Товар нахотидся в {$count} заказах");
        }
        $product->delete();

        return redirect()
            ->back()
            ->with('success', 'Товар удален');
    }

    /**
     * Удалить главное изображение.
     *
     * @param Category $category
     * @param Product $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyImage(Category $category, Product $product)
    {
        $product->clearMainImage();
        return redirect()
            ->back()
            ->with('success', 'Изображение удалено');
    }

    /**
     * Изменить статус публикации.
     *
     * @param Category $category
     * @param Product $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function published(Category $category, Product $product)
    {
        $product->published = !$product->published;
        $product->save();
        return redirect()
            ->back()
            ->with('success', 'Статус публикации изменен');
    }

    /**
     * Изменить категорию товара.
     *
     * @param ProductUpdateCategoryRequest $request
     * @param Category $category
     * @param Product $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changeCategory(ProductUpdateCategoryRequest $request, Category $category, Product $product)
    {
        $product->category_id = $request->get('category_id');
        $product->save();
        return redirect()
            ->route("admin.category.product.show", ['category' => $product->category, 'product' => $product])
            ->with('success', 'Категория изменена');
    }

    /**
     * Метатеги.
     *
     * @param Category $category
     * @param Product $product
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function metas(Category $category, Product $product)
    {
        return view("catalog::admin.categories.products.metas", [
            'category' => $category,
            'product' => $product
        ]);
    }

    /**
     * Галлерея.
     *
     * @param Category $category
     * @param Product $product
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function gallery(Category $category, Product $product)
    {
        return view("catalog::admin.categories.products.gallery", [
            'category' => $category,
            'product' => $product,
        ]);
    }
}
