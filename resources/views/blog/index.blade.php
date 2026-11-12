@extends('layouts.site')

@section('title', 'Blog — Observatorium')

@section('content')
    @include('partials.entry', [
        'selected' => $selected,
        'listId' => 'blog_list',
        'items' => $items,
        'routeName' => 'blog.show',
        'imageRoute' => null,
        'label' => fn ($item) => trim(($item->date_label ? $item->date_label.': ' : '').$item->title),
    ])
@endsection
