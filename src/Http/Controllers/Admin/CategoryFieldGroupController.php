<?php

namespace PortedCheese\Catalog\Http\Controllers\Admin;

use App\CategoryFieldGroup;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CategoryFieldGroupController extends Controller
{
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

    protected function storeValidator(array $data)
    {
        Validator::make($data, [
            "title" => ["required", "min:2", "max:200"],
            "machine" => ["nullable", "min:2", "max:100", "unique:category_field_groups,machine"],
            "weight" => ["required", "numeric", "min:1"],
        ], [], [
            "title" => "Заголовок",
            "machine" => "Машинное имя",
            "weight" => "Вес",
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
        $this->updateValidatior($request->all());

        $group->update($request->all());

        return redirect()
            ->back()
            ->with("success", "Обновлено");
    }

    protected function updateValidatior(array $data)
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
