<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListingPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_pagina_de_categoria_filtra_sus_noticias(): void
    {
        $musica = Category::factory()->create(['name' => 'Música', 'slug' => 'musica']);
        $gaming = Category::factory()->create(['name' => 'Gaming', 'slug' => 'gaming']);

        Article::factory()->published()->create(['title' => 'Hit musical del momento', 'category_id' => $musica->id]);
        Article::factory()->published()->create(['title' => 'Jugada épica en la final', 'category_id' => $gaming->id]);
        Article::factory()->draft()->create(['title' => 'Borrador musical', 'category_id' => $musica->id]);

        $response = $this->get(route('categories.show', $musica));

        $response->assertOk();
        $response->assertSee('Hit musical del momento');
        $response->assertDontSee('Jugada épica en la final');
        $response->assertDontSee('Borrador musical');
    }

    public function test_pagina_de_etiqueta(): void
    {
        $tag = Tag::factory()->create(['name' => 'fail']);

        $article = Article::factory()->published()->create(['title' => 'El fail más visto del año']);
        $article->tags()->attach($tag);

        Article::factory()->published()->create(['title' => 'Noticia sin esa etiqueta']);

        $response = $this->get(route('tags.show', $tag));

        $response->assertOk();
        $response->assertSee('El fail más visto del año');
        $response->assertDontSee('Noticia sin esa etiqueta');
    }

    public function test_busqueda_por_titulo(): void
    {
        Article::factory()->published()->create(['title' => 'Perro skater se vuelve viral']);
        Article::factory()->published()->create(['title' => 'Receta de pan casero']);

        $response = $this->get(route('search', ['q' => 'skater']));

        $response->assertOk();
        $response->assertSee('Perro skater se vuelve viral');
        $response->assertDontSee('Receta de pan casero');
    }

    public function test_busqueda_sin_resultados(): void
    {
        Article::factory()->published()->create(['title' => 'Algo publicado']);

        $response = $this->get(route('search', ['q' => 'zzz-no-existe-zzz']));

        $response->assertOk();
        $response->assertSee('No hay noticias para mostrar');
    }

    public function test_listado_por_plataforma(): void
    {
        Article::factory()->published()->create([
            'title' => 'TikTok de baile imposible',
            'video_url' => 'https://www.tiktok.com/@user/video/6718335390845095173',
        ]);
        Article::factory()->published()->create([
            'title' => 'Clip de YouTube clásico',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        ]);

        $response = $this->get(route('platform', 'tiktok'));

        $response->assertOk();
        $response->assertSee('TikTok de baile imposible');
        $response->assertDontSee('Clip de YouTube clásico');
    }

    public function test_plataforma_desconocida_devuelve_404(): void
    {
        $this->get(route('platform', 'myspace'))->assertNotFound();
    }

    public function test_pagina_de_tendencias(): void
    {
        Article::factory()->published()->create([
            'title' => 'Lo más caliente de hoy',
            'views_count' => 5_000_000,
            'published_at' => now()->subHour(),
        ]);

        $response = $this->get(route('trending'));

        $response->assertOk();
        $response->assertSee('Lo más caliente de hoy');
    }
}
