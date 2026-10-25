<?php

namespace App\Services;

use App\Models\Article;
use Illuminate\Support\Carbon;

/**
 * Calcula el "trending score" de cada noticia, al estilo Hacker News:
 *
 *     score = engagement / (edad_en_horas + 2) ^ gravedad
 *
 * donde engagement = views*w1 + likes*w2 + shares*w3 + comments*w4.
 * El score decae con el tiempo, por eso conviene recalcularlo
 * periódicamente (`php artisan ultranews:refresh-trending`).
 */
class TrendingService
{
    /**
     * @param  array<string, int|float>  $weights
     */
    public function __construct(
        protected float $gravity = 1.6,
        protected array $weights = [],
    ) {
        $this->weights = array_merge([
            'views' => 1,
            'likes' => 2,
            'shares' => 3,
            'comments' => 4,
        ], $weights);
    }

    /**
     * Engagement ponderado de un artículo.
     */
    public function engagementFor(Article $article): int
    {
        return (int) round(
            ($article->views_count * $this->weights['views'])
            + ($article->likes_count * $this->weights['likes'])
            + ($article->shares_count * $this->weights['shares'])
            + ($article->comments_count * $this->weights['comments'])
        );
    }

    /**
     * Score de tendencias para un artículo (usa published_at o created_at).
     */
    public function scoreFor(Article $article): float
    {
        return $this->score(
            engagement: $this->engagementFor($article),
            publishedAt: $article->published_at ?? $article->created_at ?? now(),
        );
    }

    /**
     * Score genérico a partir de engagement y fecha de publicación.
     */
    public function score(int $engagement, ?Carbon $publishedAt = null): float
    {
        $publishedAt ??= now();

        $ageHours = (float) $publishedAt->diffInHours(now());
        $ageHours = max(0.0, $ageHours);

        return round($engagement / (($ageHours + 2) ** $this->gravity), 4);
    }

    /**
     * Recalcula y persiste el score de un artículo sin disparar eventos.
     */
    public function refreshArticle(Article $article): void
    {
        $article->forceFill(['trending_score' => $this->scoreFor($article)])->saveQuietly();
    }

    /**
     * Recalcula el score de todos los artículos (incluidos borradores/eliminados).
     *
     * @return int cantidad de artículos actualizados
     */
    public function refreshAll(): int
    {
        $count = 0;

        Article::withTrashed()->chunkById(200, function ($articles) use (&$count): void {
            foreach ($articles as $article) {
                $this->refreshArticle($article);
                $count++;
            }
        });

        return $count;
    }
}
