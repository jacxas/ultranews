<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_el_formulario_de_login_es_publico(): void
    {
        $this->get(route('login'))->assertOk();
    }

    public function test_un_admin_puede_iniciar_sesion(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->post(route('login.attempt'), [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_un_usuario_sin_permisos_no_puede_entrar_al_panel(): void
    {
        $user = User::factory()->create();

        $response = $this->post(route('login.attempt'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_password_incorrecta_falla(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->post(route('login.attempt'), [
            'email' => $admin->email,
            'password' => 'incorrecta',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_logout_cierra_la_sesion(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('logout'))
            ->assertRedirect(route('home'));

        $this->assertGuest();
    }
}
