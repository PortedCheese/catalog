@extends('admin.layout')

@section('page-title', 'Мета - ')
@section('header-title', "{$category->title}")

@section('admin')
    @include("catalog::admin.categories.pills", ['category' => $category])
    <div class="col-12">
        <h2>Добавить тег</h2>
        @include("seo-integration::admin.meta.create", ['model' => 'categories', 'id' => $category->id])
    </div>
    <div class="col-12 mt-2">
        @include("seo-integration::admin.meta.table-models", ['metas' => $category->metas])
    </div>
@endsection
