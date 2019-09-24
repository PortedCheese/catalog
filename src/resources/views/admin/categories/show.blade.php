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
                                <img src="{{ route('imagecache', ['template' => 'medium', 'filename' => $image->file_name]) }}"
                                     class="rounded mb-2"
                                     alt="{{ $image->name }}">
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
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div role="toolbar" class="btn-toolbar">
                    <div class="btn-group mr-1">
                        <a href="{{ route("admin.category.edit", ["category" => $category]) }}" class="btn btn-primary">
                            <i class="far fa-edit"></i>
                        </a>
                        <button type="button" class="btn btn-danger" data-confirm="{{ "delete-form-{$category->id}" }}">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                    <div class="btn-group">
                        <a href="{{ route('admin.category.index') }}"
                           class="btn btn-outline-secondary">
                            Список
                        </a>
                        @if ($parent)
                            <a href="{{ route('admin.category.show', ['category' => $parent]) }}"
                               class="btn btn-outline-secondary">
                                {{ $parent->title }}
                            </a>
                        @endif
                        <a href="{{ route('admin.category.create-child', ['category' => $category]) }}"
                           class="btn btn-outline-success">
                            Добавить
                        </a>
                    </div>
                </div>
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
            </div>
        </div>
    </div>
@endsection
