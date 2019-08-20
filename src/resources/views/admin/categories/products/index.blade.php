@extends('admin.layout')

@section('page-title', 'Товары - ')
@section('header-title')
    @isset($category)
        {{ $category->title }}
    @endisset
    @empty($category)
        Товары
    @endempty
@endsection

@section('admin')
    @isset($category)
        @include("catalog::admin.categories.pills", ['category' => $category])
    @endisset

    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ $formRoute }}"
                      method="get"
                      class="form-inline">

                    <label class="sr-only" for="title">Заголовок</label>
                    <input type="text"
                           class="form-control mb-2 mr-sm-2"
                           id="title"
                           name="title"
                           value="{{ $query->get('title') }}"
                           placeholder="Заголовок">

                    <select class="custom-select mb-2 mr-sm-2" name="published">
                        <option value="all"{{ !$query->has('published') || $query->get('published') == 'all' ? " selected" : '' }}>
                            Статус публикации
                        </option>
                        <option value="1"{{ $query->get('published') === '1' ? " selected" : '' }}>
                            Опубликованно
                        </option>
                        <option value="0"{{ $query->get('published') === '0' ? " selected" : '' }}>
                            Снято с публикации
                        </option>
                    </select>

                    <div class="btn-group mb-2"
                         role="group">
                        <button class="btn btn-primary" type="submit">Искать</button>
                        <a href="{{ $formRoute }}" class="btn btn-warning">
                            Сбросить
                        </a>
                        @isset($category)
                            <a href="{{ route('admin.category.product.create', ['category' => $category]) }}"
                               class="btn btn-success">
                                Добавить
                            </a>
                        @endisset
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
                            @if ($all)
                                <th>Категория</th>
                            @endif
                            <th>Действия</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($products as $product)
                            @php
                                if ($all) {
                                    $id = $product->category_id;
                                    if (empty($categories[$id])) {
                                        $categories[$id] = $product->category;
                                    }
                                    $category = $categories[$id];
                                }
                            @endphp
                            <tr>
                                <td>{{ $product->title }}</td>
                                @if ($all)
                                    <td>
                                        <a href="{{ route('admin.category.show', ['category' => $category]) }}">
                                            {{ $category->title }}
                                        </a>
                                    </td>
                                @endif
                                <td>
                                    <confirm-delete-model-button model-id="{{ $product->id }}">
                                        <template slot="forms">
                                            <form id="change-publushed-{{ $product->id }}"
                                                  action="{{ route("admin.category.product.published", ['category' => $category, 'product' => $product]) }}"
                                                  method="post">
                                                @method('put')
                                                @csrf
                                            </form>
                                        </template>
                                        <template slot="other">
                                            <a href="#"
                                               class="btn btn-{{ $product->published ? "success" : "secondary" }}"
                                               onclick="event.preventDefault();document.getElementById('change-publushed-{{ $product->id }}').submit();">
                                                <i class="fas fa-toggle-{{ $product->published ? "on" : "off" }}"></i>
                                            </a>
                                        </template>
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
                                        <template slot="delete">
                                            <form action="{{ route('admin.category.product.destroy', ['category' => $category, 'product' => $product]) }}"
                                                  id="delete-{{ $product->id }}"
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

@section('links')
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                {{ $products->links() }}
            </div>
        </div>
    </div>
@endsection
