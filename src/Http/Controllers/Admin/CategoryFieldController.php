<?php

namespace PortedCheese\Catalog\Http\Controllers\Admin;

use App\Category;
use App\CategoryField;
use App\CategoryFieldGroup;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PortedCheese\Catalog\Events\CategoryFieldUpdate;
use PortedCheese\Catalog\Http\Requests\CategoryFieldCreateRequest;
use PortedCheese\Catalog\Http\Requests\CategoryFieldUpdateRequest;

class CategoryFieldController extends Controller
{
    /**
     * Список всех созданных характеристик.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function list(Request $request)
    {
        $fields = CategoryField::query()
            ->orderBy('category_fields.updated_at')
            ->paginate(20)->appends($request->input());
        $groups = [];
        foreach ($fields as $field) {
            if (! empty($field->group_id) && empty($groups[$field->group_id])) {
                $groups[$field->group_id] = $field->group;
            }
        }
        return view("catalog::admin.categories.fields.list", [
            'fields' => $fields,
            'groups' => $groups,
        ]);
    }

    /**
     * Просмотр.
     *
     * @param CategoryField $field
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(CategoryField $field)
    {
        $groups = CategoryFieldGroup::query()
            ->select(['id', 'title'])
            ->orderBy("weight")
            ->get();
        return view("catalog::admin.categories.fields.show", [
            'field' => $field,
            'categories' => $field->categories,
            'group' => $field->group,
            'groups' => $groups,
        ]);
    }

    /**
     * Обновить исходное поле.
     *
     * @param CategoryFieldUpdateRequest $request
     * @param CategoryField $field
     * @return \Illuminate\Http\RedirectResponse
     */
    public function selfUpdate(CategoryFieldUpdateRequest $request, CategoryField $field)
    {
        $field->update($request->all());
        return redirect()
            ->back()
            ->with("success", "Заголовок обновлен");
    }

    /**
     * Display a listing of the resource.
     *
     * @param Category $category
     * @return \Illuminate\Http\Response
     */
    public function index(Category $category)
    {
        $fields = $category->fields()
            ->orderBy('weight')
            ->get();
        $groups = [];
        foreach ($fields as $field) {
            if (! empty($field->group_id) && empty($groups[$field->group_id])) {
                $groups[$field->group_id] = $field->group;
            }
        }
        return view("catalog::admin.categories.fields.index", [
            'category' => $category,
            'fields' => $fields,
            'groups' => $groups,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Category $category
     * @return \Illuminate\Http\Response
     */
    public function create(Category $category)
    {
        $types = CategoryField::TYPES;
        return view("catalog::admin.categories.fields.create", [
            'category' => $category,
            'types' => $types,
            'available' => CategoryField::getForCategory($category),
            'groups' => CategoryFieldGroup::query()->orderBy("weight")->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CategoryFieldCreateRequest $request
     * @param Category $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CategoryFieldCreateRequest $request, Category $category)
    {
        if (empty($request->get('exists'))) {
            $field = CategoryField::create($request->all());
        }
        else {
            $field = CategoryField::find($request->get('exists'));
        }
        $title = ! empty($request->get('title')) ? $request->get("title") : $field->title;
        $field->categories()->attach($category, [
            'title' => $title,
            'filter' => $request->has('filter') ? 1 : 0
        ]);
        event(new CategoryFieldUpdate($category));
        return redirect()
            ->route('admin.category.field.index', ['category' => $category])
            ->with('success', 'Характеристика добавлена');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Category $category
     * @param CategoryField $field
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Category $category, CategoryField $field)
    {
        return view('catalog::admin.categories.fields.edit', [
            'category' => $category,
            'field' => $field,
            'pivot' => $field->categories()->find($category->id)->pivot,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param CategoryFieldUpdateRequest $request
     * @param Category $category
     * @param CategoryField $field
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(CategoryFieldUpdateRequest $request, Category $category, CategoryField $field)
    {
        $category->fields()
            ->updateExistingPivot($field->id, [
                'title' => $request->get('title'),
                'filter' => $request->has('filter') ? 1 : 0
            ]);
        event(new CategoryFieldUpdate($category));
        return redirect()
            ->route('admin.category.field.index', ['category' => $category])
            ->with('success', 'Успешно обновлено');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Category $category
     * @param CategoryField $field
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Category $category, CategoryField $field)
    {
        if ($field->values->where('category_id', $category->id)->count()) {
            return redirect()
                ->back()
                ->with('danger', 'У данной характеристики есть заполненные значения');
        }
        $category->fields()->detach($field);
        $field->checkCategoryOnDetach();
        event(new CategoryFieldUpdate($category));
        return redirect()
            ->route("admin.category.field.index", ['category' => $category])
            ->with('success', 'Поле успешно откреплено');
    }

    /**
     * Задать поля дочерним категориям.
     *
     * @param Category $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function syncChildren(Category $category)
    {
        $category->addChildFields();
        return redirect()
            ->back()
            ->with('success', 'Характеристики синхронизированны');
    }
}
