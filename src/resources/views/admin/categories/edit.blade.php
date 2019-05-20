@extends('admin.layout')

@section('page-title', 'Обновить категорию - ')
@section('header-title')
    {{ $category->title }}
@endsection

@section('admin')
    <div class="col-12">
        <form action="{{ route('admin.category.update', ['category' => $category]) }}"
              enctype="multipart/form-data"
              method="post">
            @csrf
            @method('put')

            <div class="form-group">
                <label for="title">Заголовок</label>
                <input type="text"
                       id="title"
                       name="title"
                       value="{{ old('title') ? old('title') : $category->title }}"
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
                       value="{{ old('slug') ? old('slug') : $category->slug }}"
                       class="form-control">
            </div>

            @if ($image)
                <div class="form-group">
                    <img src="{{ route('imagecache', ['template' => 'small', 'filename' => $image->file_name]) }}"
                         class="img-thumbnail"
                         alt="{{ $image->name }}">
                </div>
            @endif

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

            <div class="form-group">
                <label for="description">Описание</label>
                <textarea class="form-control" name="description" rows="3">{{ old('description') ? old('description') : $category->description }}</textarea>
            </div>

            <div class="btn-group"
                 role="group">
                <button type="submit" class="btn btn-success">Обновить</button>
                <a href="{{ route('admin.category.show', ['category' => $category]) }}" class="btn btn-dark">
                    Назад к категории
                </a>
            </div>
        </form>
    </div>
@endsection
