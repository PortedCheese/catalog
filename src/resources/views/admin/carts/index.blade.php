@extends('admin.layout')

@section('page-title', 'Корзины - ')
@section('header-title', 'Корзины')

@section('admin')
    <div class="col-12">
        <div class="card">
            <div class="card-body">
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
                            @canany(["view", "delete"], \App\Cart::class)
                                <th>Действия</th>
                            @endcanany
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
                                @canany(["view", "delete"], $cart)
                                    <td>
                                        <div role="toolbar" class="btn-toolbar">
                                            <div class="btn-group mr-1">
                                                @can("view", $cart)
                                                    <a href="{{ route('admin.cart.show', ['cart' => $cart]) }}" class="btn btn-dark">
                                                        <i class="far fa-eye"></i>
                                                    </a>
                                                @endcan
                                                @can("delete", $cart)
                                                    <button type="button" class="btn btn-danger" data-confirm="{{ "delete-form-{$cart->id}" }}">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                @endcan
                                            </div>
                                        </div>
                                        @can("delete", $cart)
                                            <confirm-form :id="'{{ "delete-form-{$cart->id}" }}'">
                                                <template>
                                                    <form action="{{ route('admin.cart.destroy', ['cart' => $cart]) }}"
                                                          id="delete-form-{{ $cart->id }}"
                                                          class="btn-group"
                                                          method="post">
                                                        @csrf
                                                        <input type="hidden" name="_method" value="DELETE">
                                                    </form>
                                                </template>
                                            </confirm-form>
                                        @endcan
                                    </td>
                                @endcanany
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('links')
    <div class="col-12">
        {{ $carts->links() }}
    </div>
@endsection
