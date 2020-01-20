@extends('admin.layout')

@section('page-title', 'Сатусы заказа - ')
@section('header-title', 'Сатусы заказа')

@section('admin')
    <div class="col-12">
        <div class="card">
            @can("create", \App\OrderState::class)
                <div class="card-header">
                    <div class="btn-group"
                         role="group">
                        <a href="{{ route('admin.order-state.create') }}"
                           class="btn btn-success">
                            Добавить
                        </a>
                    </div>
                </div>
            @endcan
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Заголовок</th>
                            <th>Ключ</th>
                            @canany(["update", "delete"], \App\OrderState::class)
                                <th>Действия</th>
                            @endcanany
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($states as $state)
                            <tr>
                                <td>{{ $state->title }}</td>
                                <td>{{ $state->machine }}</td>
                                @canany(["update", "delete"], $state)
                                    <td>
                                        <div role="toolbar" class="btn-toolbar">
                                            <div class="btn-group mr-1">
                                                @can("update", $state)
                                                    <a href="{{ route("admin.order-state.edit", ["state" => $state]) }}" class="btn btn-primary">
                                                        <i class="far fa-edit"></i>
                                                    </a>
                                                @endcan
                                                @can("delete", $state)
                                                    <button type="button" class="btn btn-danger" data-confirm="{{ "delete-form-{$state->id}" }}">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                @endcan
                                            </div>
                                        </div>
                                        @can("delete", $state)
                                            <confirm-form :id="'{{ "delete-form-{$state->id}" }}'">
                                                <template>
                                                    <form action="{{ route('admin.order-state.destroy', ['state' => $state]) }}"
                                                          id="delete-form-{{ $state->id }}"
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
