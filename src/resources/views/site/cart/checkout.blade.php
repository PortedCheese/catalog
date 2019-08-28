@extends('layouts.boot')

@section('page-title', "Оформление заказа - ")

@section('header-title', "Оформление заказа")

@section('content')
    <div class="col-12 col-md-8 col-lg-9 mb-3">
        <div class="card">
            <div class="card-header">
                Контактные данные
            </div>
            <div class="card-body">
                @if (! $user)
                    <blockquote class="blockquote mb-4">
                        <p class="mb-0">
                            <a href="#" data-toggle="modal" data-target="#LoginForm">Авторизуйтесь</a> на сайте, и мы сохраним всю информацию по заказу и автоматически заполним ваши контактные данные, иначе мы создадим заказ без привязки к Вашему личному кабинету
                        </p>
                        <button type="button" class="btn btn-primary mt-3" data-toggle="modal" data-target="#LoginForm">
                            Войти на сайт
                        </button>
                    </blockquote>
                    @includeIf("auth.login-modal")
                @endif
                <form action="{{ route('site.cart.order') }}" id="checkout-form" method="post">
                    @csrf
                    @if ($user)
                        <input type="hidden" name="user_id" value="{{ $user->id }}">
                        @if (! empty($user->full_name))
                            <input type="hidden" name="name" value="{{ $user->full_name }}">
                        @endif
                        <input type="hidden" name="email" value="{{ $user->email }}">
                    @endif

                    <div class="form-group">
                        <label for="name">Имя</label>
                        <input type="text"
                               id="name"
                               name="name"
                               @if (!empty($user->full_name))
                               disabled
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
                               disabled
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
                </form>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4 col-lg-3">
        <div class="card sticky-top" style="z-index: 900;">
            <div class="card-header">
                Ваш заказ
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    @foreach ($cart->getForRender() as $product)
                        @foreach ($product->items as $variation)
                            @php($border = ! ($loop->parent->last && $loop->last))
                            <li class="py-2 mb-2{{ $border ? ' border-bottom' : "" }}">
                                <span>{{ $product->title }} ({{ $variation->description }})</span>
                                <div>
                                    <span class="text-black-50">{{ $variation->quantity }} шт. x </span><b>{{ $variation->price }}</b> руб.
                                </div>
                            </li>
                        @endforeach
                    @endforeach
                </ul>
                <div class="h6">
                    Итого: <span class="text-primary"><span id="cart-total-side">{{ $cart->total }}</span> руб.</span>
                </div>
            </div>
            <div class="card-footer">
                <div class="btn-group btn-block"
                     role="group">
                    <a href="#"
                       onclick="event.preventDefault();document.getElementById('checkout-form').submit();"
                       class="btn btn-primary btn-block">
                        Оформить
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection