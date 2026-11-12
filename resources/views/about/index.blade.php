@extends('layouts.site')

@section('title', 'About — Observatorium')

@section('content')
    @if ($selected)
        <article class="about_entry">
            @if ($selected->galleryUrl())
                <img class="about_image" src="{{ $selected->galleryUrl() }}" alt="">
            @endif
            <div class="about_text item_body">
                <div class="item_title">{{ $selected->title }}</div>
                {!! $selected->body !!}
                @include('partials.pdfs', ['pdfs' => $selected->pdfs])
            </div>
        </article>
    @endif

    <div id="observatorium_list">
        @foreach ($items as $item)
            <a class="{{ $selected && $selected->is($item) ? 'obs_list_item_title_a_selected' : 'obs_list_item_title_a' }}"
               href="{{ route('about.show', $item) }}">{{ $item->title }}</a><br>
        @endforeach
        <p><a class="obs_list_item_title_a" href="{{ route('newsletter') }}" target="_blank" rel="noopener">Subscribe to our newsletter</a></p>
    </div>
@endsection
