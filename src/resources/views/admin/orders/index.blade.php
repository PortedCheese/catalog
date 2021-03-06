@extends('admin.layout')

@section('page-title', 'Заказы - ')
@section('header-title', 'Заказы')

@section('admin')
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form class="form-inline" method="get">
                    <label class="sr-only" for="email">E-mail</label>
                    <input type="text"
                           class="form-control mb-2 mr-sm-2"
                           name="email"
                           value="{{ $query->has('email') ? $query->get('email') : '' }}"
                           id="email"
                           placeholder="E-mail">

                    <label for="state" class="sr-only">Статус</label>
                    <select name="state"
                            id="state"
                            class="custom-select mb-2 mr-sm-2">
                        <option value=""{{ $query->has('state') ? '' : ' selected' }}>-- Статус --</option>
                        @foreach($states as $key => $value)
                            <option value="{{ $key }}"{{ $query->get('state') == $key ? ' selected' : '' }}>
                                {{ $value->title }}
                            </option>
                        @endforeach
                    </select>

                    <div class="input-group mb-2 mr-sm-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Создан</span>
                        </div>
                        <input type="date"
                               value="{{ $query->get('from', '') }}"
                               class="form-control"
                               name="from">
                        <input type="date"
                               value="{{ $query->get('to', '') }}"
                               class="form-control"
                               name="to">
                    </div>

                    <button type="submit" class="btn btn-primary mb-2">Искать</button>
                    <a href="{{ route('admin.order.index') }}" class="btn btn-link mb-2">Сбросить</a>
                </form>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Имя</th>
                            <th>E-mail</th>
                            <th>Телефон</th>
                            <th>Статус</th>
                            <th>Сумма</th>
                            <th>Создан</th>
                            @canany(["view", "delete"], \App\Order::class)
                                <th>Действия</th>
                            @endcan
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($orders as $order)
                            @php
                                $userData = $order->user_data;
                                if ($order->user_id) {
                                    $user = $order->user;
                                    $email = $user->email;
                                    $name = $user->full_name;
                                }
                                else {
                                    $user = false;
                                    $email = !empty($userData['email']) ? $userData['email'] : false;
                                    $name = $userData['name'];
                                }
                                $phone = !empty($userData['phone']) ? $userData['phone'] : false;
                            @endphp
                            <tr>
                                <td>{{ $page * $per + $loop->iteration }}</td>
                                <td>
                                    @if ($user)
                                        <a target="_blank" href="{{ route('admin.users.index', ['user' => $user, 'login' => $user->login]) }}">
                                            {{ $name }}
                                        </a>
                                    @else
                                        {{ $name }}
                                    @endif
                                </td>
                                <td>
                                    @if ($email)
                                        <a href="mailto:{{ $email }}">{{ $email }}</a>
                                    @endif
                                </td>
                                <td>
                                    @if ($phone)
                                        <a href="phone:{{ $phone }}">{{ $phone }}</a>
                                    @endif
                                </td>
                                <td>
                                    @can("update", $order)
                                        <form action="{{ route('admin.order.update', ['order' => $order]) }}"
                                              method="post"
                                              style="width: 150px">
                                            @csrf
                                            @method('put')

                                            <div class="input-group">
                                                <select name="state"
                                                        id="parent"
                                                        class="custom-select{{ $errors->has('title') ? ' is-invalid' : '' }}">
                                                    @foreach($states as $key => $value)
                                                        <option value="{{ $key }}"{{ $key == $order->state_id ? ' selected' : '' }}>
                                                            {{ $value->title }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="input-group-append">
                                                    <button type="submit" class="btn btn-outline-success">
                                                        <i class="far fa-save"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    @else
                                        {{ $order->state->title }}
                                    @endcan
                                </td>
                                <td>{{ $order->total }}</td>
                                <td>{{ datehelper()->format($order->created_at, "d.m.Y H:i") }}</td>
                                @canany(["view", "delete"], $order)
                                    <td>
                                        <div role="toolbar" class="btn-toolbar">
                                            <div class="btn-group mr-1">
                                                @can("view", $order)
                                                    <button type="button"
                                                            class="btn btn-info"
                                                            data-toggle="modal"
                                                            data-target="#orderInfo{{ $order->id }}">
                                                        <i class="fas fa-info"></i>
                                                    </button>
                                                    <a href="{{ route('admin.order.show', ['order' => $order]) }}" class="btn btn-dark">
                                                        <i class="far fa-eye"></i>
                                                    </a>
                                                @endcan
                                                @can("delete", $order)
                                                    <button type="button" class="btn btn-danger" data-confirm="{{ "delete-order-form-{$order->id}" }}">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                @endcan
                                            </div>
                                        </div>
                                        @can("delete", $order)
                                            <confirm-form :id="'{{ "delete-order-form-{$order->id}" }}'">
                                                <template>
                                                    <form action="{{ route('admin.order.destroy', ['order' => $order]) }}"
                                                          id="delete-order-form-{{ $order->id }}"
                                                          class="btn-group"
                                                          method="post">
                                                        @csrf
                                                        <input type="hidden" name="_method" value="DELETE">
                                                    </form>
                                                </template>
                                            </confirm-form>
                                        @endcan
                                        @can("view", $order)
                                            @include("catalog::admin.orders.user-info-modal", ['order' => $order, 'userData' => $userData])
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
        <div class="card">
            <div class="card-body">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
@endsection
