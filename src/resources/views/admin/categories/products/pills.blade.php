@include("catalog::admin.categories.pills", ['category' => $category, 'productPage' => true])

<div class="col-12 mb-3">
    <div class="card">
        <div class="card-body">
            <ul class="nav nav-pills">
                <li class="nav-item">
                    <a href="{{ route('admin.category.product.index', ['category' => $category]) }}"
                       class="nav-link{{ $currentRoute == 'admin.category.product.index' ? ' active' : '' }}">
                        Список
                    </a>
                </li>
                @can("create", \App\Product::class)
                    <li class="nav-item">
                        <a href="{{ route('admin.category.product.create', ['category' => $category]) }}"
                           class="nav-link{{ $currentRoute == 'admin.category.product.create' ? ' active' : '' }}">
                            Добавить
                        </a>
                    </li>
                @endcan
                @if (! empty($product))
                    <li class="nav-item">
                        <a class="nav-link{{ $currentRoute == 'admin.category.product.show' ? ' active' : '' }}"
                           href="{{ route('admin.category.product.show', ['category' => $category, 'product' => $product]) }}">
                            Просмотр
                        </a>
                    </li>
                    @can("update", $product)
                        <li class="nav-item">
                            <a class="nav-link{{ $currentRoute == 'admin.category.product.edit' ? ' active' : '' }}"
                               href="{{ route('admin.category.product.edit', ['category' => $category, 'product' => $product]) }}">
                                Редактировать
                            </a>
                        </li>
                    @endcan
                    @can("viewAny", \App\ProductField::class)
                    <li class="nav-item">
                        <a class="nav-link{{ strstr($currentRoute, 'admin.category.product.field') !== FALSE ? ' active' : '' }}"
                           href="{{ route('admin.category.product.field.index', ['category' => $category, 'product' => $product]) }}">
                            Характеристики
                        </a>
                    </li>
                    @endcan
                    @can("update", $product)
                        <li class="nav-item">
                            <a class="nav-link{{ $currentRoute == 'admin.category.product.metas' ? ' active' : '' }}"
                               href="{{ route('admin.category.product.metas', ['category' => $category, 'product' => $product]) }}">
                                Мета
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link{{ $currentRoute == 'admin.category.product.gallery' ? ' active' : '' }}"
                               href="{{ route('admin.category.product.gallery', ['category' => $category, 'product' => $product]) }}">
                                Галерея
                            </a>
                        </li>
                    @endcan
                    @can("viewAny", \App\ProductVariation::class)
                        <li class="nav-item">
                            <a class="nav-link{{ strstr($currentRoute, 'admin.category.product.variation') !== FALSE ? ' active' : '' }}"
                               href="{{ route('admin.category.product.variation.index', ['category' => $category, 'product' => $product]) }}">
                                Вариации
                            </a>
                        </li>
                    @endcan
                @endif
            </ul>
        </div>
    </div>
</div>