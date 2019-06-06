@extends('admin.layout')

@section('page-title', 'Корзина - ')
@section('header-title', 'Корзина')

@section('admin')
    <div class="col-12">
        <a href="{{ route('admin.cart.index') }}" class="btn btn-dark">
            К списку корзин
        </a>
        @includeIf("catalog::site.cart.list", ['cart' => $cart])
    </div>
@endsection
