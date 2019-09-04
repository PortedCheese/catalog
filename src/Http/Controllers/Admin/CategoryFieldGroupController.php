<?php

namespace PortedCheese\Catalog\Http\Controllers\Admin;

use App\CategoryFieldGroup;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use PortedCheese\Catalog\Http\Requests\CategoryFieldGroupCreateRequest;
use PortedCheese\Catalog\Http\Requests\CategoryFieldGroupUpdateRequest;

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
     * @param  CategoryFieldGroupCreateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CategoryFieldGroupCreateRequest $request)
    {
        $userInput = $request->all();
        if (empty($userInput['machine'])) {
            $slug = Str::slug($userInput['title'], '-');
            $buf = $slug;
            $i = 1;
            while (CategoryFieldGroup::where('machine', $slug)->count()) {
                $slug = $buf . '-' . $i++;
            }
            $userInput['machine'] = $slug;
        }
        $group = CategoryFieldGroup::create($userInput);

        return redirect()
            ->route("admin.category.groups.show", ['group' => $group])
            ->with("success", "Группа добавлена");
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
            'group' => $group
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  CategoryFieldGroupUpdateRequest  $request
     * @param  \App\CategoryFieldGroup  $group
     * @return \Illuminate\Http\Response
     */
    public function update(CategoryFieldGroupUpdateRequest $request, CategoryFieldGroup $group)
    {
        $group->update($request->all());

        return redirect()
            ->back()
            ->with("success", "Обновлено");
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
                ->with("error", "Есть поля относящиется к данной группе");
        }
        $group->delete();
        return redirect()
            ->route("admin.category.groups.index")
            ->with("success", "Группа удалена");
    }
}
