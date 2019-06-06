@extends('profile.layout')

@section('page-title', "Заказы - ")

@section('profile')
    <div class="col-12">
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Номер</th>
                    <th>Статус</th>
                    <th>Сумма</th>
                    <th>Создан</th>
                    <th>Действия</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($orders as $order)
                    <tr>
                        <td>{{ $page * $per + $loop->iteration }}</td>
                        <td>{{ $order->id }}</td>
                        <td>
                            {{ $states[$order->state_id]->title }}
                        </td>
                        <td>{{ $order->total }}</td>
                        <td>{{ date('d.m.Y H:i:s', strtotime($order->created_at)) }}</td>
                        <td>
                            <a href="{{ route('profile.order.show', ['order' => $order]) }}" class="btn btn-dark">
                                <i class="far fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection