<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticlePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_muestra_la_noticia_con_el_video_embebido(): void
    {
        $article = Article::factory()->published()->create([
            'title' => 'El video del perrito bailando',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        ]);

        $response = $this->get(route('articles.show', $article));

        $response->assertOk();
        $response->assertSee('El video del perrito bailando');
        $response->assertSee('https://www.youtube.com/embed/dQw4w9WgXcQ', false);
    }

    public function test_un_borrador_devuelve_404_para_invitados(): void
    {
        $article = Article::factory()->draft()->create();

        $this->get(route('articles.show', $article))->assertNotFound();
    }

    public function test_un_admin_puede_previsualizar_un_borrador(): void
    {
        $admin = User::factory()->admin()->create();
        $article = Article::factory()->draft()->create(['title' => 'Borrador en revisión']);

        $response = $this->actingAs($admin)->get(route('articles.show', $article));

        $response->assertOk();
        $response->assertSee('Borrador en revisión');
        $response->assertSee('Vista previa de administrador');
    }

    public function test_incrementa_las_vistas_una_sola_vez_por_sesion(): void
    {
        $article = Article::factory()->published()->create(['views_count' => 100]);

        $this->get(route('articles.show', $article))->assertOk();
        $this->assertSame(101, $article->fresh()->views_count);

        $this->get(route('articles.show', $article))->assertOk();
        $this->assertSame(101, $article->fresh()->views_count);
    }

    public function test_noticia_inexistente_devuelve_404(): void
    {
        $this->get('/noticia/no-existe-esta-noticia')->assertNotFound();
    }
}
