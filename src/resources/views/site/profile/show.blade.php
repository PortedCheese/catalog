@extends('profile.layout')

@section('page-title', "Заказы - ")

@section('profile')
    <div class="col-12">
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>Номер</th>
                    <th>Статус</th>
                    <th>Сумма</th>
                    <th>Создан</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>
                        {{ $states[$order->state_id]->title }}
                    </td>
                    <td>{{ $order->total }}</td>
                    <td>{{ date('d.m.Y H:i:s', strtotime($order->created_at)) }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="col-12">
        <h3>Позиции заказа</h3>
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>Товар</th>
                    <th>Артикул</th>
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