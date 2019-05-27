@extends('admin.layout')

@section('page-title', 'Просмотр заказа - ')
@section('header-title', "Просмотр заказа №{$order->id}")

@section('admin')
    <div class="col-12">
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>Товар</th>
                    <th>SKU</th>
                    <th>Цена</th>
                    <th>Количество</th>
                    <th>Итого</th>
                    <th>Описание</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($items as $item)
                    <tr>
                        <td>
                            @if (!empty($products[$item->product_id]))
                                @php($product = $products[$item->product_id])
                                <a href="{{ route('admin.category.product.show', [
                                                        'product' => $product,
                                                        'category' => $categories[$product->category_id]
                                                    ]) }}"
                                   target="_blank">
                                    {{ $product->title }}
                                </a>
                            @else
                                {{ $item->title }}
                            @endif
                        </td>
                        <td>{{ $item->sku }}</td>
                        <td>{{ $item->price }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $item->total }}</td>
                        <td>{{ $item->description }}</td>
                    </tr>
                @endforeach
                </tbody>
                <tfooter>
                    <tr>
                        <td colspan="4"></td>
                        <td>{{ $order->total }}</td>
                        <td></td>
                    </tr>
                </tfooter>
            </table>
        </div>
    </div>
@endsection

@section('links')
    <div class="col-12">
        <div class="btn-group"
             role="group">
            <a href="{{ route('admin.order.index') }}"
               class="btn btn-secondary">
                Список
            </a>
            <button type="button"
                    class="btn btn-info"
                    data-toggle="modal"
                    data-target="#orderInfo{{ $order->id }}">
                <i class="fas fa-info"></i>
            </button>
        </div>
    </div>
    @include("catalog::admin.orders.user-info-modal", ['order' => $order, 'userData' => $userData])
@endsection
