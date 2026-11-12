@extends('layouts.site')

@section('title', 'Projects — Observatorium')

@section('content')
    <div class="split">
        <div class="split_main">
            @if ($selected)
                <div id="project_preview_item_image_container">
                    @if ($selected->galleryUrl())
                        <a href="{{ route('projects.images', $selected) }}">
                            <img class="project_preview_item_image" src="{{ $selected->galleryUrl() }}" alt="{{ $selected->title }}">
                        </a>
                    @endif
                </div>
                <div id="project_preview_item_text">
                    <div class="item_title">{{ $selected->title }}</div>
                    @if ($selected->subtitle)
                        <div class="item_subtitle">{{ $selected->subtitle }}</div>
                    @endif
                    @if ($selected->intro)
                        <div class="item_intro">{!! $selected->intro !!}</div>
                    @endif
                    <a class="obs_list_item_title_a" href="{{ route('projects.images', $selected) }}">OPEN</a>
                </div>
            @endif
        </div>
        <div id="project_list">
            @php $year = null; @endphp
            @foreach ($projects as $project)
                @if ($project->year !== $year)
                    @php $year = $project->year; @endphp
                    <div class="item_year">{{ $year }}</div>
                @endif
                <a id="pr_{{ $project->legacy_id }}"
                   class="{{ $selected && $selected->is($project) ? 'list_item_a_selected' : 'list_item_a' }}"
                   href="{{ route('projects.preview', $project) }}">{{ $project->title }}@if ($project->location) - {{ $project->location }}@endif</a><br>
            @endforeach
        </div>
    </div>
@endsection
