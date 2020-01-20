<li class="nav-item dropdown">
    <a href="#"
       class="nav-link dropdown-toggle{{ (strstr($currentRoute, "admin.order-state.") !== false) || (strstr($currentRoute, "admin.order.")) || (strstr($currentRoute, "admin.cart.")) ? " active" : "" }}"
       role="button"
       id="order-menu"
       data-toggle="dropdown"
       aria-haspopup="true"
       aria-expanded="false">
        <i class="fab fa-jedi-order"></i>
        Заказы
    </a>

    <div class="dropdown-menu" aria-labelledby="category-menu">
        <a href="{{ route("admin.order.index") }}"
           class="dropdown-item">
            Список
        </a>

        <a href="{{ route("admin.order-state.index") }}"
           class="dropdown-item">
            Статусы
        </a>

        @if (siteconf()->get("catalog", "useCart"))
            <a href="{{ route("admin.cart.index") }}"
               class="dropdown-item">
                Корзины
            </a>
        @endif
    </div>
</li>