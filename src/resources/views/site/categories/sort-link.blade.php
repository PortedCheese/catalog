@php($reverse = !empty($reverse) ? $reverse : false)
<a href="{{ $sortUrl }}{{ $noParams ? "?" : "&" }}sort-by={{ $key }}&sort-order={{ $sortField != $key ? ($reverse ? "asc" : "desc") : ($sortOrder == "asc" ? "desc" : "asc") }}"
   class="btn btn-{{ $sortField != $key ? "outline-" : "" }}secondary btn-sm mb-2">
    {{ $name }}
    @if ($sortField == $key)
        <i class="fas fa-long-arrow-alt-{{ $sortOrder == "desc" ? "down" : "up" }}"></i>
    @elseif ($reverse)
        <i class="fas fa-long-arrow-alt-up"></i>
    @else
        <i class="fas fa-long-arrow-alt-down"></i>
    @endif
</a>