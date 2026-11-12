@extends('layouts.site')

@section('title', 'News — Observatorium')

@section('content')
    @include('partials.entry', [
        'selected' => $selected,
        'listId' => 'news_list',
        'items' => $items,
        'routeName' => 'news.show',
        'imageRoute' => 'news.images',
        'label' => fn ($item) => trim(($item->date_label ? $item->date_label.': ' : '').$item->title),
    ])
@endsection
