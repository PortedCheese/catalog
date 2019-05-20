@extends('admin.layout')

@section('page-title', 'Редактировать товар - ')
@section('header-title', "{$product->title}")

@section('admin')
    @include("catalog::admin.categories.products.pills", ['category' => $category, 'product' => $product])
    <div class="col-12">
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
                <label for="category_id">Категория</label>
                <select name="category_id"
                        id="category_id"
                        class="form-control chosen-select">
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
                <label for="description">Описание</label>
                <textarea class="form-control"
                          name="description"
                          id="ckDescription"
                          rows="3"
                          required>
                    {{ old('description') ? old('description') : $product->description }}
                </textarea>
                @if ($errors->has('description'))
                    <div class="invalid-feedback">
                        <strong>{{ $errors->first('description') }}</strong>
                    </div>
                @endif
            </div>

            @if ($image)
                <div class="form-group">
                    <img src="{{ route('imagecache', ['template' => 'small', 'filename' => $image->file_name]) }}"
                         class="img-thumbnail"
                         alt="{{ $image->name }}">
                </div>
            @endif

            <div class="form-group">
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
@endsection
