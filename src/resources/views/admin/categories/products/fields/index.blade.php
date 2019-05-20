@extends('admin.layout')

@section('page-title', 'Поля - ')
@section('header-title', "{$product->title}")

@section('admin')
    @include("catalog::admin.categories.products.pills", ['category' => $category, 'product' => $product])

    <div class="col-12">
        <div class="btn-group"
             role="group">
            <a href="{{ route('admin.category.product.field.create', [
                                                'category' => $category,
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
                    <th>Поле</th>
                    <th>Значение</th>
                    <th>Действия</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($product->fields as $field)
                    <tr>
                        <td>{{ $fields[$field->field_id]->title }}</td>
                        <td>{{ $field->value }}</td>
                        <td>
                            <confirm-delete-model-button model-id="{{ $field->id }}">
                                <template slot="edit">
                                    <a href="{{ route('admin.category.product.field.edit', [
                                                                    'category' => $category,
                                                                    'product' => $product,
                                                                    'field' => $field
                                                                ]) }}" class="btn btn-primary">
                                        <i class="far fa-edit"></i>
                                    </a>
                                </template>
                                <template slot="delete">
                                    <form action="{{ route('admin.category.product.field.destroy', [
                                                                    'category' => $category,
                                                                    'product' => $product,
                                                                    'field' => $field
                                                                ]) }}"
                                          id="delete-{{ $field->id }}"
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
