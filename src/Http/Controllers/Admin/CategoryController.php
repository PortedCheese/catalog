<?php

namespace PortedCheese\Catalog\Http\Controllers\Admin;

use App\Category;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
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
            $collection = Category::query()
                ->where('parent_id', null)
                ->orderBy('weight', 'asc')
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
     * Применить порядок.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeItemsWeight(Request $request)
    {
        if (! empty($request->get("items"))) {
            $items = $request->get("items");
            $this->setWeight($items, 0);
            return response()
                ->json("Порядок сохранен");
        }
        else {
            return response()
                ->json("Ошибка, недостаточно данных");
        }
    }

    /**
     * Установить вес.
     *
     * @param array $items
     * @param int $parent
     */
    private function setWeight(array $items, int $parent)
    {
        foreach ($items as $weight => $item) {
            if (! empty($item['children'])) {
                $this->setWeight($item['children'], $item['id']);
            }
            $id = $item['id'];
            $parentId = !empty($parent) ? $parent : NULL;
            DB::table("categories")
                ->where("id", $id)
                ->update(["weight" => $weight, "parent_id" => $parentId]);
        }
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
        $this->storeValidator($request->all());
        $category = Category::create($request->all());
        $category->uploadImage($request, "categories", "main_image");
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
            "title" => ["required", "min:2", "max:100", "unique:categories,title"],
            "slug" => ["nullable", "min:2", "max:100", "unique:categories,slug"],
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
        $category->update($request->all());
        $category->uploadImage($request, "categories", "main_image");
        return redirect()
            ->route("admin.category.show", ['category' => $category])
            ->with('success', 'Категория успешно обновлена');
    }

    /**
     * Валидация обновления.
     *
     * @param array $data
     * @param Category $category
     */
    protected function updateValidator(array $data, Category $category)
    {
        $id = $category->id;
        Validator::make($data, [
            "title" => ["required", "min:2", "max:100", "unique:categories,title,{$id}"],
            "slug" => ["nullable", "min:2", "max:100", "unique:categories,slug,{$id}"],
            "main_image" => ["nullable", "image"],
        ], [], [
            "title" => "Заголовок",
            "main_image" => "Главное изображение",
        ])->validate();
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
        $category->clearImage();
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
