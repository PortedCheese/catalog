<div class="col-12 mb-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            @isset($category)
                @foreach ($category->getSiteBreadcrumb($productPage) as $item)
                    <li class="breadcrumb-item{{ $item->active ? ' active' : '' }}" aria-current="page">
                        @if ($item->active)
                            {{ $item->title }}
                        @else
                            <a href="{{ $item->url }}">
                                {{ $item->title }}
                            </a>
                        @endif
                    </li>
                @endforeach
            @endisset
            @empty($category)
                <li class="breadcrumb-item active" aria-current="page">
                    Каталог
                </li>
            @endempty
        </ol>
    </nav>
</div>