<div id="{{ $listId }}" class="entry_list">
    @php $year = null; @endphp
    @foreach ($items as $item)
        @if ($item->year !== $year)
            @php $year = $item->year; @endphp
            <span class="year_label">{{ $year }}</span><br>
        @endif
        <a class="{{ $selected && $selected->is($item) ? 'list_item_a_selected' : 'list_item_a' }}"
           href="{{ route($routeName, $item) }}">{{ $label($item) }}</a><br>
    @endforeach
</div>
