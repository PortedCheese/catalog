@extends('admin.layout')

@section('page-title', 'Метки товара - ')
@section('header-title', 'Метки товара')

@section('admin')
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <a href="{{ route('admin.product-state.create') }}"
                   class="btn btn-success">
                    Добавить
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Заголовок</th>
                            <th>Цвет</th>
                            <th>Slug</th>
                            <th>Действия</th>
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
                                <td>
                                    <confirm-delete-model-button model-id="{{ $state->id }}">
                                        <template slot="edit">
                                            <a href="{{ route('admin.product-state.edit', ['state' => $state]) }}" class="btn btn-primary">
                                                <i class="far fa-edit"></i>
                                            </a>
                                        </template>
                                        <template slot="show">
                                            <a href="{{ route('admin.product-state.show', ['state' => $state]) }}" class="btn btn-dark">
                                                <i class="far fa-eye"></i>
                                            </a>
                                        </template>
                                        <template slot="delete">
                                            <form action="{{ route('admin.product-state.destroy', ['state' => $state]) }}"
                                                  id="delete-{{ $state->id }}"
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
        </div>
    </div>
@endsection
