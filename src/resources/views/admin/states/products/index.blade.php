@extends('admin.layout')

@section('page-title', 'Метки товара - ')
@section('header-title', 'Метки товара')

@section('admin')
    <div class="col-12">
        <div class="card">
            @can("create", \App\ProductState::class)
                <div class="card-header">
                    <a href="{{ route('admin.product-state.create') }}"
                       class="btn btn-success">
                        Добавить
                    </a>
                </div>
            @endcan
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Заголовок</th>
                            <th>Цвет</th>
                            <th>Slug</th>
                            @canany(["update", "view", "delete"], \App\ProductState::class)
                                <th>Действия</th>
                            @endcanany
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($states as $state)
                            <tr>
                                <td>{{ $state->title }}</td>
                                <td>
                            <span class="badge badge-{{ $state->color }}">
                                {{ $state->color }}
                            </span>
                                </td>
                                <td>{{ $state->slug }}</td>
                                @canany(["update", "view", "delete"], $state)
                                    <td>
                                        <div role="toolbar" class="btn-toolbar">
                                            <div class="btn-group mr-1">
                                                @can("update", $state)
                                                    <a href="{{ route("admin.product-state.edit", ["state" => $state]) }}" class="btn btn-primary">
                                                        <i class="far fa-edit"></i>
                                                    </a>
                                                @endcan
                                                @can("view", $state)
                                                    <a href="{{ route('admin.product-state.show', ['state' => $state]) }}" class="btn btn-dark">
                                                        <i class="far fa-eye"></i>
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
                                                    <form action="{{ route('admin.product-state.destroy', ['state' => $state]) }}"
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
