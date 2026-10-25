@extends('layouts.admin')

@section('title', 'Noticias')

@section('content')
    <div class="admin-page-head">
        <h1>Noticias {{ $trashed ? '· papelera' : '' }}</h1>
        <a class="btn btn-small" href="{{ route('admin.articles.create') }}">+ Nueva noticia</a>
    </div>

    <div class="panel">
        <div class="panel-head">
            <form class="filter-bar" method="GET" action="{{ route('admin.articles.index') }}">
                <input class="form-input" type="search" name="q" value="{{ $term }}" placeholder="Buscar por título...">
                <select class="form-select" name="status">
                    <option value="">Todos los estados</option>
                    <option value="published" @selected($status === 'published')>Publicadas</option>
                    <option value="draft" @selected($status === 'draft')>Borradores</option>
                </select>
                <label class="form-check">
                    <input type="checkbox" name="trashed" value="1" @checked($trashed)>
                    Ver papelera
                </label>
                <button class="btn btn-small btn-ghost" type="submit">Filtrar</button>
            </form>
        </div>

        <div class="table-scroll">
            <table class="table-admin">
                <thead>
                    <tr>
                        <th>Video</th>
                        <th>Título</th>
                        <th>Categoría</th>
                        <th>Plat.</th>
                        <th>Estado</th>
                        <th>Vistas</th>
                        <th>Score</th>
                        <th>Publicada</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($articles as $article)
                        <tr>
                            <td>
                                @if($article->thumbnail_url)
                                    <img class="table-thumb" src="{{ $article->thumbnail_url }}" alt="" loading="lazy">
                                @else
                                    <div class="table-thumb-fallback">🎬</div>
                                @endif
                            </td>
                            <td class="strong">
                                <a href="{{ route('admin.articles.edit', $article) }}">
                                    {{ \Illuminate\Support\Str::limit($article->title, 55) }}
                                </a>
                                @if($article->is_featured)
                                    <span title="Destacada">⚡</span>
                                @endif
                                <div style="color:var(--text-faint);font-size:.72rem;font-weight:400">/{{ $article->slug }}</div>
                            </td>
                            <td>{{ $article->category?->name ?? '—' }}</td>
                            <td>{{ $article->video_platform ?? '—' }}</td>
                            <td>
                                @if($article->trashed())
                                    <span class="badge-status badge-trashed">papelera</span>
                                @elseif($article->status === 'published')
                                    <span class="badge-status badge-published">publicada</span>
                                @else
                                    <span class="badge-status badge-draft">borrador</span>
                                @endif
                            </td>
                            <td>{{ \App\Support\NumberFormat::compact($article->views_count) }}</td>
                            <td>{{ number_format($article->trending_score, 1, ',', '.') }}</td>
                            <td>{{ $article->published_at?->format('d/m/Y H:i') ?? '—' }}</td>
                            <td>
                                <div class="actions-row">
                                    @if($article->trashed())
                                        <form method="POST" action="{{ route('admin.articles.restore', $article) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button class="btn-tiny success" type="submit">Restaurar</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.articles.force', $article) }}"
                                              onsubmit="return confirm('¿Eliminar «{{ $article->title }}» definitivamente?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn-tiny danger" type="submit">Borrar ✕</button>
                                        </form>
                                    @else
                                        <a class="btn-tiny" href="{{ route('admin.articles.edit', $article) }}">Editar</a>
                                        <a class="btn-tiny" href="{{ route('articles.show', $article) }}" target="_blank">Ver ↗</a>
                                        <form method="POST" action="{{ route('admin.articles.toggle', $article) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button class="btn-tiny" type="submit">
                                                {{ $article->status === 'published' ? 'A borrador' : 'Publicar' }}
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.articles.destroy', $article) }}"
                                              onsubmit="return confirm('¿Enviar «{{ $article->title }}» a la papelera?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn-tiny danger" type="submit">🗑</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">No hay noticias que coincidan con los filtros.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="panel-body pagination-wrap" style="margin: 0; justify-content: flex-end">
            {{ $articles->links() }}
        </div>
    </div>
@endsection
