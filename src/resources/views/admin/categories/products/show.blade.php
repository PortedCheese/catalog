@extends('admin.layout')

@section('page-title', 'Просмотр - ')
@section('header-title', "{$product->title}")

@section('admin')
    @include("catalog::admin.categories.products.pills", ['category' => $category, 'product' => $product])
    <div class="col-12 mt-2">
        <div class="row">
            @php
                $class = $image ? "col-12 col-md-9" : "col-12";
            @endphp
            @if ($image)
                <div class="col-12 col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <img src="{{ route('imagecache', ['template' => 'medium', 'filename' => $image->file_name]) }}"
                                 class="img-thumbnail mb-2"
                                 alt="{{ $image->name }}">
                            <confirm-delete-model-button model-id="{{ $product->id }}">
                                <template slot="delete">
                                    <form action="{{ route('admin.category.product.destroy-image', [
                                                'category' => $category,
                                                'product' => $product
                                            ]) }}"
                                          id="delete-{{ $product->id }}"
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
            <div class="product-description {{ $class }}">
                <div class="card">
                    <div class="card-header">
                        <button type="button" class="btn btn-primary collapse show collapseChangeCategory" data-toggle="modal" data-target="#changeCategory">
                            Изменить категорию
                        </button>
                        <div class="collapse mt-3 collapseChangeCategory">
                            <form class="form-inline"
                                  method="post"
                                  action="{{ route("admin.category.product.change-category", ['category' => $category, 'product' => $product]) }}">
                                @csrf
                                @method('put')
                                <div class="input-group">
                                    <select name="category_id"
                                            id="category_id"
                                            class="custom-select">
                                        @foreach($categories as $key => $value)
                                            <option value="{{ $key }}"
                                                    @if ($key == $category->id)
                                                        selected
                                                    @elseif (old('category_id'))
                                                        selected
                                                    @endif>
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="input-group-append">
                                        <button class="btn btn-success" type="submit">Обновить</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="short mb-3">
                            <h4 class="mt-0">Краткое описание:</h4>
                            {{ $product->short }}
                        </div>
                        <div class="full">
                            <h4 class="mt-0">Полное описание:</h4>
                            {!! $product->description !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="changeCategory" tabindex="-1" role="dialog" aria-labelledby="changeCategoryLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changeCategoryLabel">Вы уверены?</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    При изменении категории, характеристики, которые отсутствуют в целевой категории, будут добавлены.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
                    <button type="button"
                            class="btn btn-primary"
                            data-dismiss="modal"
                            data-toggle="collapse"
                            data-target=".collapseChangeCategory"
                            aria-expanded="false"
                            aria-controls="collapseChangeCategory">
                        Понятно
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection