@extends('admin.layout')

@section('page-title', 'Категории - ')
@section('header-title', 'Категории')

@section('admin')
    @include("catalog::admin.categories.pills")

    <div class="col-12">
        <div class="card">
            <div class="card-body">
                @if ($tree)
                    @include("catalog::admin.categories.tree", ['categories' => $categories])
                @else
                    @include("catalog::admin.categories.table-list", ['categories' => $categories])
                @endif
            </div>
        </div>
    </div>
@endsection