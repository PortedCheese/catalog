<dl class="row">
    <div class="col-12">
        @if ($useCart)
            <add-to-cart :variations="{{ json_encode($variations) }}"
                         form-action="{{ route('site.cart.add', ['product' => $product]) }}">
            </add-to-cart>
        @else
            <catalog-single-order :variations="{{ json_encode($variations) }}"
                                  @auth
                                  :user="{{ Auth::user() }}"
                                  @else
                                  :user="false"
                                  @endauth
                                  form-action="{{ route("site.catalog.order-product", ['product' => $product]) }}">
            </catalog-single-order>
        @endif
    </div>
</dl>