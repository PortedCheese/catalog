<li class="nav-item dropdown">
    <a href="#"
       class="nav-link dropdown-toggle{{ (strstr($currentRoute, "admin.category.") !== false) || (strstr($currentRoute, "admin.product.")) || (strstr($currentRoute, "admin.product-state.")) ? " active" : "" }}"
       role="button"
       id="category-menu"
       data-toggle="dropdown"
       aria-haspopup="true"
       aria-expanded="false">
        <i class="fas fa-stream"></i>
        Категори
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

        <a href="{{ route("admin.product-state.index") }}"
           class="dropdown-item">
            Метки товара
        </a>

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