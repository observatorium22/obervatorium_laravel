@extends('layouts.site')

@section('content')
    <div id="galery_list">
        @foreach ($featured->concat($projects) as $project)
            @if ($project->galleryUrl())
                <a class="black_a" href="{{ route('projects.images', $project) }}">
                    <div class="galery_image_container">
                        <img class="galery_image" src="{{ $project->galleryUrl() }}" alt="{{ $project->title }}">
                        <div class="galery_thumb_info">{{ $project->title }}<br>{{ $project->location }}</div>
                    </div>
                </a>
            @endif
        @endforeach
    </div>
@endsection
