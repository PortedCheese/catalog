@if (! empty($category))
    @php($productPage = !empty($productPage))
    @include("catalog::admin.categories.breadcrumb", ['category' => $category, 'productPage' => $productPage])
@endif

<div class="col-12 mb-3">
    <div class="card">
        <div class="card-body">
            <ul class="nav nav-pills">
                <li class="nav-item">
                    <a href="{{ route('admin.category.index') }}"
                       class="nav-link{{ isset($tree) && !$tree ? " active" : "" }}">
                        Таблицей
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.category.index') }}?view=tree"
                       class="nav-link{{ isset($tree) && $tree ? " active" : "" }}">
                        Списком
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.category.create') }}?view=tree"
                       class="nav-link{{ $currentRoute == 'admin.category.create' ? " active" : "" }}">
                        Добавить
                    </a>
                </li>
                @if (! empty($category))
                    <li class="nav-item">
                        <a href="{{ route('admin.category.show', ['category' => $category]) }}"
                           class="nav-link{{ $currentRoute == 'admin.category.show' ? ' active' : '' }}">
                            Категория
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('admin.category.metas', ['category' => $category]) }}"
                           class="nav-link{{ $currentRoute == 'admin.category.metas' ? ' active' : '' }}">
                            Мета
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('admin.category.field.index', ['category' => $category]) }}"
                           class="nav-link{{ strstr($currentRoute, 'admin.category.field') !== FALSE ? ' active' : '' }}">
                            Характеристики <span class="badge badge-dark">{{ $category->fields->count() }}</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('admin.category.product.index', ['category' => $category]) }}"
                           class="nav-link{{ strstr($currentRoute, 'admin.category.product') !== FALSE ? ' active' : '' }}">
                            Товары
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</div>