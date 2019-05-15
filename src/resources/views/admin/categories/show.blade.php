@extends('admin.layout')

@section('page-title', 'Просмотр - ')
@section('header-title', "Просмотр {$category->title}")

@section('admin')
    <div class="col-12 mb-3">
        <div class="row">
            @php
                $class = $image ? "col-9" : "col-12";
            @endphp
            @if ($image)
                <div class="col-3">
                    <img src="{{ route('imagecache', [
                                'template' => 'medium',
                                'filename' => $image->file_name
                            ]) }}"
                         class="img-thumbnail"
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
            @endif
            <div class="category-description {{ $class }}">
                {{ $category->description }}
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="btn-group"
             role="group">
            <a href="{{ route('admin.category.metas', ['category' => $category]) }}"
               class="btn btn-secondary">
                Мета
            </a>
            <a href="{{ route('admin.category.field.index', ['category' => $category]) }}"
               class="btn btn-primary">
                Просмотр характеристик <span class="badge badge-light">{{ $category->fields->count() }}</span>
            </a>
        </div>
        @include(
            "catalog::admin.categories.table-list",
            ['categories' => $category->children->sortBy('weight')]
            )
    </div>
@endsection

@section('links')
    <div class="col-12">
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
                   class="btn btn-secondary">
                    Список
                </a>
                @if ($parent)
                    <a href="{{ route('admin.category.show', ['category' => $parent]) }}"
                       class="btn btn-secondary">
                        {{ $parent->title }}
                    </a>
                @endif
                <a href="{{ route('admin.category.create-child', ['category' => $category]) }}"
                   class="btn btn-success">
                    Добавить
                </a>
            </template>
        </confirm-delete-model-button>
    </div>
@endsection
