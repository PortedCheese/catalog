@if (siteconf()->get('catalog.useCart'))
    <cart-state cart-total="{{ $cartData->total }}"
                cart-url="{{ route('site.cart.index') }}"
                cart-count="{{ $cartData->count }}">
    </cart-state>
@endif