<div class="col-12">
    <form action="{{ route("site.catalog.category.show", ['category' => $category]) }}"
          method="get">
        @foreach ($category->getFilters() as $filter)
            @switch($filter->type)
                @case('select')
                    <div class="form-group">
                        <label for="{{ $filter->machine }}">{{ $filter->title }}</label>
                        <select name="select-{{ $filter->machine }}"
                                id="{{ $filter->machine }}"
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
                                       id="check-{{ "{$loop->iteration}-{$filter->machine}" }}"
                                       name="check-{{ "{$filter->machine}" }}[]">
                                <label class="custom-control-label" for="check-{{ "{$loop->iteration}-{$filter->machine}" }}">
                                    {{ $value }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                    @break

                @case('range')
                    <div class="form-group steps-slider-cover">
                        <label>{{ $filter->title }}</label>
                        <div class="row justify-content-between mb-2">
                            <div class="col-6">
                                <input type="number"
                                       name="range-from-{{ $filter->machine }}"
                                       step="10"
                                       min="{{ min($filter->values) }}"
                                       max="{{ max($filter->values) }}"
                                       data-value="{{ min($filter->values) }}"
                                       data-init="{{ $query->get("range-from-{$filter->machine}", min($filter->values)) }}"
                                       class="form-control from-input">
                            </div>
                            <div class="col-6">
                                <input type="number"
                                       name="range-to-{{ $filter->machine }}"
                                       step="10"
                                       min="{{ min($filter->values) }}"
                                       max="{{ max($filter->values) }}"
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
                    @break
            @endswitch
        @endforeach

        <div class="btn-group-vertical btn-block mt-2"
             role="group">
            <button type="submit" class="btn btn-primary">Применить</button>
            <a href="{{ route("site.catalog.category.show", ['category' => $category]) }}"
               class="btn btn-link">
                Сбросить
            </a>
        </div>
    </form>
</div>