@extends('admin.layout')

@section('page-title', 'Мета - ')
@section('header-title', "{$category->title}")

@section('admin')
    @include("catalog::admin.categories.pills", ['category' => $category])
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Добавить тег</h5>
            </div>
            <div class="card-body">
                @include("seo-integration::admin.meta.create", ['model' => 'categories', 'id' => $category->id])
            </div>
        </div>
    </div>
    <div class="col-12 mt-2">
        <div class="card">
            <div class="card-body">
                @include("seo-integration::admin.meta.table-models", ['metas' => $category->metas])
            </div>
        </div>
    </div>
@endsection
