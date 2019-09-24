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
                                    <div role="toolbar" class="btn-toolbar">
                                        <div class="btn-group mr-1">
                                            <a href="{{ route('admin.category.groups.show', ['group' => $group]) }}" class="btn btn-dark">
                                                <i class="far fa-eye"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger" data-confirm="{{ "delete-form-{$group->id}" }}">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </div>
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