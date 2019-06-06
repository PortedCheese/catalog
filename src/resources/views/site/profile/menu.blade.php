<li class="nav-item">
    <a class="nav-link {{ strstr($currentRoute, 'profile.order.') !== FALSE ? 'active' : '' }}"
       href="{{ route('profile.order.index') }}">
        Заказы
    </a>
</li>