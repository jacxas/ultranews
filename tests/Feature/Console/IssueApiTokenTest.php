<?php

namespace Tests\Feature\Console;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IssueApiTokenTest extends TestCase
{
    use RefreshDatabase;

    public function test_genera_un_token_hasheado_para_el_usuario(): void
    {
        $user = User::factory()->admin()->create(['email' => 'admin@example.com']);

        $this->artisan('ultranews:issue-token', ['email' => 'admin@example.com'])
            ->assertSuccessful();

        $user->refresh();

        $this->assertNotNull($user->api_token);
        $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $user->api_token);
    }

    public function test_revoca_el_token(): void
    {
        $user = User::factory()->admin()->create(['email' => 'admin@example.com']);

        $this->artisan('ultranews:issue-token', ['email' => 'admin@example.com'])->assertSuccessful();
        $this->assertNotNull($user->fresh()->api_token);

        $this->artisan('ultranews:issue-token', ['email' => 'admin@example.com', '--revoke' => true])
            ->assertSuccessful();

        $this->assertNull($user->fresh()->api_token);
    }

    public function test_falla_con_email_inexistente(): void
    {
        $this->artisan('ultranews:issue-token', ['email' => 'nadie@example.com'])
            ->assertFailed();
    }
}
