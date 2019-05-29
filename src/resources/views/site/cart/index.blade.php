@extends('layouts.boot')

@section('page-title', "Корзина - ")

@section('header-title', "Корзина")

@section('content')
    @if ($cart && $cart->getCount())
        <div class="col-9">
            @include("catalog::site.cart.table", ['cart' => $cart])
        </div>
        <div class="col-3">
            <div class="btn-group"
                 role="group">
                <a href="{{ route('site.cart.checkout') }}"
                   class="btn btn-primary">
                    Оформить
                </a>
            </div>
        </div>
    @else
        <div class="col-12">
            <p class="lead">
                Корзина пуста, <a href="{{ route('site.catalog.index') }}">начать покупки</a>
            </p>
        </div>
    @endif
@endsection