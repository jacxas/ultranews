@php
    $platformIcons = [
        'youtube' => '▶ YouTube',
        'tiktok' => '♪ TikTok',
        'instagram' => '◎ Instagram',
        'x' => '𝕏',
        'vimeo' => 'V Vimeo',
        'facebook' => 'f Facebook',
        'external' => '↗ Enlace',
    ];
    $rank = $rank ?? null;
@endphp

<article class="card">
    <a class="card-media" href="{{ route('articles.show', $article) }}" tabindex="-1" aria-hidden="true">
        @if($article->thumbnail_url)
            <img src="{{ $article->thumbnail_url }}" alt="{{ $article->title }}" loading="lazy">
        @else
            <div class="thumb-fallback">🎬</div>
        @endif

        @if($article->video_platform)
            <span class="platform-badge platform-{{ $article->video_platform }}">
                {{ $platformIcons[$article->video_platform] ?? $article->video_platform }}
            </span>
        @endif

        @if($rank !== null)
            <span class="rank">#{{ $rank }}</span>
        @endif
    </a>

    <div class="card-body">
        @if($article->category)
            <a class="chip" style="--chip-color: {{ $article->category->color }}"
               href="{{ route('categories.show', $article->category) }}">{{ $article->category->name }}</a>
        @endif

        <h3 class="card-title">
            <a href="{{ route('articles.show', $article) }}">{{ $article->title }}</a>
        </h3>

        @if($article->excerpt)
            <p class="excerpt">{{ $article->excerpt }}</p>
        @endif

        <div class="card-meta">
            <span>👁 {{ \App\Support\NumberFormat::compact($article->views_count) }}</span>
            <span>❤ {{ \App\Support\NumberFormat::compact($article->likes_count) }}</span>
            <span>🔁 {{ \App\Support\NumberFormat::compact($article->shares_count) }}</span>
            @if($article->published_at)
                <span>🕒 {{ $article->published_at->diffForHumans(short: true) }}</span>
            @endif
        </div>
    </div>
</article>
