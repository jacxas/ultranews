@extends('layouts.app')

@section('title', $article->meta_title ?: $article->title)
@section('meta_description', $article->meta_description ?: \Illuminate\Support\Str::limit($article->excerpt ?? $article->title, 155))

@push('head')
    <meta property="og:title" content="{{ $article->title }}">
    <meta property="og:type" content="article">
    <meta property="og:url" content="{{ route('articles.show', $article) }}">
    @if($article->thumbnail_url)
        <meta property="og:image" content="{{ $article->thumbnail_url }}">
    @endif
    @if($article->excerpt)
        <meta property="og:description" content="{{ $article->excerpt }}">
    @endif
@endpush

@section('content')
    <nav class="breadcrumb" aria-label="Ruta de navegación">
        <a href="{{ route('home') }}">Inicio</a>
        @if($article->category)
            / <a href="{{ route('categories.show', $article->category) }}">{{ $article->category->name }}</a>
        @endif
        / <span>{{ \Illuminate\Support\Str::limit($article->title, 45) }}</span>
    </nav>

    <div class="article-page">
        <div>
            <article class="article-main">
                @unless($article->isPublished())
                    <p class="draft-banner">⚠️ Vista previa de administrador — esta noticia todavía no es pública.</p>
                @endunless

                <div class="article-head">
                    @if($article->category)
                        <a class="chip" style="--chip-color: {{ $article->category->color }}"
                           href="{{ route('categories.show', $article->category) }}">{{ $article->category->name }}</a>
                    @endif

                    <h1>{{ $article->title }}</h1>

                    <div class="article-meta">
                        @if($article->author)
                            <span>✍️ {{ $article->author->name }}</span>
                        @endif
                        @if($article->published_at)
                            <span>🕒 {{ $article->published_at->diffForHumans() }}</span>
                        @endif
                        @if($article->video_platform)
                            <span>📱 {{ ucfirst($article->video_platform) }}</span>
                        @endif
                    </div>
                </div>

                @include('partials.video-embed')

                <div class="stats-bar">
                    <span class="stat-pill">👁 <b>{{ number_format($article->views_count, 0, ',', '.') }}</b> vistas</span>
                    <span class="stat-pill">❤️ <b>{{ number_format($article->likes_count, 0, ',', '.') }}</b> likes</span>
                    <span class="stat-pill">🔁 <b>{{ number_format($article->shares_count, 0, ',', '.') }}</b> compartidos</span>
                    <span class="stat-pill">💬 <b>{{ number_format($article->comments_count, 0, ',', '.') }}</b> comentarios</span>
                    <span class="stat-pill hot">⚡ tendencia <b>{{ number_format($article->trending_score, 1, ',', '.') }}</b></span>
                </div>

                @if($article->excerpt)
                    <p class="article-lead">{{ $article->excerpt }}</p>
                @endif

                @if($article->body)
                    <div class="article-body">{{ $article->body }}</div>
                @endif

                <div class="article-foot">
                    @if($article->tags->isNotEmpty())
                        <div class="tags-list">
                            @foreach($article->tags as $tag)
                                <a class="tag" href="{{ route('tags.show', $tag) }}">#{{ $tag->name }}</a>
                            @endforeach
                        </div>
                    @endif

                    @if($article->source_url)
                        <p class="source-link">
                            Fuente original:
                            <a href="{{ $article->source_url }}" target="_blank" rel="noopener noreferrer">
                                {{ \Illuminate\Support\Str::limit($article->source_url, 60) }} ↗
                            </a>
                        </p>
                    @endif
                </div>
            </article>
        </div>

        <aside class="article-side">
            @if($related->isNotEmpty())
                <div class="side-box">
                    <h2>Relacionadas</h2>
                    <div class="side-list">
                        @foreach($related as $relatedItem)
                            <div class="side-item">
                                @if($relatedItem->thumbnail_url)
                                    <img src="{{ $relatedItem->thumbnail_url }}" alt="" loading="lazy">
                                @else
                                    <div class="side-thumb-fallback">🎬</div>
                                @endif
                                <div>
                                    <h4><a href="{{ route('articles.show', $relatedItem) }}">{{ $relatedItem->title }}</a></h4>
                                    <div class="sub">👁 {{ \App\Support\NumberFormat::compact($relatedItem->views_count) }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="side-box">
                <h2>Explorar plataformas</h2>
                <div class="side-list">
                    <div class="side-item"><div>📺 <a href="{{ route('platform', 'youtube') }}">Virales de YouTube</a></div></div>
                    <div class="side-item"><div>🎵 <a href="{{ route('platform', 'tiktok') }}">Virales de TikTok</a></div></div>
                    <div class="side-item"><div>📸 <a href="{{ route('platform', 'instagram') }}">Reels de Instagram</a></div></div>
                    <div class="side-item"><div>🐦 <a href="{{ route('platform', 'x') }}">Videos en X</a></div></div>
                </div>
            </div>

            <div class="side-box">
                <h2>🔥 Top tendencias</h2>
                <div class="side-list">
                    @foreach(\App\Models\Article::published()->trending()->limit(4)->get() as $trendItem)
                        <div class="side-item">
                            <div>
                                <h4><a href="{{ route('articles.show', $trendItem) }}">{{ $trendItem->title }}</a></h4>
                                <div class="sub">⚡ {{ number_format($trendItem->trending_score, 1, ',', '.') }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </aside>
    </div>
@endsection
