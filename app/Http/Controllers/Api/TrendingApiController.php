<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;

class TrendingApiController extends Controller
{
    /**
     * GET /api/trending — top de noticias virales del momento.
     *
     * Parámetros: limit (1-50, default 10), platform (opcional).
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $limit = min(50, max(1, (int) $request->query('limit', 10)));
        $platform = Str::lower((string) $request->query('platform', ''));

        $articles = Article::query()
            ->published()
            ->with(['category', 'tags'])
            ->when($platform !== '', fn ($q) => $q->byPlatform($platform))
            ->trending()
            ->limit($limit)
            ->get();

        return ArticleResource::collection($articles);
    }
}
