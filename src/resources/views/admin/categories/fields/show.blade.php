@extends('admin.layout')

@section('page-title', 'Просмотр характеристики - ')
@section('header-title', "{$field->title}")

@section('admin')
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <form class="form-inline" action="{{ route("admin.category.all-fields.self-update", ['field' => $field]) }}" method="post">
                    @method('put')
                    @csrf()

                    <div class="form-group mb-2 mr-sm-2">
                        <label for="title" class="sr-only">Название</label>
                        <input type="text"
                               id="title"
                               name="title"
                               value="{{ old('title') ? old('title') : $field->title }}"
                               required
                               class="form-control{{ $errors->has('title') ? ' is-invalid' : '' }}">
                        @if ($errors->has('title'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('title') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="btn-group mb-2 mr-sm-2"
                         role="group">
                        <button type="submit" class="btn btn-success">Обновить заголовок</button>
                    </div>
                </form>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">Название</dt>
                    <dd class="col-sm-9">{{ $field->title }}</dd>

                    <dt class="col-sm-3">Тип</dt>
                    <dd class="col-sm-9">{{ $field->type_human }}</dd>

                    <dt class="col-sm-3">Машинное имя</dt>
                    <dd class="col-sm-9">{{ $field->machine }}</dd>
                </dl>
                <h3>Категори, в которых используется характеристика</h3>
                <ul>
                    @foreach($categories as $category)
                        <li>
                            <a href="{{ route('admin.category.show', ['category' => $category]) }}">
                                {{ $category->title }}
                            </a>
                        </li>
                    @endforeach
                </ul>
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
                    <a href="{{ route("admin.category.all-fields.list") }}" class="btn btn-dark">Список</a>
                </div>
            </div>
        </div>
    </div>
@endsection