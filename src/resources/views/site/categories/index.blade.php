@extends('layouts.boot')

@section('page-title', "Каталог - ")

@section('header-title')
    @if ($category)
        {{ $category->title }}
    @else
        Каталог
    @endif
@endsection

@section('content')
    <div class="col-12">
        @if($categories->count())
            <div class="row">
                @foreach ($categories as $item)
                    <div class="col-3 mb-3">
                        {!! $item->getTeaser() !!}
                    </div>
                @endforeach
            </div>
        @else
            @isset($products)
                @if ($products->count())
                    @include("catalog::site.products.index", ['products' => $products])
                @endif
            @endisset
        @endif
    </div>
@endsection

@if (!$categories->count())
    @isset($products)
        @section('sidebar')
            @include("catalog::site.products.filters", [
                                'product' => $products,
                                'category' => $category,
                                'query' => $query,
                            ])
        @endsection
    @endisset
@endif