
@extends('admin.layout')

@section('page-title', 'Добавить поле категории - ')
@section('header-title', "{$category->title}")

@section('admin')
    @include("catalog::admin.categories.pills", ['category' => $category])
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.category.field.store', ['category' => $category]) }}"
                      method="post">
                    @csrf
                    {{ debugbar()->info($errors) }}
                    <div class="form-group">
                        <label for="title">Заголовок</label>
                        <input type="text"
                               id="title"
                               name="title"
                               value="{{ old('title') }}"
                               required
                               class="form-control @error('title') is-invalid @enderror">
                        @error ('title')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    @if($available->count())
                        <div class="form-group">
                            <label for="exists">Выбрать из существующих</label>
                            <select name="exists"
                                    id="exists"
                                    class="form-control @error('exists') is-invalid @enderror">
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
                            @error ('exists')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    @endif

                    <div class="form-group">
                        <label for="type">Виджет поля</label>
                        <select name="type"
                                id="type"
                                class="form-control @error('type') is-invalid @enderror">
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
                        @error ('type')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="machine">Машинное имя</label>
                        <input type="text"
                               id="machine"
                               name="machine"
                               value="{{ old('machine') }}"
                               class="form-control @error('machine') is-invalid @enderror">
                        @error ('machine')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox"
                                   @if(old('filter'))
                                   checked
                                   @endif
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
                        <a href="{{ route('admin.category.show', ['category' => $category]) }}"
                           class="btn btn-secondary">
                            Категория
                        </a>
                        <button type="submit" class="btn btn-success">Добавить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
