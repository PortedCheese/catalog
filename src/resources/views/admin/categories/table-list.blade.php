<div class="table-responsive">
    <table class="table">
        <thead>
        <tr>
            <th>Вес</th>
            <th>Заголовок</th>
            <th>Slug</th>
            <th>Дочернии</th>
            <th>Родитель</th>
            <th>Действия</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($categories as $category)
            <tr>
                <td>
                    <form action="{{ route('admin.category.change-weight', ['category' => $category]) }}"
                          style="width: 100px"
                          method="post">
                        @csrf
                        @method('put')
                        <div class="input-group mb-3">
                            <input type="number"
                                   step="1"
                                   min="0"
                                   class="form-control"
                                   required
                                   name="weight"
                                   value="{{ $category->weight }}">
                            <div class="input-group-append">
                                <button class="btn btn-outline-success"
                                        type="submit">
                                    <i class="far fa-save"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </td>
                <td>{{ $category->title }}</td>
                <td>{{ $category->slug }}</td>
                <td>{{ $category->children->count() }}</td>
                <td>
                    <form action="{{ route('admin.category.change-parent', ['category' => $category]) }}"
                          id="parent-{{ $category->id }}"
                          method="post"
                          style="width: 100px">
                        @csrf
                        @method('put')

                        <div class="input-group">
                            <select name="parent"
                                    id="parent"
                                    class="custom-select">
                                <option value="">-- Выбрать --</option>
                                @if ($category->parent)
                                    <option value="up">На уровень выше</option>
                                @endif
                                @foreach($parents as $key => $value)
                                    @if ($key != $category->id)
                                        <option value="{{ $key }}">
                                            {{ $value }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-outline-success">
                                    <i class="far fa-save"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </td>
                <td>
                    <confirm-delete-model-button model-id="{{ $category->id }}">
                        <template slot="edit">
                            <a href="{{ route('admin.category.edit', ['category' => $category]) }}"
                               class="btn btn-primary">
                                <i class="far fa-edit"></i>
                            </a>
                        </template>
                        <template slot="show">
                            <a href="{{ route('admin.category.show', ['category' => $category]) }}"
                               class="btn btn-dark">
                                <i class="far fa-eye"></i>
                            </a>
                        </template>
                        <template slot="delete">
                            <form action="{{ route('admin.category.destroy', ['category' => $category]) }}"
                                  id="delete-{{ $category->id }}"
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