@extends('layouts.boot')

@section('page-title', "Корзина - ")

@section('header-title', "Корзина")

@section('content')
    @if ($cart && $cart->getCount())
        <div class="col-12 col-md-8 col-lg-9 mb-3">
            <div class="card">
                <div class="card-header">
                    Корзина
                </div>
                <div class="card-body">
                    @include("catalog::site.cart.list", ['cart' => $cart])
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4 col-lg-3">
            <div class="card sticky-top" style="z-index: 900;">
                <div class="card-header h6">
                    Итого: <span class="text-primary"><span id="cart-total-side">{{ $cart->total }}</span> руб.</span>
                </div>
                <div class="card-body">
                    <div class="btn-group btn-block"
                         role="group">
                        <a href="{{ route('site.cart.checkout') }}"
                           class="btn btn-primary btn-block">
                            Оформить
                        </a>
                    </div>
                </div>
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