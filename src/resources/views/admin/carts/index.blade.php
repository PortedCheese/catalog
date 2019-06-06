@extends('admin.layout')

@section('page-title', 'Корзины - ')
@section('header-title', 'Корзины')

@section('admin')
    <div class="col-12">
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Пользователь</th>
                    <th>UUID</th>
                    <th>Итого</th>
                    <th>Позиции</th>
                    <th>Создано</th>
                    <th>Обновлено</th>
                    <th>Действия</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($carts as $cart)
                    <tr>
                        <td>{{ $page * $per + $loop->iteration }}</td>
                        <td>
                            @if ($cart->user_id)
                                {{ $users[$cart->user_id]->full_name }}
                            @else
                                Гость
                            @endif
                        </td>
                        <td>{{ $cart->uuid }}</td>
                        <td>{{ $cart->total }}</td>
                        <td>{{ $cart->getCount() }}</td>
                        <td>{{ date("d.m.Y H:i:s", strtotime($cart->created_at)) }}</td>
                        <td>{{ date("d.m.Y H:i:s", strtotime($cart->updated_at)) }}</td>
                        <td>
                            <confirm-delete-model-button model-id="{{ $cart->id }}">
                                <template slot="show">
                                    <a href="{{ route('admin.cart.show', ['cart' => $cart]) }}" class="btn btn-dark">
                                        <i class="far fa-eye"></i>
                                    </a>
                                </template>
                                <template slot="delete">
                                    <form action="{{ route('admin.cart.destroy', ['cart' => $cart]) }}"
                                          id="delete-{{ $cart->id }}"
                                          class="btn-group"
                                          method="post">
                                        @csrf
                                        <input type="hidden" name="_method" value="DELETE">
                                    </form>
                                </template>
                            </confirm-delete-model-button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('links')
    <div class="col-12">
        {{ $carts->links() }}
    </div>
@endsection
