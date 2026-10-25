@php($embed = $article->embed())

@if($embed !== null)
    @if(filled($embed['embed_url']))
        <div class="video-wrap @if($embed['platform'] === 'tiktok') video-wrap-tiktok @elseif($embed['platform'] === 'instagram') video-wrap-instagram @endif">
            <iframe
                src="{{ $embed['embed_url'] }}"
                title="{{ $article->title }}"
                loading="lazy"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                allowfullscreen
                referrerpolicy="strict-origin-when-cross-origin"></iframe>
        </div>
    @elseif($embed['platform'] === 'x')
        <div class="x-embed">
            <blockquote class="twitter-tweet">
                <a href="{{ $embed['watch_url'] }}">Ver publicación en X</a>
            </blockquote>
            <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
        </div>
    @else
        <a class="video-link-card" href="{{ $embed['watch_url'] }}" target="_blank" rel="noopener noreferrer">
            🎬 Ver el video en {{ ucfirst($embed['platform']) }} ↗
        </a>
    @endif
@endif
