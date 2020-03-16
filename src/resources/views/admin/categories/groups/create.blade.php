@extends("admin.layout")

@section("page-title", "Добавить группу - ")

@section('header-title')
    Добавить группу
@endsection

@section('admin')
    @include("catalog::admin.categories.groups.includes.pills")

    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route("admin.category.groups.store") }}" method="post">
                    @csrf
                    <input type="hidden" name="weight" value="{{ $weight }}">

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

                    @can("settings-management")
                        <div class="form-group">
                            <label for="machine">Машинное имя</label>
                            <input type="text"
                                   id="machine"
                                   name="machine"
                                   value="{{ old('machine') }}"
                                   class="form-control{{ $errors->has('machine') ? ' is-invalid' : '' }}">
                            @if ($errors->has('machine'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('machine') }}</strong>
                                </span>
                            @endif
                        </div>
                    @endcan

                    <div class="btn-group"
                         role="group">
                        <button type="submit" class="btn btn-success">Создать</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection