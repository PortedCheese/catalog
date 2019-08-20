@extends('admin.layout')

@section('page-title', 'Просмотр - ')
@section('header-title', "{$product->title}")

@section('admin')
    @include("catalog::admin.categories.products.pills", ['category' => $category, 'product' => $product])
    <div class="col-12 mt-2">
        <div class="row">
            @php
                $class = $image ? "col-12 col-md-9" : "col-12";
            @endphp
            @if ($image)
                <div class="col-12 col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <img src="{{ route('imagecache', ['template' => 'medium', 'filename' => $image->file_name]) }}"
                                 class="img-thumbnail"
                                 alt="{{ $image->name }}">
                            <confirm-delete-model-button model-id="{{ $product->id }}">
                                <template slot="delete">
                                    <form action="{{ route('admin.category.product.destroy-image', [
                                                'category' => $category,
                                                'product' => $product
                                            ]) }}"
                                          id="delete-{{ $product->id }}"
                                          class="btn-group"
                                          method="post">
                                        @csrf
                                        <input type="hidden" name="_method" value="DELETE">
                                    </form>
                                </template>
                            </confirm-delete-model-button>
                        </div>
                    </div>
                </div>
            @endif
            <div class="product-description {{ $class }}">
                <div class="card">
                    <div class="card-body">
                        <div class="short mb-3">
                            {{ $product->short }}
                        </div>
                        <div class="full">
                            {!! $product->description !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
