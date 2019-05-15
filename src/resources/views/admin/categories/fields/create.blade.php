@extends('admin.layout')

@section('page-title', 'Добавить поле категории - ')
@section('header-title', "Добавить поле категории {$category->title}")

@section('admin')
    <div class="col-12">
        <form action="{{ route('admin.category.field.store', ['category' => $category]) }}"
              method="post">
            @csrf

            <div class="form-group">
                <label for="title">Заголовок</label>
                <input type="text"
                       id="title"
                       name="title"
                       value="{{ old('title') }}"
                       required
                       class="form-control{{ $errors->has('title') ? ' is-invalid' : '' }}">
                @if ($errors->has('title'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('title') }}</strong>
                    </span>
                @endif
            </div>

            @if($available->count())
                <div class="form-group">
                    <label for="exists">Выбрать из существующих</label>
                    <select name="exists"
                            id="exists"
                            class="form-control">
                        <option value="">--Выберите--</option>
                        @foreach($available as $field)
                            <option value="{{ $field->id }}"
                                    @if(old('exists'))
                                    selected
                                    @endif>
                                {{ $field->title }} | {{ $field->type }}
                            </option>
                        @endforeach
                    </select>
                    @if ($errors->has('exists'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('exists') }}</strong>
                        </span>
                    @endif
                </div>
            @endif

            <div class="form-group">
                <label for="type">Виджет поля</label>
                <select name="type"
                        id="type"
                        class="form-control">
                    <option value="">--Выберите--</option>
                    @foreach($types as $key => $value)
                        <option value="{{ $key }}"
                                @if(old('type'))
                                    selected
                                @endif>
                            {{ $value }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="machine">Машинное имя</label>
                <input type="text"
                       id="machine"
                       name="machine"
                       value="{{ old('machine') }}"
                       class="form-control{{ $errors->has('machine') ? ' is-invalid' : '' }}">
                @if ($errors->has('machine'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('machine') }}</strong>
                    </span>
                @endif
            </div>


            <div class="form-check">
                <input type="checkbox"
                       @if(old('filter'))
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
                <button type="submit" class="btn btn-success">Добавить</button>
            </div>
        </form>
    </div>
@endsection
