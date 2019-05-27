@extends('admin.layout')

@section('page-title', 'Сатусы заказа - ')
@section('header-title', 'Сатусы заказа')

@section('admin')
    <div class="col-12">
        <div class="btn-group"
             role="group">
            <a href="{{ route('admin.order-state.create') }}"
               class="btn btn-primary">
                Добавить
            </a>
        </div>
    </div>
    <div class="col-12">
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>Заголовок</th>
                    <th>Ключ</th>
                    <th>Действия</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($states as $state)
                    <tr>
                        <td>{{ $state->title }}</td>
                        <td>{{ $state->machine }}</td>
                        <td>
                            <confirm-delete-model-button model-id="{{ $state->id }}">
                                <template slot="edit">
                                    <a href="{{ route('admin.order-state.edit', ['state' => $state]) }}" class="btn btn-primary">
                                        <i class="far fa-edit"></i>
                                    </a>
                                </template>
                                <template slot="show">
                                    <a href="{{ route('admin.order-state.show', ['state' => $state]) }}" class="btn btn-dark">
                                        <i class="far fa-eye"></i>
                                    </a>
                                </template>
                                <template slot="delete">
                                    <form action="{{ route('admin.order-state.destroy', ['state' => $state]) }}"
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
@endsection
