@extends('admin.layout')

@section('page-title', 'Просмотр - ')
@section('header-title', "{$category->title}")

@section('admin')
    @include("catalog::admin.categories.pills", ['category' => $category])
    <div class="col-12 mb-3">
        <div class="row">
            @php
                $class = $image ? "col-12 col-md-9" : "col-12";
            @endphp
            @if ($image)
                <div class="col-12 col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <img src="{{ route('imagecache', [
                                'template' => 'medium',
                                'filename' => $image->file_name
                            ]) }}"
                                 class="img-thumbnail mb-2"
                                 alt="{{ $image->name }}">
                            <confirm-delete-model-button model-id="{{ $category->id }}">
                                <template slot="delete">
                                    <form action="{{ route('admin.category.destroy-image', ['category' => $category]) }}"
                                          id="delete-{{ $category->id }}"
                                          class="btn-group"
                                          method="post">
                                        @csrf
                                        <input type="hidden" name="_method" value="DELETE">
                                    </form>
                                </template>
                            </confirm-delete-model-button>
                        </div>
                    </div>
                </div>
            @endif
            @if (! empty($category->description))
                <div class="category-description {{ $class }}">
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
                <confirm-delete-model-button model-id="{{ $category->id }}">
                    <template slot="edit">
                        <a href="{{ route('admin.category.edit', ['category' => $category]) }}" class="btn btn-primary">
                            <i class="far fa-edit"></i>
                        </a>
                    </template>
                    <template slot="delete">
                        <form action="{{ route('admin.category.destroy', ['category' => $category]) }}"
                              id="delete-{{ $category->id }}"
                              class="btn-group"
                              method="post">
                            @csrf
                            <input type="hidden" name="_method" value="DELETE">
                        </form>
                    </template>
                    <template slot="other">
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
                    </template>
                </confirm-delete-model-button>
            </div>
        </div>
    </div>
@endsection
