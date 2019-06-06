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
    @if (! $categories->count() && ! empty($products))
        <div class="col-12 d-block d-lg-none">
            <button type="button" class="btn btn-outline-primary mb-3" data-toggle="modal" data-target="#filterModal">
                Фильтры
            </button>

            <div class="modal slide dir-left" tabindex="-1" aria-hidden="true" role="dialog" id="filterModal">
                <div class="modal-dialog modal-dialog-centered ml-0 my-0" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4>Фильтры</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            @include("catalog::site.products.filters", [
                                'product' => $products,
                                'category' => $category,
                                'query' => $query,
                            ])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <div class="col-12">
        @if($categories->count())
            <div class="row">
                @foreach ($categories as $item)
                    <div class="col-12 col-md-6 col-lg-3 mb-3">
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

@if (!$categories->count() && ! empty($products))
    @section('sidebar')
        @include("catalog::site.products.filters", [
                            'product' => $products,
                            'category' => $category,
                            'query' => $query,
                        ])
    @endsection
@endif