<?php

namespace Tests\Feature\Admin;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_invitados_son_redirigidos_al_login(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    }

    public function test_usuarios_no_admin_reciben_403(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_el_admin_ve_el_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Dashboard');
    }

    public function test_crear_noticia_desde_el_panel(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($admin)->post(route('admin.articles.store'), [
            'title' => 'Nuevo viral de TikTok',
            'status' => 'published',
            'category_id' => $category->id,
            'video_url' => 'https://www.tiktok.com/@scout2015/video/6718335390845095173',
            'excerpt' => 'Un clásico de la plataforma.',
            'tags' => 'viral, tiktok',
        ]);

        $response->assertRedirect(route('admin.articles.index'));

        $this->assertDatabaseHas('articles', [
            'title' => 'Nuevo viral de TikTok',
            'slug' => 'nuevo-viral-de-tiktok',
            'status' => 'published',
            'video_platform' => 'tiktok',
            'video_id' => '6718335390845095173',
            'user_id' => $admin->id,
        ]);

        $this->assertDatabaseHas('tags', ['name' => 'viral']);
        $this->assertDatabaseHas('tags', ['name' => 'tiktok']);

        $article = Article::where('slug', 'nuevo-viral-de-tiktok')->firstOrFail();
        $this->assertCount(2, $article->tags);
        $this->assertNotNull($article->published_at);
    }

    public function test_editar_noticia(): void
    {
        $admin = User::factory()->admin()->create();
        $article = Article::factory()->draft()->create(['title' => 'Título viejo']);

        $response = $this->actingAs($admin)->put(route('admin.articles.update', $article), [
            'title' => 'Título nuevo y mejorado',
            'status' => 'published',
        ]);

        $response->assertRedirect(route('admin.articles.index'));

        $article->refresh();
        $this->assertSame('Título nuevo y mejorado', $article->title);
        $this->assertSame(Article::STATUS_PUBLISHED, $article->status);
    }

    public function test_toggle_cambia_el_estado(): void
    {
        $admin = User::factory()->admin()->create();
        $article = Article::factory()->draft()->create();

        $this->actingAs($admin)->patch(route('admin.articles.toggle', $article));
        $article->refresh();
        $this->assertSame(Article::STATUS_PUBLISHED, $article->status);
        $this->assertNotNull($article->published_at);

        $this->actingAs($admin)->patch(route('admin.articles.toggle', $article));
        $article->refresh();
        $this->assertSame(Article::STATUS_DRAFT, $article->status);
    }

    public function test_eliminar_envia_a_papelera_y_se_puede_restaurar(): void
    {
        $admin = User::factory()->admin()->create();
        $article = Article::factory()->published()->create();

        $this->actingAs($admin)->delete(route('admin.articles.destroy', $article));
        $this->assertSoftDeleted('articles', ['id' => $article->id]);

        $this->actingAs($admin)->patch(route('admin.articles.restore', $article));
        $this->assertDatabaseHas('articles', ['id' => $article->id, 'deleted_at' => null]);

        $this->actingAs($admin)->delete(route('admin.articles.force', $article));
        $this->assertDatabaseMissing('articles', ['id' => $article->id]);
    }

    public function test_crud_de_categorias(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('admin.categories.store'), [
            'name' => 'Esports',
            'description' => 'Competitivas y torneos.',
            'color' => '#22d3ee',
            'sort_order' => 3,
        ])->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseHas('categories', ['name' => 'Esports', 'slug' => 'esports']);

        $category = Category::where('slug', 'esports')->firstOrFail();

        $this->actingAs($admin)->put(route('admin.categories.update', $category), [
            'name' => 'E-Sports Pro',
        ])->assertRedirect(route('admin.categories.index'));

        $this->assertSame('E-Sports Pro', $category->fresh()->name);

        $this->actingAs($admin)->delete(route('admin.categories.destroy', $category));
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_el_listado_filtra_por_estado(): void
    {
        $admin = User::factory()->admin()->create();
        Article::factory()->published()->create(['title' => 'Publicada visible']);
        Article::factory()->draft()->create(['title' => 'Borrador oculto']);

        $response = $this->actingAs($admin)->get(route('admin.articles.index', ['status' => 'published']));

        $response->assertOk();
        $response->assertSee('Publicada visible');
        $response->assertDontSee('Borrador oculto');
    }
}
