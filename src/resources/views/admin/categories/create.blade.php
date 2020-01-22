@extends('admin.layout')

@section('page-title', 'Добавить категорию - ')
@section('header-title')
    Добавить категорию{{ $category ? " для " . $category->title : "" }}
@endsection

@section('admin')
    @include("catalog::admin.categories.pills")
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.category.store') }}"
                      enctype="multipart/form-data"
                      method="post">
                    @csrf

                    @if($category)
                        <input type="hidden" name="parent_id" value="{{ $category->id }}">
                    @endif

                    <div class="form-group">
                        <label for="title">Заголовок</label>
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

                    <div class="form-group">
                        <label for="description">Описание</label>
                        <textarea class="form-control" id="description" name="description" rows="3">
                    {{ old('description') }}
                </textarea>
                    </div>

                    <div class="btn-group"
                         role="group">
                        <button type="submit" class="btn btn-success">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
