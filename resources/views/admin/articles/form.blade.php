@extends('layouts.admin')

@section('title', $article->exists ? 'Editar noticia' : 'Nueva noticia')

@section('content')
    <div class="admin-page-head">
        <h1>{{ $article->exists ? 'Editar noticia' : 'Nueva noticia' }}</h1>
        <a class="btn btn-small btn-ghost" href="{{ route('admin.articles.index') }}">← Volver al listado</a>
    </div>

    @if($errors->any())
        <div class="error-list">
            <strong>Revisá los siguientes errores:</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST"
          action="{{ $article->exists ? route('admin.articles.update', $article) : route('admin.articles.store') }}">
        @csrf
        @if($article->exists)
            @method('PUT')
        @endif

        <div class="panel">
            <div class="panel-head">
                <h2 class="panel-title">📰 Contenido</h2>
            </div>
            <div class="panel-body">
                <div class="form-grid">
                    <div class="form-group span-2">
                        <label class="form-label" for="title">Título *</label>
                        <input class="form-input" id="title" name="title" type="text"
                               value="{{ old('title', $article->title) }}" required maxlength="255">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="slug">Slug</label>
                        <input class="form-input" id="slug" name="slug" type="text"
                               value="{{ old('slug', $article->slug) }}" placeholder="se-genera-desde-el-titulo">
                        <p class="form-help">Opcional: si lo dejás vacío se genera automáticamente desde el título.</p>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="category_id">Categoría</label>
                        <select class="form-select" id="category_id" name="category_id">
                            <option value="">— Sin categoría —</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}"
                                        @selected((string) old('category_id', $article->category_id) === (string) $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group span-2">
                        <label class="form-label" for="video_url">URL del video viral</label>
                        <input class="form-input" id="video_url" name="video_url" type="url"
                               value="{{ old('video_url', $article->video_url) }}"
                               placeholder="https://www.youtube.com/watch?v=... | https://www.tiktok.com/@user/video/... | https://www.instagram.com/reel/...">
                        <p class="form-help">
                            Se aceptan videos de YouTube, TikTok, Instagram, X, Vimeo y Facebook.
                            La plataforma, el ID y el embed se detectan automáticamente.
                        </p>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="source_url">URL de la fuente</label>
                        <input class="form-input" id="source_url" name="source_url" type="url"
                               value="{{ old('source_url', $article->source_url) }}"
                               placeholder="https://...">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="thumbnail_url">Miniatura personalizada</label>
                        <input class="form-input" id="thumbnail_url" name="thumbnail_url" type="url"
                               value="{{ old('thumbnail_url', $article->thumbnail_url) }}"
                               placeholder="https://...">
                        <p class="form-help">Opcional: para YouTube se usa automáticamente la miniatura del video.</p>
                    </div>

                    <div class="form-group span-2">
                        <label class="form-label" for="excerpt">Bajada / resumen</label>
                        <textarea class="form-textarea" id="excerpt" name="excerpt"
                                  maxlength="500">{{ old('excerpt', $article->excerpt) }}</textarea>
                    </div>

                    <div class="form-group span-2">
                        <label class="form-label" for="body">Cuerpo de la noticia</label>
                        <textarea class="form-textarea" id="body" name="body"
                                  style="min-height: 200px">{{ old('body', $article->body) }}</textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="tags">Etiquetas</label>
                        <input class="form-input" id="tags" name="tags" type="text"
                               value="{{ old('tags', $tagList) }}" placeholder="viral, youtube, humor">
                        <p class="form-help">Separalas con comas. Se crean automáticamente si no existen.</p>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="published_at">Fecha de publicación</label>
                        <input class="form-input" id="published_at" name="published_at" type="datetime-local"
                               value="{{ old('published_at', $article->published_at?->format('Y-m-d\TH:i')) }}">
                        <p class="form-help">Programá la publicación dejando una fecha futura.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-head">
                <h2 class="panel-title">📊 Métricas sociales y SEO</h2>
            </div>
            <div class="panel-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label" for="views_count">Vistas</label>
                        <input class="form-input" id="views_count" name="views_count" type="number" min="0"
                               value="{{ old('views_count', $article->views_count ?? 0) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="likes_count">Likes</label>
                        <input class="form-input" id="likes_count" name="likes_count" type="number" min="0"
                               value="{{ old('likes_count', $article->likes_count ?? 0) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="shares_count">Compartidos</label>
                        <input class="form-input" id="shares_count" name="shares_count" type="number" min="0"
                               value="{{ old('shares_count', $article->shares_count ?? 0) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="comments_count">Comentarios</label>
                        <input class="form-input" id="comments_count" name="comments_count" type="number" min="0"
                               value="{{ old('comments_count', $article->comments_count ?? 0) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="meta_title">Meta título (SEO)</label>
                        <input class="form-input" id="meta_title" name="meta_title" type="text" maxlength="255"
                               value="{{ old('meta_title', $article->meta_title) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="meta_description">Meta descripción (SEO)</label>
                        <input class="form-input" id="meta_description" name="meta_description" type="text" maxlength="500"
                               value="{{ old('meta_description', $article->meta_description) }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-head">
                <h2 class="panel-title">🚦 Publicación</h2>
            </div>
            <div class="panel-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label" for="status">Estado</label>
                        <select class="form-select" id="status" name="status">
                            <option value="draft" @selected(old('status', $article->status ?? 'draft') === 'draft')>Borrador</option>
                            <option value="published" @selected(old('status', $article->status ?? '') === 'published')>Publicada</option>
                        </select>
                    </div>
                    <div class="form-group" style="display:flex;align-items:flex-end;padding-bottom:.4rem">
                        <label class="form-check">
                            <input type="checkbox" name="is_featured" value="1"
                                   @checked(old('is_featured', $article->is_featured))>
                            ⚡ Marcar como destacada del día
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn">
                {{ $article->exists ? 'Guardar cambios' : 'Crear noticia' }}
            </button>
            <a class="btn btn-ghost" href="{{ route('admin.articles.index') }}">Cancelar</a>
        </div>
    </form>
@endsection
