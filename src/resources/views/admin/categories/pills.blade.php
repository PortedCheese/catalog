@if (! empty($category))
    @php($productPage = !empty($productPage))
    @include("catalog::admin.categories.breadcrumb", ['category' => $category, 'productPage' => $productPage])
@endif

<div class="col-12 mb-3">
    <div class="card">
        <div class="card-body">
            <ul class="nav nav-pills">
                @can("viewAny", \App\Category::class)
                    <li class="nav-item">
                        <a href="{{ route('admin.category.index') }}"
                           class="nav-link{{ isset($tree) && !$tree ? " active" : "" }}">
                            Категории
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('admin.category.index') }}?view=tree"
                           class="nav-link{{ isset($tree) && $tree ? " active" : "" }}">
                            Структура
                        </a>
                    </li>
                @endcan

                @empty($category)
                    @can("create", \App\Category::class)
                        <li class="nav-item">
                            <a href="{{ route('admin.category.create') }}"
                               class="nav-link{{ $currentRoute == 'admin.category.create' ? " active" : "" }}">
                                Добавить
                            </a>
                        </li>
                    @endcan
                @else
                    @can("create", \App\Category::class)
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ $currentRoute == 'admin.category.create-child' ? " active" : "" }}"
                               data-toggle="dropdown"
                               href="#"
                               role="button"
                               aria-haspopup="true"
                               aria-expanded="false">
                                Добавить
                            </a>
                            <div class="dropdown-menu">
                                <a class="dropdown-item"
                                   href="{{ route('admin.category.create') }}">
                                    Основную
                                </a>
                                <a class="dropdown-item"
                                   href="{{ route('admin.category.create-child', ['category' => $category]) }}">
                                    Подкатегорию
                                </a>
                            </div>
                        </li>
                    @endcan

                    @can("view", \App\Category::class)
                        <li class="nav-item">
                            <a href="{{ route('admin.category.show', ['category' => $category]) }}"
                               class="nav-link{{ $currentRoute == 'admin.category.show' ? ' active' : '' }}">
                                Просмотр
                            </a>
                        </li>
                    @endcan

                    @can("update", \App\Category::class)
                        <li class="nav-item">
                            <a href="{{ route('admin.category.edit', ['category' => $category]) }}"
                               class="nav-link{{ $currentRoute == 'admin.category.edit' ? ' active' : '' }}">
                                Редактировать
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('admin.category.metas', ['category' => $category]) }}"
                               class="nav-link{{ $currentRoute == 'admin.category.metas' ? ' active' : '' }}">
                                Мета
                            </a>
                        </li>
                    @endcan

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
                @endempty
            </ul>
        </div>
    </div>
</div>