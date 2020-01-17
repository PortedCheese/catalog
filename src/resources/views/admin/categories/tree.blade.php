{{--<ul>--}}
{{--    @foreach ($categories as $category)--}}
{{--        <li>--}}
{{--            <a href="{{ route('admin.category.show', ['category' => $category["slug"]]) }}"--}}
{{--               class="btn btn-link">--}}
{{--                {{ $category["title"] }}--}}
{{--            </a>--}}
{{--            <span class="badge badge-secondary">{{ count($category["children"]) }}</span>--}}
{{--            @if (count($category["children"]))--}}
{{--                @include("catalog::admin.categories.tree", ['categories' => $category["children"]])--}}
{{--            @endif--}}
{{--        </li>--}}
{{--    @endforeach--}}
{{--</ul>--}}

<admin-category-list :structure="{{ json_encode($categories) }}"
                 :update-url="'{{ route("admin.category.items-weight") }}'">
</admin-category-list>
