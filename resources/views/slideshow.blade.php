@extends('layouts.site')

@section('title', ($record->title ?: 'Images').' — Observatorium')

@section('content')
    <div class="viewer">
        <div class="viewer_bar">
            <span class="item_smalltitle">{{ $record->title }}</span>
            @if ($record->images->isNotEmpty())
                <span class="item_smalltitle">{{ $index + 1 }} / {{ $record->images->count() }}</span>
            @endif
            <a class="obs_list_item_title_a" href="{{ $backUrl }}">TEXT</a>
        </div>
        @if ($image)
            @php $next = ($index + 1) % $record->images->count(); @endphp
            <a href="{{ route('news.images', ['news' => $record, 'image' => $next]) }}">
                <img class="viewer_image" src="{{ $image->url() }}" alt="{{ $image->title ?: $record->title }}">
            </a>
        @endif
    </div>
@endsection
