<div class="col-12">
    <div class="my-2">
        <div class="pr-2 d-inline-block">
            <h5>Сортировка:</h5>
        </div>
        <div class="d-inline-block">
            @if (! $disablePriceSort)
                @sortLink(['name' => "По цене", "key" => "price"])
            @endif
            @sortLink(['name' => "По названию", "key" => "title"])
            @sortLink(['name' => "По новизне", "key" => "created_at"])
        </div>
    </div>
</div>