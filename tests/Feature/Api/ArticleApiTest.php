<?php

namespace Tests\Feature\Api;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ArticleApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_lista_noticias_publicadas_paginadas(): void
    {
        Article::factory()->published()->count(3)->create();
        Article::factory()->draft()->create();

        $response = $this->getJson('/api/articles');

        $response->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'slug', 'url', 'status', 'video', 'stats', 'published_at'],
                ],
                'links',
                'meta',
            ]);
    }

    public function test_filtra_por_categoria_plataforma_y_orden(): void
    {
        $musica = Category::factory()->create(['slug' => 'musica']);

        $youtube = Article::factory()->published()->create([
            'title' => 'Clip de YouTube',
            'category_id' => $musica->id,
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'views_count' => 500,
        ]);

        Article::factory()->published()->create([
            'title' => 'Clip de TikTok',
            'video_url' => 'https://www.tiktok.com/@user/video/6718335390845095173',
            'views_count' => 9_000,
        ]);

        $this->getJson('/api/articles?category=musica')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $youtube->id);

        $this->getJson('/api/articles?platform=tiktok')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.video.platform', 'tiktok');

        $this->getJson('/api/articles?sort=views')
            ->assertOk()
            ->assertJsonPath('data.0.title', 'Clip de TikTok');
    }

    public function test_detalle_de_una_noticia(): void
    {
        $article = Article::factory()->published()->create([
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        ]);

        $response = $this->getJson('/api/articles/'.$article->slug);

        $response->assertOk()
            ->assertJsonPath('data.video.platform', 'youtube')
            ->assertJsonPath('data.video.id', 'dQw4w9WgXcQ')
            ->assertJsonPath('data.video.embed_url', 'https://www.youtube.com/embed/dQw4w9WgXcQ');
    }

    public function test_detalle_de_borrador_devuelve_404(): void
    {
        $article = Article::factory()->draft()->create();

        $this->getJson('/api/articles/'.$article->slug)->assertNotFound();
    }

    public function test_escribir_sin_token_devuelve_401(): void
    {
        $this->postJson('/api/articles', ['title' => 'Sin token'])->assertUnauthorized();
    }

    public function test_token_invalido_devuelve_401(): void
    {
        $this->postJson('/api/articles', ['title' => 'Token trucho'], [
            'Authorization' => 'Bearer token-que-no-existe',
        ])->assertUnauthorized();
    }

    public function test_usuario_no_admin_devuelve_403(): void
    {
        $user = User::factory()->create();
        $token = $this->issueToken($user);

        $this->postJson('/api/articles', ['title' => 'Intento no admin'], [
            'Authorization' => 'Bearer '.$token,
        ])->assertForbidden();
    }

    public function test_admin_puede_crear_actualizar_y_eliminar(): void
    {
        $admin = User::factory()->admin()->create();
        $token = $this->issueToken($admin);
        $headers = ['Authorization' => 'Bearer '.$token];

        // Crear
        $storeResponse = $this->postJson('/api/articles', [
            'title' => 'Noticia creada por API',
            'excerpt' => 'Bajada de prueba.',
            'status' => 'published',
            'video_url' => 'https://youtu.be/9bZkp7q19f0',
            'tags' => 'api, viral',
        ], $headers);

        $storeResponse->assertCreated()
            ->assertJsonPath('data.title', 'Noticia creada por API')
            ->assertJsonPath('data.video.platform', 'youtube')
            ->assertJsonPath('data.video.id', '9bZkp7q19f0')
            ->assertJsonPath('data.status', 'published');

        $slug = $storeResponse->json('data.slug');
        $this->assertSame('noticia-creada-por-api', $slug);
        $this->assertDatabaseHas('tags', ['name' => 'api']);

        // Actualizar
        $this->putJson('/api/articles/'.$slug, [
            'title' => 'Título editado por API',
        ], $headers)
            ->assertOk()
            ->assertJsonPath('data.title', 'Título editado por API');

        // Eliminar
        $this->deleteJson('/api/articles/'.$slug, [], $headers)->assertNoContent();
        $this->assertSoftDeleted('articles', ['slug' => 'noticia-creada-por-api']);
    }

    public function test_validacion_devuelve_422(): void
    {
        $admin = User::factory()->admin()->create();
        $token = $this->issueToken($admin);

        $this->postJson('/api/articles', ['title' => ''], [
            'Authorization' => 'Bearer '.$token,
        ])->assertStatus(422)->assertJsonValidationErrors(['title']);
    }

    public function test_endpoint_de_vista_incrementa_el_contador(): void
    {
        $article = Article::factory()->published()->create(['views_count' => 10]);

        $response = $this->postJson('/api/articles/'.$article->slug.'/view');

        $response->assertOk()->assertJsonPath('views_count', 11);
        $this->assertSame(11, $article->fresh()->views_count);
    }

    /**
     * Genera un token personal y devuelve el valor en texto plano.
     */
    private function issueToken(User $user): string
    {
        $plain = Str::random(48);

        $user->forceFill(['api_token' => hash('sha256', $plain)])->save();

        return $plain;
    }
}
