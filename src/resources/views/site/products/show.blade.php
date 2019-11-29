@extends('layouts.boot')

@section('page-title', "{$product->title} - ")

@section('header-title', "{$product->title}")

@section('content')
    <div class="col-12 mt-2">
        <div class="row mb-3">
            <div class="col-12 col-md-4{{ $image ? '' : " text-center" }}">
                @if ($hasStates)
                    <div class="states text-left pl-3">
                        @foreach($states as $state)
                            <span class="badge badge-{{ $state->color }} px-3 py-2 mt-2">
                                {{ $state->title }}
                            </span>
                        @endforeach
                    </div>
                @endif
                @if ($image)
                    @image(['image' => $image, 'template' => 'large'])@endimage
                @else
                    <i class="far fa-image fa-9x"></i>
                @endif
            </div>
            <div class="product-variations col-12 col-md-8">
                @include("catalog::site.products.variations", [
                                'variations' => $variations,
                                'product' => $product,
                                'useCart' => $useCart,
                            ])
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
            @if (count($groups))
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
            @endif
            @if($gallery->count())
                <li class="nav-item">
                    <a href="#gallery"
                       class="nav-link"
                       id="gallery-tab"
                       data-toggle="tab"
                       role="tab"
                       aria-controls="gallery"
                       aria-selected="false">
                        Галерея
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
            @if (count($groups))
                <div class="tab-pane fade"
                     id="fields"
                     role="tabpanel"
                     aria-labelledby="fields-tab">
                    @foreach($groups as $group)
                        <table class="table table-striped table-hover table-sm">
                            @if ($group->model)
                                <thead>
                                <tr>
                                    <th colspan="2" class="border-0">
                                        <h5>{{ $group->title }}</h5>
                                    </th>
                                </tr>
                                </thead>
                            @endif
                            <tbody>
                            @foreach ($group->fields as $field)
                                <tr>
                                    <td class="w-25 border-0">{{ $field->title }}</td>
                                    <td class="w-75 border-0">{{ implode(', ', $field->values) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @endforeach
                </div>
            @endif
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
