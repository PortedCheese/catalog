@extends('admin.layout')

@section('page-title', 'Категории - ')
@section('header-title', 'Категории')

@section('admin')
    <div class="col-12">
        <div class="row">
            <div class="col-12">
                <ul class="nav nav-pills mb-3">
                    <li class="nav-item">
                        <a href="{{ route('admin.category.index') }}"
                           class="nav-link{{ $tree ? "" : " active" }}">
                            Таблицей
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.category.index') }}?view=tree"
                           class="nav-link{{ $tree ? " active" : "" }}">
                            Списком
                        </a>
                    </li>
                </ul>
            </div>
            @if ($tree)
                @include("catalog::admin.categories.tree", ['categories' => $categories])
            @else
                @include("catalog::admin.categories.table-list", ['categories' => $categories])
            @endif
        </div>
    </div>
@endsection