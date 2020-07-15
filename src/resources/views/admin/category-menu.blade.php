@if ($theme == "sb-admin")
    @php($active = (strstr($currentRoute, "admin.category.") !== false) || (strstr($currentRoute, "admin.product.")) || (strstr($currentRoute, "admin.product-state.")))
    <li class="nav-item dropdown{{ $active ? " active" : "" }}">
        <a href="#"
           class="nav-link"
           data-toggle="collapse"
           data-target="#collapse-category-menu"
           aria-controls="#collapse-category-menu"
           aria-expanded="{{ $active ? "true" : "false" }}">
            <i class="fas fa-stream"></i>
            <span>Категории</span>
        </a>

        <div id="collapse-category-menu" class="collapse{{ $active ? " show" : "" }}"
             data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                @can("viewAny", \App\Category::class)
                    <a href="{{ route("admin.category.index") }}"
                       class="collapse-item{{ $currentRoute == "admin.category.index" ? " active" : "" }}">
                        <span>Список</span>
                    </a>
                @endcan

                @can("viewAny", \App\Product::class)
                    <a href="{{ route("admin.product.index") }}"
                       class="collapse-item{{ $currentRoute == "admin.product.index" ? " active" : "" }}">
                        <span>Товары</span>
                    </a>
                @endcan

                @can("viewAny", \App\ProductState::class)
                    <a href="{{ route("admin.product-state.index") }}"
                       class="collapse-item{{ $currentRoute == "admin.product-state.index" ? " active" : "" }}">
                        <span>Метки товара</span>
                    </a>
                @endcan

                @can("viewAny", \App\CategoryField::class)
                    <a href="{{ route("admin.category.all-fields.list") }}"
                       class="collapse-item{{ $currentRoute == "admin.category.all-fields.list" ? " active" : "" }}">
                        <span>Характеристики</span>
                    </a>
                @endcan

                @can("viewAny", \App\CategoryFieldGroup::class)
                    <a href="{{ route("admin.category.groups.index") }}"
                       class="collapse-item{{ $currentRoute == "admin.category.groups.index" ? " active" : "" }}">
                        <span>Группы</span>
                    </a>
                @endcan
            </div>
        </div>
    </li>
@else
    <li class="nav-item dropdown">
        <a href="#"
           class="nav-link dropdown-toggle{{ (strstr($currentRoute, "admin.category.") !== false) || (strstr($currentRoute, "admin.product.")) || (strstr($currentRoute, "admin.product-state.")) ? " active" : "" }}"
           role="button"
           id="category-menu"
           data-toggle="dropdown"
           aria-haspopup="true"
           aria-expanded="false">
            <i class="fas fa-stream"></i>
            Категории
        </a>

        <div class="dropdown-menu" aria-labelledby="category-menu">
            @can("viewAny", \App\Category::class)
                <a href="{{ route("admin.category.index") }}"
                   class="dropdown-item">
                    Список
                </a>
            @endcan

            @can("viewAny", \App\Product::class)
                <a href="{{ route("admin.product.index") }}"
                   class="dropdown-item">
                    Товары
                </a>
            @endcan

            @can("viewAny", \App\ProductState::class)
                <a href="{{ route("admin.product-state.index") }}"
                   class="dropdown-item">
                    Метки товара
                </a>
            @endcan

            @can("viewAny", \App\CategoryField::class)
                <a href="{{ route("admin.category.all-fields.list") }}"
                   class="dropdown-item">
                    Характеристики
                </a>
            @endcan

            @can("viewAny", \App\CategoryFieldGroup::class)
                <a href="{{ route("admin.category.groups.index") }}"
                   class="dropdown-item">
                    Группы
                </a>
            @endcan
        </div>
    </li>
@endif