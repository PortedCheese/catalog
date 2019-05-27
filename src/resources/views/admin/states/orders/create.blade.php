@extends('admin.layout')

@section('page-title', 'Добавить статус заказа - ')
@section('header-title', 'Добавить статус заказа')

@section('admin')
    <div class="col-12">
        <form action="{{ route("admin.order-state.store") }}"
              method="post">
            @csrf

            @if ($default)
                <input type="hidden" name="machine" value="{{ $default }}">
            @else
                <div class="form-group">
                    <label for="machine">Ключ</label>
                    <input type="text"
                           id="machine"
                           name="machine"
                           value="{{ old('machine') }}"
                           required
                           class="form-control{{ $errors->has('machine') ? ' is-invalid' : '' }}">
                    @if ($errors->has('machine'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('machine') }}</strong>
                        </span>
                    @endif
                </div>
            @endif

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

            <div class="btn-group"
                 role="group">
                <a href="{{ route('admin.order-state.index') }}"
                   class="btn btn-secondary">
                    Список
                </a>
                <button type="submit"
                        class="btn btn-success">
                    Добавить
                </button>
            </div>
        </form>
    </div>
@endsection
