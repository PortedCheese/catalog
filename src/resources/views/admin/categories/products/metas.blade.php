@extends('admin.layout')

@section('page-title', 'Мета - ')
@section('header-title', "{$product->title}")

@section('admin')
    @include("catalog::admin.categories.products.pills", ['category' => $category, 'product' => $product])
    <div class="col-12">
        <h2>Добавить тег</h2>
        @include("seo-integration::admin.meta.create", ['model' => 'products', 'id' => $product->id])
    </div>
    <div class="col-12 mt-2">
        @include("seo-integration::admin.meta.table-models", ['metas' => $product->metas])
    </div>
@endsection
