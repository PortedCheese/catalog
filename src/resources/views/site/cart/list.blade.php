@foreach ($cart->getForRender() as $product)
    @foreach ($product->items as $variation)
        @php($border = ! ($loop->parent->last && $loop->last))
        <div id="variation-{{ $variation->model->id }}" class="row py-4 mb-2 mx-2{{ $border ? ' border-bottom' : "" }}">
            <div class="col-6 col-lg-2 mb-2">
                @if ($product->image)
                    @image([
                    'image' => $product->image,
                    'template' => 'small',
                    'lightbox' => "image-{$variation->model->id}"
                    ])@endimage
                @endif
            </div>
            <div class="col-6 col-lg-5 mb-2">
                <a href="{{ route('site.catalog.product.show', ['category' => $product->category, 'product' => $product->model]) }}">
                    {{ $product->title }}
                </a>
                <br>
                <span>{{ $variation->description }}</span>
                <div>
                    <span class="variation-price text-primary">{{ $variation->price }}</span> руб.
                </div>
            </div>
            <div class="col-12 col-lg-3 mb-2">
                <div class="row">
                    <div class="col-12 col-sm-6 col-lg-12 mb-3">
                        <change-item-quantity item-quantity="{{ $variation->quantity }}"
                                              variation-id="{{ $variation->model->id }}"
                                              put-url="{{ route('site.cart.change-quantity', ['product' => $product->model, 'variation' => $variation->model]) }}">
                        </change-item-quantity>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-12 text-primary text-lg-right">
                        <span class="variation-total h4">{{ $variation->total }}</span> руб.
                    </div>
                </div>
            </div>
            <div class=" col-12 col-lg-2 mb-2">
                <form action="{{ route('site.cart.delete', ['product' => $product->model, 'variation' => $variation->model]) }}" method="post">
                    @csrf
                    @method('delete')

                    <button type="submit" class="btn btn-light d-none d-lg-block mx-auto">
                        <i class="fas fa-times"></i>
                    </button>
                    <button type="submit" class="btn btn-outline-danger btn-block d-block d-lg-none">
                        Удалить
                    </button>
                </form>
            </div>
        </div>
    @endforeach
@endforeach