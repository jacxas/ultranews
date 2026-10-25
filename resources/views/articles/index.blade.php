@extends('layouts.app')

@section('title', $title)

@section('content')
    <div class="page-head">
        <h1 class="page-title">{{ $title }}</h1>
        @if(! empty($subtitle))
            <p class="page-subtitle">{{ $subtitle }}</p>
        @endif
    </div>

    @if($articles->isEmpty())
        <div class="empty-state">
            <span class="big">🕵️</span>
            No hay noticias para mostrar acá (todavía).
        </div>
    @else
        <div class="grid-cards">
            @foreach($articles as $index => $article)
                @include('partials.article-card', [
                    'article' => $article,
                    'rank' => ($showRank ?? false) ? $articles->firstItem() + $index : null,
                ])
            @endforeach
        </div>

        <div class="pagination-wrap">
            {{ $articles->links() }}
        </div>
    @endif
@endsection
