@extends('admin.layout')

@section('page-title', 'Добавить метку - ')
@section('header-title', 'Добавить метку')

@section('admin')
    <div class="col-12">
        <form action="{{ route('admin.product-state.store') }}"
              method="post">
            @csrf

            <div class="form-group">
                <label for="title">Заголовк</label>
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

            <div class="form-group">
                <label for="slug">Slug</label>
                <input type="text"
                       id="slug"
                       name="slug"
                       value="{{ old('slug') }}"
                       class="form-control">
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
                                @elseif($value == 'secondary')
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
                <button type="submit" class="btn btn-success">Добавить</button>
            </div>
        </form>
    </div>
@endsection
