<div class="table-responsive">
    <table class="table">
        <thead>
        <tr>
            <th>Товар</th>
            <th>Количество</th>
            <th>Цена</th>
            <th>Сумма</th>
            <th>Удалить</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($cart->getForRender() as $product)
            @foreach ($product->items as $variation)
                <tr id="variation-{{ $variation->model->id }}">
                    <td>
                        @if ($product->image)
                            @image([
                                'image' => $product->image,
                                'template' => 'small',
                                'lightbox' => "image-{$variation->model->id}"
                            ])@endimage
                        @endif
                        <a href="{{ route('site.catalog.product.show', ['category' => $product->category, 'product' => $product->model]) }}">
                            {{ $product->title }}
                        </a>
                        <p>{{ $variation->description }}</p>
                    </td>
                    <td>
                        <change-item-quantity item-quantity="{{ $variation->quantity }}"
                                              variation-id="{{ $variation->model->id }}"
                                              put-url="{{ route('site.cart.change-quantity', ['product' => $product->model, 'variation' => $variation->model]) }}">
                        </change-item-quantity>
                    </td>
                    <td>
                        <span class="variation-price">
                            {{ $variation->price }}
                        </span>
                    </td>
                    <td>
                        <span class="variation-total">
                            {{ $variation->total }}
                        </span>
                    </td>
                    <td>
                        <form action="{{ route('site.cart.delete', ['product' => $product->model, 'variation' => $variation->model]) }}" method="post">
                            @csrf
                            @method('delete')

                            <div class="btn-group"
                                 role="group">
                                <button type="submit" class="btn btn-danger">
                                    <i class="far fa-trash-alt"></i>
                                </button>
                            </div>
                        </form>
                    </td>
                </tr>
            @endforeach
        @endforeach
        </tbody>
    </table>
</div>