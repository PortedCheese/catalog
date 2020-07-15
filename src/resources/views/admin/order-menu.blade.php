@if ($theme == "sb-admin")
    @php($active = (strstr($currentRoute, "admin.order-state.") !== false) || (strstr($currentRoute, "admin.order.")) || (strstr($currentRoute, "admin.cart.")))
    <li class="nav-item dropdown{{ $active ? ' active' : '' }}">
        <a href="#"
           class="nav-link"
           data-toggle="collapse"
           data-target="#collapse-order-menu"
           aria-controls="#collapse-order-menu"
           aria-expanded="{{ $active ? "true" : "false" }}">
            <i class="fab fa-jedi-order"></i>
            <span>Заказы</span>
        </a>

        <div id="collapse-order-menu" class="collapse{{ $active ? " show" : "" }}"
             data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a href="{{ route("admin.order.index") }}"
                   class="collapse-item{{ $currentRoute == "admin.order.index" ? " active" : "" }}">
                    <span>Список</span>
                </a>

                @can("viewAny", \App\OrderState::class)
                    <a href="{{ route("admin.order-state.index") }}"
                       class="collapse-item{{ $currentRoute == "admin.order-state.index" ? " active" : "" }}">
                        <span>Статусы</span>
                    </a>
                @endcan

                @if (base_config()->get("catalog", "useCart"))
                    @can("viewAny", \App\Cart::class)
                        <a href="{{ route("admin.cart.index") }}"
                           class="collapse-item{{ $currentRoute == "admin.cart.index" ? " active" : "" }}">
                            <span>Корзины</span>
                        </a>
                    @endcan
                @endif
            </div>
        </div>
    </li>
@else
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

            @can("viewAny", \App\OrderState::class)
                <a href="{{ route("admin.order-state.index") }}"
                   class="dropdown-item">
                    Статусы
                </a>
            @endcan

            @if (base_config()->get("catalog", "useCart"))
                @can("viewAny", \App\Cart::class)
                    <a href="{{ route("admin.cart.index") }}"
                       class="dropdown-item">
                        Корзины
                    </a>
                @endcan
            @endif
        </div>
    </li>
@endif