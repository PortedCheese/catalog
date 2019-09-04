@extends("admin.layout")

@section("page-title", "Группы характеристик - ")

@section('header-title')
    Группы характеристик
@endsection

@section('admin')
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="btn-group"
                     role="group">
                    <a href="{{ route("admin.category.groups.create") }}" class="btn btn-success">Добавить</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Заголовок</th>
                            <th>Машинное имя</th>
                            <th>Приоритет</th>
                            <th>Действия</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($groups as $group)
                            <tr>
                                <td>{{ $group->title }}</td>
                                <td>{{ $group->machine }}</td>
                                <td>{{ $group->weight }}</td>
                                <td>
                                    <confirm-delete-model-button model-id="{{ $group->id }}">
                                        <template slot="show">
                                            <a href="{{ route('admin.category.groups.show', ['group' => $group]) }}" class="btn btn-dark">
                                                <i class="far fa-eye"></i>
                                            </a>
                                        </template>
                                        <template slot="delete">
                                            <form action="{{ route('admin.category.groups.destroy', ['group' => $group]) }}"
                                                  id="delete-{{ $group->id }}"
                                                  class="btn-group"
                                                  method="post">
                                                @csrf
                                                <input type="hidden" name="_method" value="DELETE">
                                            </form>
                                        </template>
                                    </confirm-delete-model-button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('links')
    @if ($groups->lastPage() > 1)
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    {{ $groups->links() }}
                </div>
            </div>
        </div>
    @endif
@endsection