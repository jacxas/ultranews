@extends('layouts.app')

@section('title', 'Noticias de videos virales')

@php
    $platformMeta = [
        'youtube' => ['icon' => '▶', 'color' => '#ff0033', 'name' => 'YouTube'],
        'tiktok' => ['icon' => '♪', 'color' => '#25f4ee', 'name' => 'TikTok'],
        'instagram' => ['icon' => '◎', 'color' => '#e1306c', 'name' => 'Instagram'],
        'x' => ['icon' => '𝕏', 'color' => '#14171f', 'name' => 'X'],
        'vimeo' => ['icon' => 'V', 'color' => '#1ab7ea', 'name' => 'Vimeo'],
        'facebook' => ['icon' => 'f', 'color' => '#1877f2', 'name' => 'Facebook'],
        'external' => ['icon' => '↗', 'color' => '#8b93a5', 'name' => 'Otros'],
    ];
@endphp

@section('content')

    @if($featured)
        <section class="section">
            <div class="hero">
                <a class="hero-media" href="{{ route('articles.show', $featured) }}">
                    @if($featured->thumbnail_url)
                        <img src="{{ $featured->thumbnail_url }}" alt="{{ $featured->title }}">
                    @else
                        <div class="thumb-fallback">🎬</div>
                    @endif
                    @if($featured->video_platform)
                        <span class="platform-badge platform-{{ $featured->video_platform }}">
                            {{ ($platformMeta[$featured->video_platform] ?? null)['name'] ?? $featured->video_platform }}
                        </span>
                    @endif
                </a>
                <div class="hero-content">
                    <span class="hero-kicker">⚡ Destacada del día</span>
                    <h1 class="hero-title">
                        <a href="{{ route('articles.show', $featured) }}">{{ $featured->title }}</a>
                    </h1>
                    <p class="hero-excerpt">{{ $featured->excerpt }}</p>

                    <div>
                        @if($featured->category)
                            <a class="chip" style="--chip-color: {{ $featured->category->color }}"
                               href="{{ route('categories.show', $featured->category) }}">{{ $featured->category->name }}</a>
                        @endif
                    </div>

                    <div class="hero-meta">
                        <span>👁 {{ \App\Support\NumberFormat::compact($featured->views_count) }} vistas</span>
                        <span>❤ {{ \App\Support\NumberFormat::compact($featured->likes_count) }} likes</span>
                        @if($featured->published_at)
                            <span>🕒 {{ $featured->published_at->diffForHumans() }}</span>
                        @endif
                    </div>

                    <div>
                        <a class="btn" href="{{ route('articles.show', $featured) }}">Ver la noticia</a>
                    </div>
                </div>
            </div>
        </section>
    @elseif($trending->isEmpty() && $latest->isEmpty())
        <section class="section">
            <div class="empty-state">
                <span class="big">🍿</span>
                <strong>Todavía no hay noticias virales.</strong><br>
                Ejecutá <code>php artisan migrate --seed</code> para cargar el contenido de demostración,
                o <code>php artisan ultranews:fetch-viral</code> para importar los videos más populares de YouTube.
            </div>
        </section>
    @endif

    @if($trending->isNotEmpty())
        <section class="section">
            <div class="section-head">
                <h2 class="section-title"><span class="fire">🔥</span>Tendencias ahora</h2>
                <a class="section-link" href="{{ route('trending') }}">Ver ranking completo →</a>
            </div>

            <div class="trending-list">
                @foreach($trending->take(8) as $position => $item)
                    <div class="trending-item">
                        <span class="pos">{{ $position + 1 }}</span>
                        <div class="mini-thumb">
                            @if($item->thumbnail_url)
                                <img src="{{ $item->thumbnail_url }}" alt="" loading="lazy">
                            @else
                                <div class="thumb-fallback">🎬</div>
                            @endif
                        </div>
                        <div class="info">
                            <h3><a href="{{ route('articles.show', $item) }}">{{ $item->title }}</a></h3>
                            <span class="sub">
                                {{ $item->category?->name ?? 'Sin categoría' }}
                                · 👁 {{ \App\Support\NumberFormat::compact($item->views_count) }}
                                @if($item->video_platform)
                                    · {{ ($platformMeta[$item->video_platform] ?? null)['name'] ?? $item->video_platform }}
                                @endif
                            </span>
                        </div>
                        <span class="score-badge">⚡ {{ number_format($item->trending_score, 1, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    @if($platforms->isNotEmpty())
        <section class="section">
            <div class="section-head">
                <h2 class="section-title">📱 Virales por plataforma</h2>
            </div>
            <div class="platform-strip">
                @foreach($platforms as $platform)
                    @continue(($platform->platform ?? null) === null)
                    @php($meta = $platformMeta[$platform->platform] ?? null)
                    @continue($meta === null)
                    <a class="platform-link" href="{{ route('platform', $platform->platform) }}" style="--p-color: {{ $meta['color'] }}">
                        <span class="p-icon">{{ $meta['icon'] }}</span>
                        <span>
                            <span class="p-name">{{ $meta['name'] }}</span><br>
                            <span class="p-count">{{ $platform->total }} noticias · 👁 {{ \App\Support\NumberFormat::compact($platform->views ?? 0) }}</span>
                        </span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    @if($latest->isNotEmpty())
        <section class="section">
            <div class="section-head">
                <h2 class="section-title">🆕 Últimas noticias</h2>
                <a class="section-link" href="{{ route('search') }}">Buscar →</a>
            </div>
            <div class="grid-cards">
                @foreach($latest as $article)
                    @include('partials.article-card', ['article' => $article])
                @endforeach
            </div>
        </section>
    @endif

@endsection
