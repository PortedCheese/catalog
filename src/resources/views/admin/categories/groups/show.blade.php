@extends("admin.layout")

@section("page-title", "Просмотр группы - ")

@section('header-title')
    Группа {{ $group->title }}
@endsection

@section('admin')
    @include("catalog::admin.categories.groups.includes.pills")

    <div class="col-12">
        <div class="card">
            @can("update", $group)
                <div class="card-header">
                    <form class="form-inline" action="{{ route("admin.category.groups.update", ['group' => $group]) }}" method="post">
                        @method('put')
                        @csrf()

                        <div class="form-group mb-2 mr-sm-2">
                            <label for="title" class="sr-only">Заголовок</label>
                            <input type="text"
                                   id="title"
                                   name="title"
                                   value="{{ old('title') ? old('title') : $group->title }}"
                                   required
                                   class="form-control{{ $errors->has('title') ? ' is-invalid' : '' }}">
                        </div>

                        <div class="btn-group mb-2 mr-sm-2"
                             role="group">
                            <button type="submit" class="btn btn-success">Обновить</button>
                        </div>
                    </form>
                    @if ($errors->has('weight'))
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $errors->first('weight') }}</strong>
                        </span>
                    @endif
                    @if ($errors->has('title'))
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $errors->first('title') }}</strong>
                        </span>
                    @endif
                </div>
            @endcan
            <div class="card-body">
                @can("view", \App\CategoryField::class)
                    @if ($fields->count())
                        <h5>Поля относящается к группе</h5>
                        <ul>
                            @foreach ($fields as $field)
                                <li>
                                    <a href="{{ route("admin.category.all-fields.show", ['field' => $field]) }}">
                                        {{ $field->title }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                @endcan
            </div>
        </div>
    </div>
@endsection

@section('links')
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div role="toolbar" class="btn-toolbar">
                    <div class="btn-group mr-1">
                        @can("delete", $group)
                            <button type="button" class="btn btn-danger" data-confirm="{{ "delete-form-{$group->id}" }}">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        @endcan
                    </div>
                </div>
                @can("delete", $group)
                    <confirm-form :id="'{{ "delete-form-{$group->id}" }}'">
                        <template>
                            <form action="{{ route('admin.category.groups.destroy', ['group' => $group]) }}"
                                  id="delete-form-{{ $group->id }}"
                                  class="btn-group"
                                  method="post">
                                @csrf
                                <input type="hidden" name="_method" value="DELETE">
                            </form>
                        </template>
                    </confirm-form>
                @endcan
            </div>
        </div>
    </div>
@endsection