@extends('admin.layout')

@section('page-title', 'Редактировать товар - ')
@section('header-title', "{$product->title}")

@section('admin')
    @include("catalog::admin.categories.products.pills", ['category' => $category, 'product' => $product])
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.category.product.update', ['category' => $category, 'product' => $product]) }}"
                      enctype="multipart/form-data"
                      method="post">
                    @csrf
                    @method('put')

                    <div class="form-group">
                        <label for="title">Заголовок</label>
                        <input type="text"
                               id="title"
                               name="title"
                               value="{{ old('title') ? old('title') : $product->title }}"
                               required
                               class="form-control{{ $errors->has('title') ? ' is-invalid' : '' }}">
                        @if ($errors->has('title'))
                            <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('title') }}</strong>
                    </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="slug">Slug</label>
                        <input type="text"
                               id="slug"
                               name="slug"
                               value="{{ old('slug') ? old('slug') : $product->slug }}"
                               class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Метки</label>
                        @foreach($states as $state)
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input"
                                       type="checkbox"
                                       @if (old('check-' . $state->id))
                                       checked
                                       @elseif(in_array($state->id, $productStateIds))
                                       checked
                                       @endif
                                       value="{{ $state->id }}"
                                       id="check-{{ $state->id }}"
                                       name="check-{{ $state->id }}">
                                <label class="custom-control-label" for="check-{{ $state->id }}">
                                    {{ $state->title }}
                                </label>
                            </div>
                        @endforeach
                    </div>

                    <div class="form-group">
                        <label for="short">Краткое описание</label>
                        <input type="text"
                               id="short"
                               name="short"
                               value="{{ old('short') ? old('short') : $product->short }}"
                               class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="ckDescription">Описание</label>
                        <textarea class="form-control"
                                  name="description"
                                  id="ckDescription"
                                  rows="3"
                                  required>
                            {{ old('description') ? old('description') : $product->description }}
                        </textarea>
                        @if ($errors->has('description'))
                            <input type="hidden" class="form-control is-invalid">
                            <div class="invalid-feedback">
                                <strong>{{ $errors->first('description') }}</strong>
                            </div>
                        @endif
                    </div>

                    @if ($image)
                        <div class="form-group">
                            <div class="d-inline-block">
                                <img src="{{ route('imagecache', ['template' => 'small', 'filename' => $image->file_name]) }}"
                                     class="rounded mb-2"
                                     alt="{{ $image->name }}">
                                <button type="button" class="close ml-1" data-confirm="{{ "delete-image-form-{$category->id}" }}">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <confirm-form :id="'{{ "delete-image-form-{$category->id}" }}'">
                                <template>
                                    <form action="{{ route('admin.category.product.destroy-image', [
                                                'category' => $category,
                                                'product' => $product
                                            ]) }}"
                                          id="delete-image-form-{{ $category->id }}"
                                          class="btn-group"
                                          method="post">
                                        @csrf
                                        <input type="hidden" name="_method" value="DELETE">
                                    </form>
                                </template>
                            </confirm-form>
                        </div>
                    @endif

                    <div class="form-group">
                        <label for="custom-file-input">Главное изображение</label>
                        <div class="custom-file">
                            <input type="file"
                                   class="custom-file-input{{ $errors->has('main_image') ? ' is-invalid' : '' }}"
                                   id="custom-file-input"
                                   lang="ru"
                                   name="main_image"
                                   aria-describedby="inputGroupMainImage">
                            <label class="custom-file-label"
                                   for="custom-file-input">
                                Выберите файл изображения
                            </label>
                            @if ($errors->has('main_image'))
                                <div class="invalid-feedback">
                                    <strong>{{ $errors->first('main_image') }}</strong>
                                </div>
                            @endif
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
