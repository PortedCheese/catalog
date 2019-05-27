@extends('admin.layout')

@section('page-title', 'Добавить вариацию - ')
@section('header-title', "{$product->title}")

@section('admin')
    @include("catalog::admin.categories.products.pills", ['category' => $category, 'product' => $product])

    <div class="col-12">
        <form action="{{ route('admin.category.product.variation.store', [
                                        'category' => $category,
                                        'product' => $product
                                    ]) }}"
              method="post">
            @csrf

            <input type="hidden" name="product_id" value="{{ $product->id }}">

            <div class="form-group">
                <label for="sku">Артикул</label>
                <input type="text"
                       id="sku"
                       name="sku"
                       value="{{ old('sku') }}"
                       required
                       class="form-control{{ $errors->has('sku') ? ' is-invalid' : '' }}">
                @if ($errors->has('sku'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('sku') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group">
                <label for="description">Описание</label>
                <input type="text"
                       id="description"
                       name="description"
                       value="{{ old('description') }}"
                       class="form-control">
            </div>

            <div class="form-group">
                <label for="price">Цена</label>
                <input type="number"
                       step="0.01"
                       min="0"
                       id="price"
                       name="price"
                       value="{{ old('price') }}"
                       required
                       class="form-control{{ $errors->has('price') ? ' is-invalid' : '' }}">
                @if ($errors->has('price'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('price') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group">
                <label for="sale_price">Старая цена</label>
                <input type="number"
                       step="0.01"
                       min="0"
                       id="sale_price"
                       name="sale_price"
                       value="{{ old('sale_price') }}"
                       class="form-control">
            </div>

            <div class="form-check">
                <input type="checkbox"
                       @if(old('sale'))
                       checked
                       @endif
                       class="form-check-input"
                       value=""
                       name="sale"
                       id="sale">
                <label for="sale">
                    Действует скидка
                </label>
            </div>

            <div class="btn-group"
                 role="group">
                <button type="submit" class="btn btn-success">Добавить</button>
            </div>
        </form>
    </div>
@endsection
