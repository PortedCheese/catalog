@extends('admin.layout')

@section('page-title', 'Доступные характеристики - ')
@section('header-title', "Доступные характеристики")

@section('admin')
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route($currentRoute) }}" method="get" class="form-inline">
                    <label class="sr-only" for="title">Заголовок</label>
                    <input type="text"
                           class="form-control mb-2 mr-sm-2"
                           id="title"
                           placeholder="Заголовок"
                           value="{{ $query->get("title", "") }}"
                           name="title">

                    <button type="submit" class="btn btn-primary mb-2 mr-sm-2">Поиск</button>
                    <a href="{{ route($currentRoute) }}" class="btn btn-outline-secondary mb-2">Сбросить</a>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Заголовок</th>
                            <th>Тип</th>
                            <th>Машинное имя</th>
                            <th>Группа</th>
                            @can("view", \App\CategoryField::class)
                                <th>Действия</th>
                            @endcan
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($fields as $field)
                            <tr>
                                <td>{{ $field->title }}</td>
                                <td>{{ $field->type_human }}</td>
                                <td>{{ $field->machine }}</td>
                                <td>
                                    @if (! empty($field->group_id))
                                        {{ $groups[$field->group_id]->title }}
                                    @else
                                        Не задана
                                    @endif
                                </td>
                                @can("view", \App\CategoryField::class)
                                    <td>
                                        <a href="{{ route('admin.category.all-fields.show', ['field' => $field]) }}" class="btn btn-dark">
                                            <i class="far fa-eye"></i>
                                        </a>
                                    </td>
                                @endcan
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @if ($fields->lastPage() > 1)
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    {{ $fields->links() }}
                </div>
            </div>
        </div>
    @endif
@endsection
