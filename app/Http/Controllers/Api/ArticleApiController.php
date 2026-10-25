<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ArticleRequest;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class ArticleApiController extends Controller
{
    /**
     * GET /api/articles — listar noticias publicadas con filtros.
     *
     * Filtros: category (slug), tag (slug), platform, q, featured.
     * Orden: sort=trending|recent|views. Paginación: page, per_page (máx. 50).
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = min(50, max(1, (int) $request->query('per_page', 12)));
        $sort = (string) $request->query('sort', 'trending');

        $articles = Article::query()
            ->published()
            ->with(['category', 'tags'])
            ->when(
                $request->filled('category'),
                fn ($q) => $q->whereHas('category', fn ($c) => $c->where('slug', (string) $request->query('category')))
            )
            ->when(
                $request->filled('tag'),
                fn ($q) => $q->whereHas('tags', fn ($t) => $t->where('slug', (string) $request->query('tag')))
            )
            ->when($request->filled('platform'), fn ($q) => $q->byPlatform((string) $request->query('platform')))
            ->when($request->boolean('featured'), fn ($q) => $q->featured())
            ->search($request->query('q'))
            ->when(
                match ($sort) {
                    'recent' => 'recent',
                    'views' => 'mostViewed',
                    default => 'trending',
                },
                fn ($q, $scope) => $q->{$scope}()
            )
            ->paginate($perPage)
            ->withQueryString();

        return ArticleResource::collection($articles);
    }

    /**
     * GET /api/articles/{slug} — detalle de una noticia.
     */
    public function show(Request $request, Article $article): ArticleResource
    {
        abort_unless($article->isPublished() || ($request->user()?->isAdmin() ?? false), 404);

        return new ArticleResource($article->loadMissing(['category', 'tags', 'author']));
    }

    /**
     * POST /api/articles — crear noticia (token de API + admin).
     */
    public function store(ArticleRequest $request): JsonResponse
    {
        $article = Article::create($request->articleAttributes() + [
            'user_id' => $request->user()->id,
        ]);

        $article->syncTagsFromList($request->input('tags'));
        $article->loadMissing(['category', 'tags']);

        return (new ArticleResource($article))
            ->response()
            ->setStatusCode(201)
            ->header('Location', route('api.articles.show', $article));
    }

    /**
     * PUT|PATCH /api/articles/{slug} — actualizar noticia (token de API + admin).
     */
    public function update(ArticleRequest $request, Article $article): ArticleResource
    {
        $article->fill($request->articleAttributes());
        $article->save();
        $article->syncTagsFromList($request->input('tags'));
        $article->loadMissing(['category', 'tags']);

        return new ArticleResource($article);
    }

    /**
     * DELETE /api/articles/{slug} — eliminar noticia (soft delete).
     */
    public function destroy(Article $article): Response
    {
        $article->delete();

        return response('', 204);
    }

    /**
     * POST /api/articles/{slug}/view — registrar una reproducción/visita.
     */
    public function view(Article $article): JsonResponse
    {
        abort_unless($article->isPublished(), 404);

        $article->increment('views_count');

        return new JsonResponse([
            'slug' => $article->slug,
            'views_count' => $article->fresh()?->views_count ?? $article->views_count,
        ]);
    }
}
