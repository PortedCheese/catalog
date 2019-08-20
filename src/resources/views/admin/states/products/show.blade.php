@extends('admin.layout')

@section('page-title', 'Товары метки - ')
@section('header-title', "Товары метки {$state->title}")

@section('admin')
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.product-state.show', ['state' => $state]) }}"
                      method="get"
                      class="form-inline">

                    <label class="sr-only" for="title">Заголовок</label>
                    <input type="text"
                           class="form-control mb-2 mr-sm-2"
                           id="title"
                           name="title"
                           value="{{ $query->get('title') }}"
                           placeholder="Заголовок">

                    <div class="btn-group mb-2"
                         role="group">
                        <button class="btn btn-primary" type="submit">Искать</button>
                        <a href="{{ route('admin.product-state.show', ['state' => $state]) }}"
                           class="btn btn-warning">
                            Сбросить
                        </a>
                    </div>
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
                            <th>Заголовок</th>
                            <th>Категория</th>
                            <th>Действия</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($products as $product)
                            @php
                                $id = $product->category_id;
                                if (empty($categories[$id])) {
                                    $categories[$id] = $product->category;
                                }
                                $category = $categories[$id];
                            @endphp
                            <tr>
                                <td>{{ $product->title }}</td>
                                <td>
                                    <a href="{{ route('admin.category.show', ['category' => $category]) }}">
                                        {{ $category->title }}
                                    </a>
                                </td>
                                <td>
                                    <confirm-delete-model-button model-id="{{ $product->id }}">
                                        <template slot="edit">
                                            <a href="{{ route('admin.category.product.edit', ['category' => $category, 'product' => $product]) }}"
                                               class="btn btn-primary">
                                                <i class="far fa-edit"></i>
                                            </a>
                                        </template>
                                        <template slot="show">
                                            <a href="{{ route('admin.category.product.show', ['category' => $category, 'product' => $product]) }}"
                                               class="btn btn-dark">
                                                <i class="far fa-eye"></i>
                                            </a>
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

@section('links')
    <div class="col-12">
        <div class="card">
            <div class="crd-body">
                <div class="btn-group"
                     role="group">
                    <a href="{{ route('admin.product-state.index') }}"
                       class="btn btn-secondary">
                        Метки товара
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
