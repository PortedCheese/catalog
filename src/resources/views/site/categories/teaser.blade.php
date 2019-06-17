<div class="card card-base category-teaser h-100">
    @if ($category->image)
        @picture([
            'image' => $category->image,
            'template' => "sm-grid-12",
            'grid' => [
                "lg-grid-3" => 992,
                'md-grid-6' => 768,
            ],
            'imgClass' => 'card-img-top',
        ])@endpicture
    @else
        <div class="empty-image">
            <i class="far fa-image fa-9x"></i>
        </div>
    @endempty
    <div class="card-body">
        <h5 class="card-title">{{ $category->title }}</h5>
        <p class="card-text">{{ $category->description }}</p>
    </div>
    <div class="card-footer">
        <a href="{{ route('site.catalog.category.show', ['category' => $category]) }}"
           class="btn btn-primary px-4 py-2">
            Подробнее
        </a>
    </div>
</div>