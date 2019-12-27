@extends('admin.layout')

@section('page-title', 'Обновить вариацию - ')
@section('header-title', "{$product->title}")

@section('admin')
    @include("catalog::admin.categories.products.pills", ['category' => $category, 'product' => $product])

    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.category.product.variation.update', ['category' => $category, 'product' => $product, 'variation' => $variation ]) }}"
                      method="post">
                    @csrf
                    @method('put')

                    <div class="form-group">
                        <label for="sku">Артикул</label>
                        <input type="text"
                               id="sku"
                               name="sku"
                               value="{{ old('sku') ? old('sku') : $variation->sku }}"
                               class="form-control{{ $errors->has('sku') ? ' is-invalid' : '' }}">
                        @if ($errors->has('sku'))
                            <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('sku') }}</strong>
                    </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="description">Описание <span class="text-danger">*</span></label>
                        <input type="text"
                               id="description"
                               name="description"
                               value="{{ old('description') ? old('description') : $variation->description }}"
                               required
                               class="form-control{{ $errors->has('description') ? ' is-invalid' : '' }}">
                        @if ($errors->has('description'))
                            <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('description') }}</strong>
                    </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="price">Цена <span class="text-danger">*</span></label>
                        <input type="number"
                               step="0.01"
                               min="0"
                               id="price"
                               name="price"
                               value="{{ old('price') ? old('price') : number_format($variation->price, 2, '.', '') }}"
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
                               value="{{ old('sale_price') ? old('sale_price') : number_format($variation->sale_price, 2, '.', '') }}"
                               class="form-control">
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox"
                                   class="custom-control-input"
                                   id="sale"
                                   {{ (! count($errors->all()) && $variation->sale) || old("sale") ? "checked" : "" }}
                                   name="sale">
                            <label class="custom-control-label" for="sale">Действует скидка</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox"
                                   class="custom-control-input"
                                   id="available"
                                   {{ (! count($errors->all()) && $variation->available) || old("available") ? "checked" : "" }}
                                   name="available">
                            <label class="custom-control-label" for="available">В наличии</label>
                        </div>
                    </div>

                    <div class="btn-group"
                         role="group">
                        <button type="submit" class="btn btn-success">Обновить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
