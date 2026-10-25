<?php

namespace Tests\Feature\Api;

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MiscApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_lista_categorias_con_conteo_de_noticias(): void
    {
        $category = Category::factory()->create(['name' => 'Virales', 'slug' => 'virales']);

        Article::factory()->published()->count(2)->create(['category_id' => $category->id]);
        Article::factory()->draft()->create(['category_id' => $category->id]);

        $response = $this->getJson('/api/categories');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.slug', 'virales')
            ->assertJsonPath('data.0.articles_count', 2);
    }

    public function test_detalle_de_categoria(): void
    {
        Category::factory()->create(['name' => 'Memes', 'slug' => 'memes']);

        $this->getJson('/api/categories/memes')
            ->assertOk()
            ->assertJsonPath('data.name', 'Memes');
    }

    public function test_lista_etiquetas(): void
    {
        Tag::factory()->create(['name' => 'humor']);

        $this->getJson('/api/tags')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'humor');
    }

    public function test_trending_devuelve_las_mas_calientes(): void
    {
        $caliente = Article::factory()->published()->create([
            'title' => 'Explotando ahora',
            'views_count' => 1_000_000,
            'likes_count' => 100_000,
            'published_at' => now()->subHour(),
        ]);

        $fria = Article::factory()->published()->create([
            'title' => 'Noticia vieja y fría',
            'views_count' => 1_000,
            'likes_count' => 10,
            'published_at' => now()->subDays(20),
        ]);

        $response = $this->getJson('/api/trending?limit=2');

        $response->assertOk()->assertJsonCount(2, 'data');
        $this->assertSame($caliente->id, $response->json('data.0.id'));
        $this->assertSame($fria->id, $response->json('data.1.id'));
    }

    public function test_trending_filtra_por_plataforma(): void
    {
        Article::factory()->published()->create([
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        ]);
        Article::factory()->published()->create([
            'video_url' => 'https://www.tiktok.com/@user/video/6718335390845095173',
        ]);

        $this->getJson('/api/trending?platform=youtube')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.video.platform', 'youtube');
    }
}
