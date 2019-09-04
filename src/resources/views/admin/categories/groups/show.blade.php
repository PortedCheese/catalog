@extends("admin.layout")

@section("page-title", "Просмотр - ")

@section('header-title')
    {{ $group->title }}
@endsection

@section('admin')
    <div class="col-12">
        <div class="card">
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

                    <div class="form-group mb-2 mr-sm-2">
                        <label for="weight" class="sr-only">Приоритет</label>
                        <input type="number"
                               min="1"
                               step="1"
                               id="weight"
                               name="weight"
                               value="{{ old('weight') ? old('weight') : $group->weight }}"
                               required
                               class="form-control{{ $errors->has('weight') ? ' is-invalid' : '' }}">
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
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">Заголовок</dt>
                    <dd class="col-sm-9">{{ $group->title }}</dd>

                    <dt class="col-sm-3">Машинное имя</dt>
                    <dd class="col-sm-9">{{ $group->machine }}</dd>

                    <dt class="col-sm-3">Приоритет</dt>
                    <dd class="col-sm-9">{{ $group->weight }}</dd>
                </dl>
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
            </div>
        </div>
    </div>
@endsection

@section('links')
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <confirm-delete-model-button model-id="{{ $group->id }}">
                    <template slot="delete">
                        <form action="{{ route('admin.category.groups.destroy', ['group' => $group]) }}"
                              id="delete-{{ $group->id }}"
                              class="btn-group"
                              method="post">
                            @csrf
                            <input type="hidden" name="_method" value="DELETE">
                        </form>
                    </template>
                    <template slot="other">
                        <a href="{{ route('admin.category.groups.index') }}" class="btn btn-dark">К списку</a>
                    </template>
                </confirm-delete-model-button>
            </div>
        </div>
    </div>
@endsection