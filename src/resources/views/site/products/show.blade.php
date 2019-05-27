@extends('layouts.boot')

@section('page-title', "{$product->title} - ")

@section('header-title', "{$product->title}")

@section('content')
    <div class="col-12 mt-2">
        <div class="row mb-3">
            @php
                $class = $image ? "col-12 col-md-8" : "col-12";
            @endphp
            @if ($image)
                <div class="col-12 col-md-4">
                    @image(['image' => $image, 'template' => 'large'])@endimage
                </div>
            @endif
            <div class="product-variations {{ $class }}">
                @include("catalog::site.products.variations", ['variations' => $variations, 'product' => $product])

            </div>
        </div>
        <div class="clearfix"></div>
        <ul class="nav nav-tabs mb-3" id="product-more" role="tablist">
            <li class="nav-item">
                <a href="#description"
                   class="nav-link active"
                   id="description-tab"
                   data-toggle="tab"
                   role="tab"
                   aria-controls="description"
                   aria-selected="true">
                    Описание
                </a>
            </li>
            <li class="nav-item">
                <a href="#fields"
                   class="nav-link"
                   id="fields-tab"
                   data-toggle="tab"
                   role="tab"
                   aria-controls="fields"
                   aria-selected="false">
                    Характеристики
                </a>
            </li>
            @if($gallery->count())
                <li class="nav-item">
                    <a href="#gallery"
                       class="nav-link"
                       id="gallery-tab"
                       data-toggle="tab"
                       role="tab"
                       aria-controls="gallery"
                       aria-selected="false">
                        Галлерея
                    </a>
                </li>
            @endif
        </ul>
        <div class="tab-content" id="product-more-content">
            <div class="tab-pane fade show active"
                 id="description"
                 role="tabpanel"
                 aria-labelledby="description-tab">
                {!! $product->description !!}
            </div>
            <div class="tab-pane fade"
                 id="fields"
                 role="tabpanel"
                 aria-labelledby="fields-tab">
                <dl class="row">
                    @foreach ($fields as $field)
                        <dt class="col-sm-3">{{ $field->title }}</dt>
                        <dd class="col-sm-9">{{ implode(', ', $field->values) }}</dd>
                    @endforeach
                </dl>
            </div>
            @if($gallery->count())
                <div class="tab-pane fade"
                     id="gallery"
                     role="tabpanel"
                     aria-labelledby="gallery-tab">
                    @gallery([
                        'gallery' => $gallery,
                        'lightbox' => 'products',
                        'template' => 'medium'
                    ])@endgallery
                </div>
            @endif
        </div>
    </div>
@endsection
