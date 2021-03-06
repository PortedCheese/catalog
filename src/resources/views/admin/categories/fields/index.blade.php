@extends('admin.layout')

@section('page-title', 'Характеристики категории - ')
@section('header-title', "{$category->title}")

@section('admin')
    @include("catalog::admin.categories.pills", ['category' => $category])
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Заголовок</th>
                            <th>Тип</th>
                            <th>В фильтре</th>
                            <th>Группа</th>
                            <th>Приоритет</th>
                            @canany(["update", "delete"], \App\CategoryField::class)
                                <th>Действия</th>
                            @endcan
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($fields as $field)
                            <tr>
                                <td>{{ $field->pivot->title }}</td>
                                <td>{{ $field->type }}</td>
                                <td>
                                    {{ $field->pivot->filter ? "Да" : "Нет" }}
                                </td>
                                <td>
                                    @if (! empty($field->group_id))
                                        {{ $groups[$field->group_id]->title }}
                                    @else
                                        Не задана
                                    @endif
                                </td>
                                <td>
                                    {{ $field->pivot->weight }}
                                </td>
                                @canany(["update", "delete"], $field)
                                    <td>
                                        <div role="toolbar" class="btn-toolbar">
                                            <div class="btn-group mr-1">
                                                @can("update", $field)
                                                    <a href="{{ route("admin.category.field.edit", ['category' => $category, "field" => $field]) }}" class="btn btn-primary">
                                                        <i class="far fa-edit"></i>
                                                    </a>
                                                @endcan
                                                @can("delete", $field)
                                                    <button type="button" class="btn btn-danger" data-confirm="{{ "delete-form-{$field->id}" }}">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                @endcan
                                            </div>
                                        </div>
                                        @can("delete", $field)
                                            <confirm-form :id="'{{ "delete-form-{$field->id}" }}'">
                                                <template>
                                                    <form action="{{ route('admin.category.field.destroy', ['category' => $category, 'field' => $field]) }}"
                                                          id="delete-form-{{ $field->id }}"
                                                          class="btn-group"
                                                          method="post">
                                                        @csrf
                                                        <input type="hidden" name="_method" value="DELETE">
                                                    </form>
                                                </template>
                                            </confirm-form>
                                        @endcan
                                    </td>
                                @endcanany
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('links')
    @canany(["create", "update"], \App\CategoryField::class)
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="btn-group"
                         role="group">
                        @can("create", \App\CategoryField::class)
                            <a href="{{ route('admin.category.field.create', ['category' => $category]) }}"
                               class="btn btn-success">
                                Добавить
                            </a>
                        @endcan
                        @can("update", \App\CategoryField::class)
                            <button type="button"
                                    class="btn btn-warning"
                                    data-toggle="tooltip"
                                    data-placement="top"
                                    onclick="event.preventDefault();document.getElementById('sycn-fields').submit();"
                                    title="Добавить недостающие характеристики у дочерних категорий и обновить значения">
                                Синхронизировать
                            </button>
                        @endcan
                    </div>
                    @can("update", \App\CategoryField::class)
                        <div class="d-none">
                            <form id="sycn-fields"
                                  action="{{ route('admin.category.field.sync', ['category' => $category]) }}"
                                  method="post">
                                @csrf
                            </form>
                        </div>
                    @endcan
                </div>
            </div>
        </div>
    @endcanany
@endsection
