<?php

namespace PortedCheese\Catalog\Http\Controllers\Admin;

use App\Category;
use App\CategoryField;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PortedCheese\Catalog\Events\CategoryFieldUpdate;
use PortedCheese\Catalog\Http\Requests\CategoryFieldCreateRequest;

class CategoryFieldController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Category $category)
    {
        return view("catalog::admin.categories.fields.index", [
            'category' => $category,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Category $category)
    {
        $types = CategoryField::TYPES;
        return view("catalog::admin.categories.fields.create", [
            'category' => $category,
            'types' => $types,
            'available' => CategoryField::getForCategory($category),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CategoryFieldCreateRequest $request, Category $category)
    {
        if (empty($request->get('exists'))) {
            $field = CategoryField::create($request->all());
        }
        else {
            $field = CategoryField::find($request->get('exists'));
        }
        $field->categories()->attach($category, [
            'title' => $request->get('title'),
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
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
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category, CategoryField $field)
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
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
