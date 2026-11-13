@extends('layouts.site')

@section('title', 'About — Observatorium')

@section('content')
    @if ($selected)
        <article class="about_entry">
            <div class="about_side">
                @if ($selected->galleryUrl())
                    <img class="about_image" src="{{ $selected->galleryUrl() }}" alt="">
                @endif
                @include('partials.about-list')
            </div>
            <div class="about_text item_body">
                <div class="item_title">{{ $selected->title }}</div>
                {!! $selected->body !!}
                @include('partials.pdfs', ['pdfs' => $selected->pdfs])
            </div>
        </article>
    @else
        @include('partials.about-list')
    @endif
@endsection
