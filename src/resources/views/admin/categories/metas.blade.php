@extends('admin.layout')

@section('page-title', 'Мета - ')
@section('header-title', "Мета для {$category->title}")

@section('admin')
    <div class="col-12 mt-2">
        <h2>Добавить тег</h2>
        @include("seo-integration::admin.meta.create", ['model' => 'categories', 'id' => $category->id])
    </div>
    <div class="col-12 mt-2">
        @include("seo-integration::admin.meta.table-models", ['metas' => $category->metas])
    </div>
@endsection

@section('links')
    <div class="col-12">
        <div class="btn-group"
             role="group">
            <a href="{{ route('admin.category.show', ['category' => $category]) }}"
               class="btn btn-secondary">
                Назад к категории
            </a>
        </div>
    </div>
@endsection
