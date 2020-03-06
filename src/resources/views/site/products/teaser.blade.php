<div class="card card-base product-teaser h-100">
    @if ($hasStates)
        <div class="states left">
            @foreach($states as $state)
                <span class="badge badge-{{ $state->color }} px-3 py-2 mt-2">
                    {{ $state->title }}
                </span>
                <br>
            @endforeach
        </div>
    @endif
    @if ($image)
        @picture([
            'image' => $image,
            'template' => "sm-grid-12",
            'grid' => [
                "lg-grid-4" => 992,
                'md-grid-4' => 768,
                'sm-grid-6' => 540,
            ],
            'imgClass' => 'card-img-top',
        ])@endpicture
    @else
        <div class="empty-image">
            <i class="far fa-image fa-9x"></i>
        </div>
    @endif
    <div class="card-body">
        <h5 class="card-title">{{ $product->title }}</h5>
        <p class="card-text text-secondary">{{ $product->short }}</p>
    </div>
    <div class="card-footer">
        @if ($variation)
            <h4 class="mb-4 text-primary">от {{ $variation->price }} руб.</h4>
        @endif
        <a href="{{ route('site.catalog.product.show', [
                            'category' => $category,
                            'product' => $product
                        ]) }}"
           class="btn btn-primary px-4 py-2">
            Подробнее
        </a>
    </div>
</div>