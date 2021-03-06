<?php

namespace PortedCheese\Catalog\Http\Controllers\Admin;

use App\CategoryFieldGroup;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CategoryFieldGroupController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->authorizeResource(CategoryFieldGroup::class, "group");
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $groups = CategoryFieldGroup::query()
            ->orderBy('weight')
            ->paginate(20)->appends($request->input());
        return view("catalog::admin.categories.groups.index", [
            'groups' => $groups,
        ]);
    }

    public function priority()
    {
        $this->authorize("update", CategoryFieldGroup::class);
        $groups = [];
        $collection = CategoryFieldGroup::query()
            ->orderBy("weight")
            ->get();
        foreach ($collection as $item) {
            $groups[] = [
                "name" => $item->title,
                "id" => $item->id,
            ];
        }
        return view("catalog::admin.categories.groups.priority", [
            'groups' => $groups,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $weight = CategoryFieldGroup::all()->count() + 1;
        return view("catalog::admin.categories.groups.create", [
            'weight' => $weight,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->storeValidator($request->all());
        $group = CategoryFieldGroup::create($request->all());

        return redirect()
            ->route("admin.category.groups.show", ['group' => $group])
            ->with("success", "Группа добавлена");
    }

    /**
     * Валидация сохранения.
     *
     * @param array $data
     */
    protected function storeValidator(array $data)
    {
        Validator::make($data, [
            "title" => ["required", "min:2", "max:200"],
            "machine" => ["nullable", "min:2", "max:100", "unique:category_field_groups,machine"],
        ], [], [
            "title" => "Заголовок",
            "machine" => "Машинное имя",
        ])->validate();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CategoryFieldGroup  $group
     * @return \Illuminate\Http\Response
     */
    public function show(CategoryFieldGroup $group)
    {
        return view("catalog::admin.categories.groups.show", [
            'group' => $group,
            'fields' => $group->fields()->orderBy('title')->get()
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  \App\CategoryFieldGroup  $group
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CategoryFieldGroup $group)
    {
        $this->updateValidator($request->all());

        $group->update($request->all());

        return redirect()
            ->back()
            ->with("success", "Обновлено");
    }

    /**
     * Валидация обновления.
     *
     * @param array $data
     */
    protected function updateValidator(array $data)
    {
        Validator::make($data, [
            "title" => ["required", "min:2", "max:200"],
        ], [], [
            "title" => "Заголовок",
        ])->validate();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param CategoryFieldGroup $group
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(CategoryFieldGroup $group)
    {
        if ($group->fields->count()) {
            return redirect()
                ->back()
                ->with("danger", "Есть поля относящиется к данной группе");
        }
        $group->delete();
        return redirect()
            ->route("admin.category.groups.index")
            ->with("success", "Группа удалена");
    }
}
