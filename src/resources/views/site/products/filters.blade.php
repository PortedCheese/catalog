<div class="col-12">
    <form action="{{ route("site.catalog.category.show", ['category' => $category]) }}"
          method="get">
        @foreach ($filters as $filter)
            @switch($filter->type)
                @case('select')
                    <div class="form-group">
                        <label for="{{ $filter->machine }}{{ isset($modal) ?: "-main" }}">{{ $filter->title }}</label>
                        <select name="select-{{ $filter->machine }}"
                                id="{{ $filter->machine }}{{ isset($modal) ?: "-main" }}"
                                class="form-control custom-select">
                            <option value="">-- Выберите --</option>
                            @foreach($filter->values as $value)
                                <option value="{{ $value }}"
                                        @if($query->get("select-$filter->machine", '') == $value)
                                            selected
                                        @endif>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @break

                @case('checkbox')
                    <div class="form-group">
                        <label>{{ $filter->title }}</label>
                        @foreach($filter->values as $value)
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input"
                                       type="checkbox"
                                       @if (in_array($value, $query->get("check-{$filter->machine}", [])))
                                       checked
                                       @endif
                                       value="{{ $value }}"
                                       id="check-{{ "{$loop->iteration}-{$filter->machine}" }}{{ isset($modal) ?: "-main" }}"
                                       name="check-{{ "{$filter->machine}" }}[]">
                                <label class="custom-control-label" for="check-{{ "{$loop->iteration}-{$filter->machine}" }}{{ isset($modal) ?: "-main" }}">
                                    {{ $value }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                    @break

                @case('range')
                    @if ($filter->render)
                        <div class="form-group steps-slider-cover" data-step="1">
                            <label>{{ $filter->title }}</label>
                            <div class="row justify-content-between mb-2">
                                <div class="col-6">
                                    <input type="number"
                                           name="range-from-{{ $filter->machine }}"
                                           step="1"
                                           min="{{ (int) min($filter->values) }}"
                                           max="{{ (int) max($filter->values) }}"
                                           data-value="{{ min($filter->values) }}"
                                           data-init="{{ $query->get("range-from-{$filter->machine}", min($filter->values)) }}"
                                           class="form-control from-input">
                                </div>
                                <div class="col-6">
                                    <input type="number"
                                           name="range-to-{{ $filter->machine }}"
                                           step="1"
                                           min="{{ (int) min($filter->values) }}"
                                           max="{{ (int) max($filter->values) }}"
                                           data-value="{{ max($filter->values) }}"
                                           data-init="{{ $query->get("range-to-{$filter->machine}", max($filter->values)) }}"
                                           class="form-control to-input">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 mt-2">
                                    <div class="steps-slider"></div>
                                </div>
                            </div>
                        </div>
                    @endif
                    @break
            @endswitch
        @endforeach

        <input type="hidden" name="sort-by" value="{{ $sortField }}">
        <input type="hidden" name="sort-order" value="{{ $sortOrder }}">

        @if (count($filters))
            <div class="btn-group-vertical btn-block mt-2"
                 role="group">
                <button type="submit" class="btn btn-primary">Применить</button>
                <a href="{{ route("site.catalog.category.show", ['category' => $category]) }}"
                   class="btn btn-outline-secondary">
                    Сбросить
                </a>
            </div>
        @else
            <p class="text-muted">Нет параметров для фильтрации</p>
        @endif
    </form>
</div>