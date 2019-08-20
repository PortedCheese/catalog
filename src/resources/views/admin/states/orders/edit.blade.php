@extends('admin.layout')

@section('page-title', 'Редактировать статус заказа - ')
@section('header-title', $state->title)

@section('admin')
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route("admin.order-state.update", ['state' => $state]) }}"
                      method="post">
                    @csrf
                    @method('put')

                    <div class="form-group">
                        <label for="title">Заголовок</label>
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

                    <div class="btn-group"
                         role="group">
                        <a href="{{ route('admin.order-state.index') }}"
                           class="btn btn-secondary">
                            Список
                        </a>
                        <button type="submit"
                                class="btn btn-success">
                            Обновить
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
