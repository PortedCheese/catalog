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
                            <th>Действия</th>
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
                                <td>
                                    <confirm-delete-model-button model-id="{{ $field->id }}">
                                        <template slot="edit">
                                            <a href="{{ route('admin.category.field.edit', ['category' => $category, 'field' => $field]) }}" class="btn btn-primary">
                                                <i class="far fa-edit"></i>
                                            </a>
                                        </template>
                                        <template slot="delete">
                                            <form action="{{ route('admin.category.field.destroy', ['category' => $category, 'field' => $field]) }}"
                                                  id="delete-{{ $field->id }}"
                                                  class="btn-group"
                                                  method="post">
                                                @csrf
                                                <input type="hidden" name="_method" value="DELETE">
                                            </form>
                                        </template>
                                    </confirm-delete-model-button>
                                </td>
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
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="btn-group"
                     role="group">
                    <a href="{{ route('admin.category.field.create', ['category' => $category]) }}"
                       class="btn btn-success">
                        Добавить
                    </a>
                    <button type="button"
                            class="btn btn-warning"
                            data-toggle="tooltip"
                            data-placement="top"
                            onclick="event.preventDefault();document.getElementById('sycn-fields').submit();"
                            title="Добавить недостающие характеристики у дочерних категорий и обновить значения">
                        Синхронизировать
                    </button>
                </div>
                <div class="d-none">
                    <form id="sycn-fields"
                          action="{{ route('admin.category.field.sync', ['category' => $category]) }}"
                          method="post">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
