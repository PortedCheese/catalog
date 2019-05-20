@extends('admin.layout')

@section('page-title', 'Редактировать значение - ')
@section('header-title', "{$product->title}")

@section('admin')
    @include("catalog::admin.categories.products.pills", ['category' => $category, 'product' => $product])

    <div class="col-12">
        <form action="{{ route('admin.category.product.field.update', [
                                                'category' => $category,
                                                'product' => $product,
                                                'field' => $field
                                            ]) }}"
              method="post">
            @csrf
            @method('put')

            <div class="form-group">
                <label for="value">Значение</label>
                <input type="text"
                       id="value"
                       name="value"
                       value="{{ old('value') ? old('value') : $field->value }}"
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
                <button type="submit" class="btn btn-success">Обновить</button>
            </div>
        </form>
    </div>
@endsection
