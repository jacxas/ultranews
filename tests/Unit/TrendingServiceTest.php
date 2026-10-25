<?php

namespace Tests\Unit;

use App\Models\Article;
use App\Services\TrendingService;
use PHPUnit\Framework\TestCase;

class TrendingServiceTest extends TestCase
{
    public function test_el_engagement_pondera_cada_metrica(): void
    {
        $service = new TrendingService;

        $article = new Article([
            'views_count' => 100,
            'likes_count' => 10,
            'shares_count' => 5,
            'comments_count' => 2,
        ]);

        // 100*1 + 10*2 + 5*3 + 2*4 = 143
        $this->assertSame(143, $service->engagementFor($article));
    }

    public function test_pesos_personalizados(): void
    {
        $service = new TrendingService(weights: ['views' => 2, 'likes' => 0, 'shares' => 0, 'comments' => 0]);

        $article = new Article([
            'views_count' => 100,
            'likes_count' => 10,
            'shares_count' => 5,
            'comments_count' => 2,
        ]);

        $this->assertSame(200, $service->engagementFor($article));
    }

    public function test_mas_engagement_produce_mas_score(): void
    {
        $service = new TrendingService;
        $moment = now()->subHours(5);

        $this->assertGreaterThan(
            $service->score(1_000, $moment),
            $service->score(10_000, $moment),
        );
    }

    public function test_el_score_decae_con_el_tiempo(): void
    {
        $service = new TrendingService;

        $reciente = $service->score(1_000, now()->subHour());
        $viejo = $service->score(1_000, now()->subDays(3));

        $this->assertGreaterThan($viejo, $reciente);
    }

    public function test_gravedad_alta_hace_decaer_mas_rapido(): void
    {
        $moment = now()->subHours(10);

        $suave = (new TrendingService(gravity: 1.0))->score(10_000, $moment);
        $fuerte = (new TrendingService(gravity: 2.5))->score(10_000, $moment);

        $this->assertGreaterThan($fuerte, $suave);
    }

    public function test_score_para_articulo_con_fecha_de_publicacion(): void
    {
        $service = new TrendingService;

        $article = new Article([
            'views_count' => 1_000,
            'likes_count' => 0,
            'shares_count' => 0,
            'comments_count' => 0,
            'published_at' => now()->subHours(2),
        ]);

        $expected = 1_000 / ((2 + 2) ** 1.6);

        $this->assertEqualsWithDelta($expected, $service->scoreFor($article), 0.01);
    }
}
