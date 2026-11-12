@extends('layouts.site')

@section('title', $project->title.' — Observatorium')

@section('content')
    <div class="project_page">
        @php
            $count = $project->images->count();
            $next = $count ? ($index + 1) % $count : 0;
            $prev = $count ? ($index - 1 + $count) % $count : 0;
        @endphp
        <div class="viewer_bar">
            <span class="item_smalltitle">{{ $project->title }}</span>
            @if ($count)
                <span class="image_nav item_smalltitle">
                    <a id="arrow_prj_prev_img" href="{{ route('projects.images', ['project' => $project, 'image' => $prev]) }}">&lt;</a>
                    <span id="prj_slides_fromto">{{ $index + 1 }} / {{ $count }}</span>
                    <a id="arrow_prj_next_img" href="{{ route('projects.images', ['project' => $project, 'image' => $next]) }}">&gt;</a>
                </span>
            @endif
        </div>
        @if ($count)
            <div class="project_slideshow" data-index="{{ $index }}">
                <div class="project_slideshow_track">
                    @foreach ($project->images as $slide)
                        <div class="project_slide">
                            <img class="viewer_image" src="{{ $slide->url() }}" alt="{{ $slide->title ?: $project->title }}">
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="text_columns project_text">
            <div id="project_item_text_intro_container">
                <div class="item_title">{{ $project->title }}</div>
                @if ($project->year || $project->location)
                    <div class="item_subtitle">{{ $project->year }}@if ($project->year && $project->location) - @endif{{ $project->location }}</div>
                @endif
                @if ($project->subtitle)
                    <div class="item_subtitle">{{ $project->subtitle }}</div>
                @endif
                @if ($project->intro)
                    <div class="item_intro"><strong>{!! $project->intro !!}</strong></div>
                @endif
                @include('partials.pdfs', ['pdfs' => $project->pdfs])
            </div>
            <div id="project_item_text_body_container" class="item_body">
                {!! $project->body !!}
            </div>
            <div id="project_item_text_body2_container" class="item_body">
                {!! $project->body2 !!}
            </div>
        </div>
    </div>
@endsection
