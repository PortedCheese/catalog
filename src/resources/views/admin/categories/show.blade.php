@extends('admin.layout')

@section('page-title', 'Просмотр - ')
@section('header-title', "{$category->title}")

@section('admin')
    @include("catalog::admin.categories.pills", ['category' => $category])
    <div class="col-12 mb-3">
        <div class="row">
            @if ($image)
                <div class="col-12 col-md-3">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Изображение:</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-inline-block">
                                @img([
                                    "image" => $image,
                                    "template" => "medium",
                                    "lightbox" => "lightGroup" . $category->id,
                                    "imgClass" => "rounded mb-2",
                                    "grid" => [],
                                ])
                                <button type="button" class="close ml-1" data-confirm="{{ "delete-image-form-{$category->id}" }}">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <confirm-form :id="'{{ "delete-image-form-{$category->id}" }}'">
                                <template>
                                    <form action="{{ route('admin.category.destroy-image', ['category' => $category]) }}"
                                          id="delete-image-form-{{ $category->id }}"
                                          class="btn-group"
                                          method="post">
                                        @csrf
                                        <input type="hidden" name="_method" value="DELETE">
                                    </form>
                                </template>
                            </confirm-form>
                        </div>
                    </div>
                </div>
            @endif
            @if (! empty($category->description))
                <div class="category-description {{ $image ? "col-12 col-md-9" : "col-12" }}">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Описание:</h5>
                        </div>
                        <div class="card-body">
                            {{ $category->description }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Подкатегории</h5>
            </div>
            <div class="card-body">
                @include("catalog::admin.categories.table-list", ['categories' => $category->children->sortBy('weight')])
            </div>
        </div>
    </div>
@endsection

@section('links')
    @canany(["delete", "create"], \App\Category::class)
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div role="toolbar" class="btn-toolbar">
                        @can("delete", \App\Category::class)
                            <div class="btn-group mr-1">
                                <button type="button" class="btn btn-danger" data-confirm="{{ "delete-form-{$category->id}" }}">
                                    Удалить
                                </button>
                            </div>
                        @endcan
                        @can("create", \App\Category::class)
                            <div class="btn-group">
                                <a href="{{ route('admin.category.create-child', ['category' => $category]) }}"
                                   class="btn btn-success">
                                    Добавить
                                </a>
                            </div>
                        @endcan
                    </div>
                    @can("delete", \App\Category::class)
                        <confirm-form :id="'{{ "delete-form-{$category->id}" }}'">
                            <template>
                                <form action="{{ route('admin.category.destroy', ['category' => $category]) }}"
                                      id="delete-form-{{ $category->id }}"
                                      class="btn-group"
                                      method="post">
                                    @csrf
                                    <input type="hidden" name="_method" value="DELETE">
                                </form>
                            </template>
                        </confirm-form>
                    @endcan
                </div>
            </div>
        </div>
    @endcanany
@endsection
