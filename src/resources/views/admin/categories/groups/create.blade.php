@extends("admin.layout")

@section("page-title", "Добавить группу - ")

@section('header-title')
    Добавить группу
@endsection

@section('admin')
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route("admin.category.groups.store") }}" method="post">
                    @csrf

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
                        <label for="machine">Машинное имя</label>
                        <input type="text"
                               id="machine"
                               name="machine"
                               value="{{ old('machine') }}"
                               class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="weight">Приоритет</label>
                        <input type="number"
                               step="1"
                               min="1"
                               id="weight"
                               name="weight"
                               value="{{ old('weight') ? old('weight') : $weight }}"
                               class="form-control">
                    </div>

                    <div class="btn-group"
                         role="group">
                        <button type="submit" class="btn btn-success">Создать</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection