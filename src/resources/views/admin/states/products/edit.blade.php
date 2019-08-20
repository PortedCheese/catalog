@extends('admin.layout')

@section('page-title', 'Редакторовать метку - ')
@section('header-title', "Редактировать метку {$state->title}")

@section('admin')
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.product-state.update', ['state' => $state]) }}"
                      method="post">
                    @csrf
                    @method('put')

                    <div class="form-group">
                        <label for="title">Заголовк</label>
                        <input type="text"
                               id="title"
                               name="title"
                               value="{{ old('title') ? old('title') : $state->title }}"
                               required
                               class="form-control{{ $errors->has('title') ? ' is-invalid' : '' }}">
                        @if ($errors->has('title'))
                            <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('title') }}</strong>
                    </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="slug">Slug</label>
                        <input type="text"
                               id="slug"
                               name="slug"
                               value="{{ old('slug') ? old('slug') : $state->slug }}"
                               class="form-control{{ $errors->has('slug') ? ' is-invalid' : '' }}">
                        @if ($errors->has('slug'))
                            <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('slug') }}</strong>
                    </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="color">Цвет</label>
                        <select name="color"
                                id="color"
                                class="form-control custom-select">
                            @foreach($colors as $value)
                                <option value="{{ $value }}"
                                        @if(old('color'))
                                        selected
                                        @elseif($value == $state->color)
                                        selected
                                        @endif>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        @foreach ($colors as $color)
                            <span class="badge badge-pill badge-{{ $color }}">
                        {{ $color }}
                    </span>
                        @endforeach
                    </div>

                    <div class="btn-group"
                         role="group">
                        <button type="submit" class="btn btn-success">Обновить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
