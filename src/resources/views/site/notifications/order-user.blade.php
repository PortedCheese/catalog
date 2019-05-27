@component('mail::message')
# Здравствуйте!

Вы оформили заказ на сумму {{ $order->total }} рублей.

@component('mail::table')
| Товар | Количество | Сумма |
| :---: | :--------: | :---: |
@foreach ($items as $item)
| {{ $item->title }} | {{ $item->quantity }} | {{ $item->total }} |
@endforeach
@endcomponent

С уважением,<br>
{{ config('app.name') }}
@endcomponent