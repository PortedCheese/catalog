@extends('admin.layout')

@section('page-title', 'Редактировать поле категории - ')
@section('header-title', "{$pivot->title}")

@section('admin')
    @include("catalog::admin.categories.pills", ['category' => $category])
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.category.field.update', ['category' => $category, 'field' => $field]) }}"
                      method="post">
                    @csrf
                    @method('put')

                    <div class="form-group">
                        <label for="title">Заголовок</label>
                        <input type="text"
                               id="title"
                               name="title"
                               value="{{ old("title", $pivot->title) }}"
                               class="form-control @error("title") is-invalid @enderror">
                        @error("title")
                            <div class="invalid-feedback" role="alert">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="weight">Приоритет</label>
                        <input type="number"
                               step="1"
                               min="1"
                               id="weight"
                               name="weight"
                               value="{{ old("weight", $pivot->weight) }}"
                               class="form-control @error("weight") is-invalid @enderror">
                        @error("weight")
                            <div class="invalid-feedback" role="alert">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox"
                                   {{ (! count($errors->all()) && $pivot->filter) || old("filter") ? "checked" : "" }}
                                   class="custom-control-input"
                                   value=""
                                   name="filter"
                                   id="filter">
                            <label for="filter" class="custom-control-label">
                                Добавить в фильтр
                            </label>
                        </div>
                    </div>

                    <div class="btn-group"
                         role="group">
                        <button type="submit" class="btn btn-success">Обновить</button>
                    </div>
                    <small class="form-text text-muted">
                        Группа меняется для всех полей <a target="_blank" href="{{ route("admin.category.all-fields.show", ["field" => $field]) }}">этого</a> типа
                    </small>
                </form>
            </div>
        </div>
    </div>
@endsection
