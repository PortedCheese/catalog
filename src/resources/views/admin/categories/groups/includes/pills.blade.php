<div class="col-12">
    <div class="card">
        <div class="card-body">
            <ul class="nav nav-pills">
                <li class="nav-item">
                    <a href="{{ route('admin.category.groups.index') }}"
                       class="nav-link{{ $currentRoute === "admin.category.groups.index" ? " active" : "" }}">
                        Список
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.category.groups.priority') }}"
                       class="nav-link{{ $currentRoute === "admin.category.groups.priority" ? " active" : "" }}">
                        Приоритет
                    </a>
                </li>

                @can("create", \App\CategoryFieldGroup::class)
                    <li class="nav-item">
                        <a href="{{ route('admin.category.groups.create') }}"
                           class="nav-link{{ $currentRoute === "admin.category.groups.create" ? " active" : "" }}">
                            Добавить
                        </a>
                    </li>
                @endcan

                @if (! empty($group))
                    @can("view", $group)
                        <li class="nav-item">
                            <a href="{{ route('admin.category.groups.show', ['group' => $group]) }}"
                               class="nav-link{{ $currentRoute === "admin.category.groups.show" ? " active" : "" }}">
                                Просмотр
                            </a>
                        </li>
                    @endcan
                @endif
            </ul>
        </div>
    </div>
</div>