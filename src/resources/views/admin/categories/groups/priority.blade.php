@extends("admin.layout")

@section("page-title", "Группы характеристик - ")

@section('header-title')
    Группы характеристик
@endsection

@section('admin')
    @include("catalog::admin.categories.groups.includes.pills")

    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <universal-priority
                        :elements="{{ json_encode($groups) }}"
                        url="{{ route("admin.vue.priority", ['table' => "category_field_groups", "field" => "weight"]) }}">
                </universal-priority>
            </div>
        </div>
    </div>
@endsection