@extends('layouts.boot')

@section('page-title', "Оформление - ")

@section('header-title', "Оформление")

@section('content')
    <div class="col-12">
        <form action="{{ route('site.cart.order') }}" method="post">
            @csrf

            <div class="form-group">
                <label for="name">Имя</label>
                <input type="text"
                       id="name"
                       name="name"
                       @if ($user)
                       value="{{ $user->full_name }}"
                       @endif
                       class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}">
                @if ($errors->has('name'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('name') }}</strong>
                    </span>
                @endif
            </div>


            <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email"
                       id="email"
                       name="email"
                       @if ($user)
                       value="{{ $user->email }}"
                       @endif
                       class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}">
                @if ($errors->has('email'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group">
                <label for="phone">Телефон</label>
                <input type="text"
                       id="phone"
                       name="phone"
                       class="form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}">
                @if ($errors->has('phone'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('phone') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group">
                <label for="comment">Комметарий</label>
                <textarea class="form-control" name="comment" id="comment" rows="3"></textarea>
            </div>

            <div class="btn-group"
                 role="group">
                <a class="btn btn-secondary" href="{{ route('site.cart.index') }}">Корзина</a>
                <button type="submit" class="btn btn-success">Заказать</button>
            </div>
        </form>
    </div>
@endsection