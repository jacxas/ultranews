<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class IssueApiToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ultranews:issue-token
        {email? : Email del usuario}
        {--revoke : Revoca el token actual del usuario}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera (o revoca) un token personal para autenticar la API REST de UltraNews';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = $this->argument('email') ?? $this->ask('¿Email del usuario?');

        /** @var User|null $user */
        $user = User::where('email', $email)->first();

        if ($user === null) {
            $this->error("No existe ningún usuario con el email «{$email}».");

            return self::FAILURE;
        }

        if ($this->option('revoke')) {
            $user->forceFill(['api_token' => null])->save();
            $this->info("Token revocado para {$user->email}.");

            return self::SUCCESS;
        }

        if (! $user->isAdmin()) {
            $this->warn('Atención: el usuario no es administrador; podrá autenticarse pero las rutas de escritura exigirán permisos de admin.');
        }

        $plainToken = Str::random(48);

        // Solo se guarda el hash SHA-256; el token plano se muestra una única vez.
        $user->forceFill(['api_token' => hash('sha256', $plainToken)])->save();

        $this->info("Token generado para {$user->email}:");
        $this->newLine();
        $this->line("    <fg=green;options=bold>{$plainToken}</>");
        $this->newLine();
        $this->warn('Guardalo ahora: no se puede volver a mostrar.');
        $this->line('Uso:  curl -H "Authorization: Bearer '.$plainToken.'" '.config('app.url').'/api/articles');

        return self::SUCCESS;
    }
}
