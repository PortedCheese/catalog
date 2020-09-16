<?php

namespace PortedCheese\Catalog\Http\Controllers\Admin;

use App\Category;
use App\CategoryField;
use App\CategoryFieldGroup;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PortedCheese\Catalog\Events\CategoryFieldUpdate;

class CategoryFieldController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->authorizeResource(CategoryField::class, "field");
    }

    /**
     * Список всех созданных характеристик.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function list(Request $request)
    {
        $this->authorize("viewAny", CategoryField::class);
        $query = CategoryField::query();

        if ($title = $request->get("title", false)) {
            $query->where("title", "LIKE", "%$title%");
        }

        $fields = $query->orderBy('title')
            ->paginate()
            ->appends($request->input());

        $groups = [];
        foreach ($fields as $field) {
            if (! empty($field->group_id) && empty($groups[$field->group_id])) {
                $groups[$field->group_id] = $field->group;
            }
        }
        return view("catalog::admin.categories.fields.list", [
            'fields' => $fields,
            'groups' => $groups,
            "query" => $request->query,
        ]);
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
            'nextField' => $category->fields()->count() + 1,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param Category $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Category $category)
    {
        $this->storeValidation($request->all());

        if (empty($request->get('exists'))) {
            $field = CategoryField::create($request->all());
        }
        else {
            $field = CategoryField::find($request->get('exists'));
        }

        $title = ! empty($request->get('title')) ? $request->get("title") : $field->title;
        $field->categories()->attach($category, [
            'title' => $title,
            'filter' => $request->has('filter') ? 1 : 0,
            'weight' => $request->get("weight", 1)
        ]);

        event(new CategoryFieldUpdate($category));

        return redirect()
            ->route('admin.category.field.index', ['category' => $category])
            ->with('success', 'Характеристика добавлена');
    }

    protected function storeValidation(array $data)
    {
        Validator::make($data, [
            "title" => ["nullable", "required_without:exists", "min:2", "max:200"],
            "exists" => ["nullable", "required_without_all:machine,type,title", "exists:category_fields,id"],
            "type" => ["nullable", "required_without:exists"],
            "machine" => ["nullable", "max:100", "unique:category_fields,machine"],
            "weight" => ["nullable", "numeric", "min:1"],
        ], [], [
            "title" => "Заголовок",
            "exists" => "Существующие",
            "type" => "Тип",
            "machine" => "Машинное имя",
            "weight" => "Вес",
        ])->validate();
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
            "types" => CategoryField::TYPES,
        ]);
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
            'nextField' => $category->fields()->count() + 1,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Category $category
     * @param CategoryField $field
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Category $category, CategoryField $field)
    {
        $this->updateValidator($request->all());

        $category->fields()
            ->updateExistingPivot($field->id, [
                'title' => $request->get('title'),
                'filter' => $request->has('filter') ? 1 : 0,
                'weight' => $request->get("weight", 1)
            ]);
        event(new CategoryFieldUpdate($category));
        return redirect()
            ->route('admin.category.field.index', ['category' => $category])
            ->with('success', 'Успешно обновлено');
    }

    protected function updateValidator(array $data)
    {
        Validator::make($data, [
            "title" => ["required", "min:2", "max:200"],
            "weight" => ["required", "numeric", "min:1"],
        ], [], [
            "title" => "Заголовок",
            "weight" => "Вес",
        ])->validate();
    }

    /**
     * Обновить исходное поле.
     *
     * @param Request $request
     * @param CategoryField $field
     * @return \Illuminate\Http\RedirectResponse
     */
    public function selfUpdate(Request $request, CategoryField $field)
    {
        $this->selfUpdateValidator($request->all());

        $field->update($request->all());
        $categories = $field->categories;
        if ($categories->count()) {
            foreach ($categories as $category) {
                event(new CategoryFieldUpdate($category));
            }
        }
        return redirect()
            ->back()
            ->with("success", "Обновлено");
    }

    protected function selfUpdateValidator(array $data)
    {
        Validator::make($data, [
            "title" => ["required", "min:2", "max:200"],
            "weight" => ["required", "numeric", "min:1"],
            "type" => ["required"],
        ], [], [
            "title" => "Заголовок",
            "weight" => "Вес",
            "type" => "Виджет поля",
        ])->validate();
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
