<?php

namespace Tests\Feature\Console;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FetchViralVideosTest extends TestCase
{
    use RefreshDatabase;

    public function test_importa_los_videos_mas_populares_de_youtube(): void
    {
        config()->set('services.youtube.key', 'clave-falsa');

        Http::fake([
            '*googleapis.com*' => Http::response($this->youtubePayload(), 200),
        ]);

        $this->artisan('ultranews:fetch-viral', ['--region' => 'AR', '--limit' => 2])
            ->assertSuccessful();

        $this->assertDatabaseHas('articles', [
            'video_id' => 'AAA111bbb22',
            'video_platform' => 'youtube',
            'views_count' => 1_000_000,
            'likes_count' => 50_000,
            'status' => Article::STATUS_PUBLISHED,
        ]);

        $this->assertDatabaseHas('articles', ['video_id' => 'CCC333ddd44']);
        $this->assertDatabaseHas('categories', ['slug' => 'virales']);

        $imported = Article::where('video_id', 'AAA111bbb22')->firstOrFail();
        $this->assertSame('https://www.youtube.com/embed/AAA111bbb22', $imported->embed()['embed_url']);
        $this->assertGreaterThan(0, $imported->trending_score);
    }

    public function test_ejecutar_dos_veces_actualiza_sin_duplicar(): void
    {
        config()->set('services.youtube.key', 'clave-falsa');

        Http::fake([
            '*googleapis.com*' => Http::response($this->youtubePayload(), 200),
        ]);

        $this->artisan('ultranews:fetch-viral')->assertSuccessful();
        $this->artisan('ultranews:fetch-viral')->assertSuccessful();

        $this->assertSame(2, Article::count());
    }

    public function test_dry_run_no_guarda_nada(): void
    {
        config()->set('services.youtube.key', 'clave-falsa');

        Http::fake([
            '*googleapis.com*' => Http::response($this->youtubePayload(), 200),
        ]);

        $this->artisan('ultranews:fetch-viral', ['--dry-run' => true])
            ->assertSuccessful();

        $this->assertSame(0, Article::count());
    }

    public function test_falla_sin_clave_de_api(): void
    {
        config()->set('services.youtube.key', '');

        $this->artisan('ultranews:fetch-viral')->assertFailed();
    }

    public function test_falla_con_plataforma_no_soportada(): void
    {
        $this->artisan('ultranews:fetch-viral', ['--platform' => 'tiktok'])
            ->assertFailed();
    }

    public function test_falla_si_la_api_devuelve_error(): void
    {
        config()->set('services.youtube.key', 'clave-falsa');

        Http::fake([
            '*googleapis.com*' => Http::response(['error' => ['message' => 'Cuota excedida']], 403),
        ]);

        $this->artisan('ultranews:fetch-viral')->assertFailed();
    }

    /**
     * @return array<string, mixed>
     */
    private function youtubePayload(): array
    {
        return [
            'items' => [
                [
                    'id' => 'AAA111bbb22',
                    'snippet' => [
                        'title' => 'El video más viral de la semana',
                        'description' => 'Nadie puede dejar de verlo.',
                        'publishedAt' => '2026-10-20T12:00:00Z',
                        'channelId' => 'UC123456',
                        'thumbnails' => [
                            'high' => ['url' => 'https://i.ytimg.com/vi/AAA111bbb22/hqdefault.jpg'],
                        ],
                    ],
                    'statistics' => [
                        'viewCount' => '1000000',
                        'likeCount' => '50000',
                        'commentCount' => '3000',
                    ],
                ],
                [
                    'id' => 'CCC333ddd44',
                    'snippet' => [
                        'title' => 'Otro video en tendencia',
                        'description' => 'Tendencia en ascenso.',
                        'publishedAt' => '2026-10-22T09:30:00Z',
                        'channelId' => 'UC789012',
                        'thumbnails' => [
                            'high' => ['url' => 'https://i.ytimg.com/vi/CCC333ddd44/hqdefault.jpg'],
                        ],
                    ],
                    'statistics' => [
                        'viewCount' => '250000',
                        'likeCount' => '12000',
                        'commentCount' => '800',
                    ],
                ],
            ],
        ];
    }
}
