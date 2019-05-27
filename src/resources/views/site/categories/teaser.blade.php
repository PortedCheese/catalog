<div class="card category-teaser shadow h-100">
    @isset ($category->image)
        <img src="{{ route('imagecache', ['template' => 'large', 'filename' => $category->image->file_name]) }}"
             class="card-img-top"
             alt="{{ $category->image->name }}">
    @endisset
    <div class="card-body">
        <h5 class="card-title">{{ $category->title }}</h5>
        <p class="card-text">{{ $category->description }}</p>
    </div>
    <div class="card-footer">
        <a href="{{ route('site.catalog.category.show', ['category' => $category]) }}"
           class="card-link">
            Подробнее
        </a>
    </div>
</div>