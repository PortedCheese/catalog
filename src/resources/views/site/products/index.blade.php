<div class="row">
    @foreach ($products as $item)
        <div class="col-12 col-sm-6 col-md-4 col-lg-4 mb-3">
            {!! $item->getTeaser() !!}
        </div>
    @endforeach
</div>
<div class="row">
    {{ $products->links() }}
</div>