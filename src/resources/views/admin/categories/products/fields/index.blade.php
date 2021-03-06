@extends('admin.layout')

@section('page-title', 'Поля - ')
@section('header-title', "{$product->title}")

@section('admin')
    @include("catalog::admin.categories.products.pills", ['category' => $category, 'product' => $product])

    <div class="col-12">
        <div class="card">
            @can("create", \App\ProductField::class)
                <div class="card-header">
                    <div class="btn-group"
                         role="group">
                        <a href="{{ route('admin.category.product.field.create', ['category' => $category, 'product' => $product]) }}"
                           class="btn btn-success">
                            Добавить
                        </a>
                    </div>
                </div>
            @endcan
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Поле</th>
                            <th>Значение</th>
                            @canany(["update", "delete"], \App\ProductField::class)
                                <th>Действия</th>
                            @endcan
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($product->fields as $field)
                            <tr>
                                <td>
                                    {{ $fields[$field->field_id]->title }} | {{ $fields[$field->field_id]->type_human }}
                                    @if (! empty($fields[$field->field_id]->group_id))
                                        ({{ $fields[$field->field_id]->group->title }})
                                    @endif
                                </td>
                                <td>{{ $field->value }}</td>
                                @canany(["update", "delete"], $field)
                                        <td>
                                        <div role="toolbar" class="btn-toolbar">
                                            <div class="btn-group mr-1">
                                                @can("update", $field)
                                                    <a href="{{ route('admin.category.product.field.edit', [
                                                                            'category' => $category,
                                                                            'product' => $product,
                                                                            'field' => $field
                                                                        ]) }}"
                                                       class="btn btn-primary">
                                                        <i class="far fa-edit"></i>
                                                    </a>
                                                @endcan
                                                @can("delete", $field)
                                                    <button type="button" class="btn btn-danger" data-confirm="{{ "delete-field-form-{$field->id}" }}">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                @endcan
                                            </div>
                                        </div>
                                        @can("delete", $field)
                                            <confirm-form :id="'{{ "delete-field-form-{$field->id}" }}'">
                                                <template>
                                                    <form action="{{ route('admin.category.product.field.destroy', [
                                                                            'category' => $category,
                                                                            'product' => $product,
                                                                            'field' => $field
                                                                        ]) }}"
                                                          id="delete-field-form-{{ $field->id }}"
                                                          class="btn-group"
                                                          method="post">
                                                        @csrf
                                                        <input type="hidden" name="_method" value="DELETE">
                                                    </form>
                                                </template>
                                            </confirm-form>
                                        @endcan
                                    </td>
                                @endcanany
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
