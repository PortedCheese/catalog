@extends('admin.layout')

@section('page-title', 'Вариации - ')
@section('header-title', "{$product->title}")

@section('admin')
    @include("catalog::admin.categories.products.pills", ['category' => $category, 'product' => $product])
    
    <div class="col-12">
        <div class="btn-group"
             role="group">
            <a href="{{ route('admin.category.product.variation.create', [
                                    'categroy' => $category,
                                    'product' => $product,
                                ]) }}"
               class="btn btn-success">
                Добавить
            </a>
        </div>
    </div>
    
    <div class="col-12">
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>SKU</th>
                    <th>Цена</th>
                    <th>Старая цена</th>
                    <th>Скидка</th>
                    <th>Описание</th>
                    <th>В наличии</th>
                    <th>Действия</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($variations as $variation)
                    <tr>
                        <td>{{ $variation->sku }}</td>
                        <td>{{ $variation->price }}</td>
                        <td>{{ $variation->sale_price }}</td>
                        <td>{{ $variation->sale ? 'Да' : "Нет" }}</td>
                        <td>{{ $variation->description }}</td>
                        <td>{{ $variation->available ? "Да" : "Нет" }}</td>
                        <td>
                            <confirm-delete-model-button model-id="{{ $variation->id }}">
                                <template slot="edit">
                                    <a href="{{ route('admin.category.product.variation.edit', [
                                                            'variation' => $variation,
                                                            'product' => $product,
                                                            'category' => $category,
                                                        ]) }}"
                                       class="btn btn-primary">
                                        <i class="far fa-edit"></i>
                                    </a>
                                </template>
                                <template slot="delete">
                                    <form action="{{ route('admin.category.product.variation.destroy', [
                                                            'variation' => $variation,
                                                            'product' => $product,
                                                            'category' => $category,
                                                        ]) }}"
                                          id="delete-{{ $variation->id }}"
                                          class="btn-group"
                                          method="post">
                                        @csrf
                                        <input type="hidden" name="_method" value="DELETE">
                                    </form>
                                </template>
                            </confirm-delete-model-button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
