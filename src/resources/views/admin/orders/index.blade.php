@extends('admin.layout')

@section('page-title', 'Заказы - ')
@section('header-title', 'Заказы')

@section('admin')
    <div class="col-12">
        <form class="form-inline" method="get">
            <label class="sr-only" for="email">E-mail</label>
            <input type="text"
                   class="form-control mb-2 mr-sm-2"
                   name="email"
                   value="{{ $query->has('email') ? $query->get('email') : '' }}"
                   id="email"
                   placeholder="E-mail">

            <select name="state"
                    id="parent"
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
    <div class="col-12">
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Пользователь</th>
                    <th>E-mail</th>
                    <th>Статус</th>
                    <th>Сумма</th>
                    <th>Создан</th>
                    <th>Действия</th>
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
                            $email = $userData['email'];
                            $name = $userData['name'];
                        }
                    @endphp
                    <tr>
                        <td>{{ $page * $per + $loop->iteration }}</td>
                        <td>{{ $name }}</td>
                        <td>{{ $email }}</td>
                        <td>
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
                        </td>
                        <td>{{ $order->total }}</td>
                        <td>{{ date('d.m.Y H:i:s', strtotime($order->created_at)) }}</td>
                        <td>
                            <confirm-delete-model-button model-id="{{ $order->id }}">
                                <template slot="other">
                                    <button type="button"
                                            class="btn btn-info"
                                            data-toggle="modal"
                                            data-target="#orderInfo{{ $order->id }}">
                                        <i class="fas fa-info"></i>
                                    </button>
                                </template>
                                <template slot="show">
                                    <a href="{{ route('admin.order.show', ['order' => $order]) }}" class="btn btn-dark">
                                        <i class="far fa-eye"></i>
                                    </a>
                                </template>
                                <template slot="delete">
                                    <form action="{{ route('admin.order.destroy', ['order' => $order]) }}"
                                          id="delete-{{ $order->id }}"
                                          class="btn-group"
                                          method="post">
                                        @csrf
                                        <input type="hidden" name="_method" value="DELETE">
                                    </form>
                                </template>
                            </confirm-delete-model-button>

                            @include("catalog::admin.orders.user-info-modal", ['order' => $order, 'userData' => $userData])
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
        {{ $orders->links() }}
    </div>
@endsection
