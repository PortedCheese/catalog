@extends('admin.layout')

@section('page-title', 'Корзина - ')
@section('header-title', 'Корзина')

@section('admin')
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <a href="{{ route('admin.cart.index') }}" class="btn btn-dark">
                    К списку корзин
                </a>
            </div>
            <div class="card-body">
                @includeIf("catalog::site.cart.list", ['cart' => $cart])
            </div>
        </div>
    </div>
@endsection
