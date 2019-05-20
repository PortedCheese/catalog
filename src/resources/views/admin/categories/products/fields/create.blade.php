@extends('admin.layout')

@section('page-title', 'Добавить значение - ')
@section('header-title', "{$product->title}")

@section('admin')
    @include("catalog::admin.categories.products.pills", ['category' => $category, 'product' => $product])

    <div class="col-12">
        <form action="{{ route('admin.category.product.field.store', [
                                                'category' => $category,
                                                'product' => $product
                                            ]) }}"
              method="post">
            @csrf

            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <input type="hidden" name="category_id" value="{{ $category->id }}">
            
            <div class="form-group">
                <label for="field_id">Характеристика</label>
                <select name="field_id"
                        id="field_id"
                        required
                        class="form-control">
                    <option value="">-- Выберите --</option>
                    @foreach($fields as $key => $value)
                        <option value="{{ $key }}"
                                @if(old('field_id'))
                                    selected
                                @endif>
                            {{ $value->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="value">Значение</label>
                <input type="text"
                       id="value"
                       name="value"
                       value="{{ old('value') }}"
                       required
                       class="form-control{{ $errors->has('value') ? ' is-invalid' : '' }}">
                @if ($errors->has('value'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('value') }}</strong>
                    </span>
                @endif
            </div>
            
            <div class="btn-group"
                 role="group">
                <button type="submit" class="btn btn-success">Добавить</button>
            </div>
        </form>
    </div>
@endsection
