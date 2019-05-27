<div class="card category-teaser shadow h-100">
    @isset ($product->image)
        <img src="{{ route('imagecache', ['template' => 'large', 'filename' => $product->image->file_name]) }}"
             class="card-img-top"
             alt="{{ $product->image->name }}">
    @endisset
    <div class="card-body">
        <h5 class="card-title">{{ $product->title }}</h5>
        <p class="card-text">{{ $product->short }}</p>
    </div>
    <div class="card-footer">
        <a href="{{ route('site.catalog.product.show', [
                            'category' => $product->category,
                            'product' => $product
                        ]) }}"
           class="card-link">
            Подробнее
        </a>
    </div>
</div>