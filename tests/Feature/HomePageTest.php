<?php

namespace Tests\Feature;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_portada_muestra_las_noticias_publicadas(): void
    {
        Article::factory()->published()->create(['title' => 'Gato surfista la rompe en las redes']);
        Article::factory()->draft()->create(['title' => 'Borrador secreto no visible']);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('Gato surfista la rompe en las redes');
        $response->assertDontSee('Borrador secreto no visible');
    }

    public function test_la_portada_incluye_seccion_de_tendencias(): void
    {
        Article::factory()->published()->count(3)->create();

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('Tendencias ahora');
    }

    public function test_la_portada_sin_noticias_muestra_estado_vacio(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('Todavía no hay noticias virales');
    }

    public function test_no_se_publican_noticias_con_fecha_futura(): void
    {
        Article::factory()->create([
            'title' => 'Noticia programada para mañana',
            'status' => Article::STATUS_PUBLISHED,
            'published_at' => now()->addDay(),
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertDontSee('Noticia programada para mañana');
    }
}
