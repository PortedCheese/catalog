@extends('admin.layout')

@section('page-title', 'Редактировать поле категории - ')
@section('header-title', "Редактировать поле {$pivot->title} категории {$category->title}")

@section('admin')
    <div class="col-12">
        <form action="{{ route('admin.category.field.update', ['category' => $category, 'field' => $field]) }}"
              method="post">
            @csrf
            @method('put')

            <div class="form-group">
                <label for="title">Заголовок</label>
                <input type="text"
                       id="title"
                       name="title"
                       value="{{ old('title') ? old('title') : $pivot->title }}"
                       required
                       class="form-control{{ $errors->has('title') ? ' is-invalid' : '' }}">
                @if ($errors->has('title'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('title') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-check">
                <input type="checkbox"
                       @if (old('filter'))
                               checked
                       @elseif ($pivot->filter)
                               checked
                       @endif
                       class="form-check-input"
                       value=""
                       name="filter"
                       id="filter">
                <label for="filter">
                    Добавить в фильтр
                </label>
            </div>

            <div class="btn-group"
                 role="group">
                <button type="submit" class="btn btn-success">Обновить</button>
            </div>
        </form>
    </div>
@endsection
