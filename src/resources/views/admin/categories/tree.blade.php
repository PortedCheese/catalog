<ul>
    @foreach ($categories as $category)
        <li>
            <a href="{{ route('admin.category.show', ['category' => $category]) }}"
               class="btn btn-link">
                {{ $category->title }}
            </a>
            @if ($category->children->count())
                @include("catalog::admin.categories.tree", ['categories' => $category->children->sortBy('weight')])
            @endif
        </li>
    @endforeach
</ul>