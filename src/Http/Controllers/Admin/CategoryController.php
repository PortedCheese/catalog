<?php

namespace PortedCheese\Catalog\Http\Controllers\Admin;

use App\Category;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = $request->query;
        $view = $query->get('view', 'default');
        $parents = [];
        if ($view == 'tree') {
            $collection = Category::getTree();
        }
        else {
            $collection = Category::where('parent_id', null)
                ->orderBy('weight', 'desc')
                ->get();
            foreach ($collection as $item) {
                $parents[$item->id] = $item->title;
            }
        }
        return view('catalog::admin.categories.index', [
            'categories' => $collection,
            'parents' => $parents,
            'tree' => $view == 'tree',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Category|null $category
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Category $category = null)
    {
        return view("catalog::admin.categories.create", [
            'category' => $category,
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
        $userInput = $request->all();

        $this->storeValidator($userInput);

        if (empty($userInput['slug'])) {
            $slug = Str::slug($userInput['title'], '-');
            $buf = $slug;
            $i = 1;
            while (Category::where('slug', $slug)->count()) {
                $slug = $buf . '-' . $i++;
            }
            $userInput['slug'] = $slug;
        }
        $category = Category::create($userInput);
        $category->uploadMainImage($request);
        return redirect()
            ->route("admin.category.show", ['category' => $category])
            ->with('success', 'Категория успешно создана');
    }

    /**
     * Валидация сохранения.
     *
     * @param array $data
     */
    protected function storeValidator(array $data)
    {
        Validator::make($data, [
            "title" => ["required", "min:2", "unique:categories,title"],
            "slug" => ["nullable", "min:2", "unique:categories,slug"],
            "main_image" => ["nullable", "image"],
        ], [], [
            "title" => "Заголовок",
            "main_image" => "Главное изображение",
        ])->validate();
    }

    /**
     * Display the specified resource.
     *
     * @param Category $category
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Category $category)
    {
        return view("catalog::admin.categories.show", [
            'category' => $category,
            'image' => $category->image,
            'parent' => $category->parent,
            'parents' => $category->getParents(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Category $category
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Category $category)
    {
        return view("catalog::admin.categories.edit", [
            'category' => $category,
            'image' => $category->image,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Category $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Category $category)
    {
        $this->updateValidator($request->all(), $category);

        $userInput = $request->all();
        $category->update($userInput);
        $category->uploadMainImage($request);
        return redirect()
            ->route("admin.category.show", ['category' => $category])
            ->with('success', 'Категория успешно обновлена');
    }

    protected function updateValidator(array $data, Category $category)
    {
        $id = $category->id;
        Validator::make($data, [
            "title" => ["required", "min:2", "unique:categories,title,{$id}"],
            "slug" => ["min:2", "unique:categories,slug,{$id}"],
            "main_image" => ["nullable", "image"],
        ], [], [
            "title" => "Заголовок",
            "main_image" => "Главное изображение",
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Category $category
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Category $category)
    {
        $parent = $category->parent;
        if ($category->children->count()) {
            return redirect()
                ->back()
                ->with('danger', 'Невозможно удалить категорию, у нее есть подкатегории');
        }
        if ($category->products->count()) {
            return redirect()
                ->back()
                ->with('danger', 'Невозможно удалить категорию, у нее есть товары');
        }
        $category->delete();
        if ($parent) {
            return redirect()
                ->route('admin.category.show', ['category' => $parent])
                ->with('success', 'Категория успешно удалена');
        }
        else {
            return redirect()
                ->route('admin.category.index')
                ->with('success', 'Категория успешно удалена');
        }
    }

    /**
     * Удалить изображение.
     *
     * @param Category $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyImage(Category $category)
    {
        $category->clearMainImage();
        return redirect()
            ->back()
            ->with('success', 'Изображение удалено');
    }

    /**
     * Изменить родителя.
     *
     * @param Request $request
     * @param Category $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changeParent(Request $request, Category $category)
    {
        $parentId = $request->get('parent', null);
        if (empty($parentId)) {
            return redirect()
                ->back()
                ->with('danger', 'Родитель не может быть пустым');
        }
        if (is_numeric($parentId)) {
            $category->parent_id = $parentId;
        }
        elseif ($category->parent) {
            if ($parent = $category->parent->parent) {
                $category->parent_id = $parent->id;
            }
            else {
                $category->parent_id = NULL;
            }
        }
        $category->save();
        return redirect()
            ->back()
            ->with('success', 'Родитель изменен');
    }

    /**
     * Изменить вес.
     *
     * @param Request $request
     * @param Category $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changeWeight(Request $request, Category $category)
    {
        $weight = $request->get('weight', 0);
        if (! is_numeric($weight) || $weight < 0) {
            $weight = 0;
        }
        $category->weight = $weight;
        $category->save();
        return redirect()
            ->back()
            ->with('success', 'Вес изменен');
    }

    /**
     * Метатеги.
     *
     * @param Category $category
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function metas(Category $category)
    {
        return view("catalog::admin.categories.metas", [
            'category' => $category,
        ]);
    }
}
