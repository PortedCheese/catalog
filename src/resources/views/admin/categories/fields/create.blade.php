
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
                    <input type="hidden" name="weight" value="{{ $nextField }}">

                    @if($available->count())
                        <div class="form-group border border-primary p-2 bg-light rounded">
                            <label for="exists">Выбрать из существующих</label>
                            <select name="exists"
                                    id="exists"
                                    class="form-control @error('exists') is-invalid @enderror">
                                <option value="">--Выберите--</option>
                                @foreach($available as $field)
                                    <option value="{{ $field->id }}"
                                            {{ old("exists") == $field->id ? "selected" : "" }}>
                                        {{ $field->title }} | {{ $field->type_human }}
                                        @if (! empty($field->group_id))
                                            ({{ $field->group->title }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error ('exists')
                                <div class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>
                    @endif

                    <div class="form-group">
                        <label for="title">Заголовок</label>
                        <input type="text"
                               id="title"
                               name="title"
                               value="{{ old('title') }}"
                               class="form-control @error("title") is-invalid @enderror">
                        @error("title")
                            <div class="invalid-feedback" role="alert">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="type">Виджет поля</label>
                        <select name="type"
                                id="type"
                                class="custom-select @error('type') is-invalid @enderror">
                            <option value="">Выберите...</option>
                            @foreach($types as $key => $value)
                                <option value="{{ $key }}"
                                        {{ old("type") == $key ? "selected" : "" }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                        @error ('type')
                            <div class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </div>
                        @enderror
                    </div>

                    @if ($groups->count())
                        <div class="form-group">
                            <label for="group">Группа</label>
                            <select class="custom-select" name="group_id" id="group">
                                <option>Выберите...</option>
                                @foreach($groups as $group)
                                    <option value="{{ $group->id }}" {{ old("group_id") == $group->id ? "selected" : "" }}>
                                        {{ $group->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    @can("settings-management")
                        <div class="form-group">
                            <label for="machine">Машинное имя</label>
                            <input type="text"
                                   id="machine"
                                   name="machine"
                                   value="{{ old('machine') }}"
                                   class="form-control @error('machine') is-invalid @enderror">
                            @error ('machine')
                                <div class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>
                    @endcan

                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox"
                                   {{ old("filter") ? "checked" : "" }}
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
