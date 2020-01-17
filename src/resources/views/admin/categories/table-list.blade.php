<div class="table-responsive">
    <table class="table">
        <thead>
        <tr>
            <th>Заголовок</th>
            <th>Адресная строка</th>
            <th>Дочернии</th>
            <th>Действия</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($categories as $category)
            <tr>
                <td>{{ $category->title }}</td>
                <td>{{ $category->slug }}</td>
                <td>{{ $category->children->count() }}</td>
                <td>
                    <div role="toolbar" class="btn-toolbar">
                        <div class="btn-group mr-1">
                            <a href="{{ route("admin.category.edit", ["category" => $category]) }}" class="btn btn-primary">
                                <i class="far fa-edit"></i>
                            </a>
                            <a href="{{ route('admin.category.show', ['category' => $category]) }}" class="btn btn-dark">
                                <i class="far fa-eye"></i>
                            </a>
                            <button type="button" class="btn btn-danger" data-confirm="{{ "delete-form-{$category->id}" }}">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                    <confirm-form :id="'{{ "delete-form-{$category->id}" }}'">
                        <template>
                            <form action="{{ route('admin.category.destroy', ['category' => $category]) }}"
                                  id="delete-form-{{ $category->id }}"
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