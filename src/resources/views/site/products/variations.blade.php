<dl class="row">
    <div class="col-12">
        <catalog-single-order :variations="{{ json_encode($variations) }}"
                              @auth
                              :user="{{ Auth::user() }}"
                              @else
                              :user="false"
                              @endauth
                              form-action="{{ route("site.catalog.order-product", ['product' => $product]) }}">
        </catalog-single-order>
    </div>
</dl>