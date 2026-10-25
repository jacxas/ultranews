@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="admin-page-head">
        <h1>Dashboard</h1>
        <div style="display:flex;gap:.5rem;flex-wrap:wrap">
            <a class="btn btn-small" href="{{ route('admin.articles.create') }}">+ Nueva noticia</a>
            <a class="btn btn-small btn-ghost" href="{{ route('admin.categories.create') }}">+ Nueva categoría</a>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card accent">
            <div class="stat-value">{{ number_format($stats['articles'], 0, ',', '.') }}</div>
            <div class="stat-label">Noticias</div>
        </div>
        <div class="stat-card success">
            <div class="stat-value">{{ number_format($stats['published'], 0, ',', '.') }}</div>
            <div class="stat-label">Publicadas</div>
        </div>
        <div class="stat-card warning">
            <div class="stat-value">{{ number_format($stats['drafts'], 0, ',', '.') }}</div>
            <div class="stat-label">Borradores</div>
        </div>
        <div class="stat-card neon">
            <div class="stat-value">{{ \App\Support\NumberFormat::compact($stats['views']) }}</div>
            <div class="stat-label">Vistas totales</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $stats['categories'] }}</div>
            <div class="stat-label">Categorías</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $stats['tags'] }}</div>
            <div class="stat-label">Etiquetas</div>
        </div>
    </div>

    <div class="panels-row">
        <div class="panel">
            <div class="panel-head">
                <h2 class="panel-title">🔥 Top tendencias</h2>
                <a class="section-link" href="{{ route('trending') }}" target="_blank">Ver en el sitio →</a>
            </div>
            <div class="table-scroll">
                <table class="table-admin">
                    <thead>
                        <tr><th>#</th><th>Noticia</th><th>Plataforma</th><th>Vistas</th><th>Score</th></tr>
                    </thead>
                    <tbody>
                        @forelse($topTrending as $position => $item)
                            <tr>
                                <td class="strong">{{ $position + 1 }}</td>
                                <td class="strong">
                                    <a href="{{ route('admin.articles.edit', $item) }}">{{ $item->title }}</a>
                                </td>
                                <td>{{ $item->video_platform ?? '—' }}</td>
                                <td>{{ \App\Support\NumberFormat::compact($item->views_count) }}</td>
                                <td><span class="score-badge">⚡ {{ number_format($item->trending_score, 1, ',', '.') }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="5">Todavía no hay noticias publicadas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="panel">
            <div class="panel-head">
                <h2 class="panel-title">🕒 Últimas noticias</h2>
                <a class="section-link" href="{{ route('admin.articles.index') }}">Gestionar →</a>
            </div>
            <div class="table-scroll">
                <table class="table-admin">
                    <thead>
                        <tr><th>Noticia</th><th>Estado</th><th>Fecha</th><th></th></tr>
                    </thead>
                    <tbody>
                        @forelse($latestArticles as $item)
                            <tr>
                                <td class="strong">{{ \Illuminate\Support\Str::limit($item->title, 42) }}</td>
                                <td>
                                    @if($item->status === 'published')
                                        <span class="badge-status badge-published">publicada</span>
                                    @else
                                        <span class="badge-status badge-draft">borrador</span>
                                    @endif
                                </td>
                                <td>{{ $item->published_at?->format('d/m H:i') ?? $item->created_at?->format('d/m H:i') }}</td>
                                <td>
                                    <div class="actions-row">
                                        <a class="btn-tiny" href="{{ route('admin.articles.edit', $item) }}">Editar</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4">Sin noticias.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="panel">
            <div class="panel-head">
                <h2 class="panel-title">📱 Por plataforma</h2>
            </div>
            <div class="table-scroll">
                <table class="table-admin">
                    <thead>
                        <tr><th>Plataforma</th><th>Noticias</th></tr>
                    </thead>
                    <tbody>
                        @forelse($byPlatform as $platform)
                            <tr>
                                <td class="strong">{{ ucfirst($platform->platform) }}</td>
                                <td>{{ $platform->total }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2">Sin videos cargados todavía.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="panel-body" style="border-top:1px solid var(--border)">
                <p class="form-help" style="margin:0">
                    💡 Importá los videos más populares de YouTube con:
                    <code>php artisan ultranews:fetch-viral --region=AR</code>
                    <br>(requiere <code>YOUTUBE_API_KEY</code> en el <code>.env</code>)
                </p>
            </div>
        </div>
    </div>
@endsection
