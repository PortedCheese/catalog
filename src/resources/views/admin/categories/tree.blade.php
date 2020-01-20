@can("update", \App\Category::class)
    <admin-category-list :structure="{{ json_encode($categories) }}"
                     :update-url="'{{ route("admin.category.items-weight") }}'">
    </admin-category-list>
@else
<ul>
    @foreach ($categories as $category)
        <li>
            @can("view", \App\Category::class)
                <a href="{{ route('admin.category.show', ['category' => $category["slug"]]) }}"
                   class="btn btn-link">
                    {{ $category["title"] }}
                </a>
            @else
                <span>{{ $category['title'] }}</span>
            @endcan
            <span class="badge badge-secondary">{{ count($category["children"]) }}</span>
            @if (count($category["children"]))
                @include("catalog::admin.categories.tree", ['categories' => $category["children"]])
            @endif
        </li>
    @endforeach
</ul>
@endcan
