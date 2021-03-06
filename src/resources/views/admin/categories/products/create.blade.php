@extends('admin.layout')

@section('page-title', 'Добавить товар - ')
@section('header-title', "{$category->title}")

@section('admin')
    @include("catalog::admin.categories.products.pills", ['category' => $category, 'product' => false])

    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.category.product.store', ['category' => $category]) }}"
                      enctype="multipart/form-data"
                      method="post">
                    @csrf

                    <input type="hidden" value="{{ $category->id }}" name="category_id">

                    <div class="form-group">
                        <label for="title">Заголовок <span class="text-danger">*</span></label>
                        <input type="text"
                               id="title"
                               name="title"
                               value="{{ old('title') }}"
                               required
                               class="form-control{{ $errors->has('title') ? ' is-invalid' : '' }}">
                        @if ($errors->has('title'))
                            <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('title') }}</strong>
                    </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="slug">Адресная строка</label>
                        <input type="text"
                               id="slug"
                               name="slug"
                               value="{{ old('slug') }}"
                               class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="short">Краткое описание</label>
                        <input type="text"
                               id="short"
                               name="short"
                               value="{{ old('short') }}"
                               class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="ckDescription">Описание <span class="text-danger">*</span></label>
                        <textarea class="form-control"
                                  name="description"
                                  id="ckDescription"
                                  rows="3">{{ old('description') }}</textarea>
                        @if ($errors->has('description'))
                            <input type="hidden" class="form-control is-invalid">
                            <div class="invalid-feedback">
                                <strong>{{ $errors->first('description') }}</strong>
                            </div>
                        @endif
                    </div>

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
                        <a href="{{ route('admin.category.product.index', ['category' => $category]) }}"
                           class="btn btn-secondary">
                            Товары
                        </a>
                        <button type="submit" class="btn btn-success">Добавить</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection
