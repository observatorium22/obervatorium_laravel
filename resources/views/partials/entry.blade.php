@if ($selected)
    <article class="entry">
        <div class="entry_image">
            @if ($selected->galleryUrl())
                <img src="{{ $selected->galleryUrl() }}" alt="{{ $selected->title }}">
            @endif
            @if ($imageRoute && $selected->images->count() > 1)
                <a class="obs_list_item_title_a" href="{{ route($imageRoute, $selected) }}">MORE IMAGES</a>
            @endif
        </div>
        <div class="entry_text item_body">
            <div class="item_title">{{ $selected->title }}</div>
            @if ($selected->date_label)
                <div class="item_date">{{ $selected->date_label }}</div>
            @endif
            @if ($selected->subtitle)
                <div class="item_subtitle">{{ $selected->subtitle }}</div>
            @endif
            {!! $selected->body !!}
            @include('partials.pdfs', ['pdfs' => $selected->pdfs])
        </div>
    </article>
@endif

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
