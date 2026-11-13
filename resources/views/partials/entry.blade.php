@if ($selected)
    <article class="entry">
        <div class="entry_image">
            @if ($selected->galleryUrl())
                <img src="{{ $selected->galleryUrl() }}" alt="{{ $selected->title }}">
            @endif
            @if ($imageRoute && $selected->images->count() > 1)
                <a class="obs_list_item_title_a" href="{{ route($imageRoute, $selected) }}">MORE IMAGES</a>
            @endif
            @if ($listUnderImage ?? false)
                @include('partials.entry-list')
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

@if (! ($listUnderImage ?? false) || ! $selected)
    @include('partials.entry-list')
@endif
