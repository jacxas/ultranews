<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Ojo: NO usar WithoutModelEvents — el modelo Article depende del evento
     * `saving` para generar el slug y el trending score.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@ultranews.test'],
            [
                'name' => 'Admin UltraNews',
                'password' => 'password',
                'is_admin' => true,
                'email_verified_at' => now(),
            ],
        );

        if (! $admin->isAdmin()) {
            $admin->forceFill(['is_admin' => true])->save();
        }

        $this->call([
            CategorySeeder::class,
            TagSeeder::class,
            ArticleSeeder::class,
        ]);

        $this->command?->info('--- UltraNews seed completo ---');
        $this->command?->info('Panel: http://localhost:8000/login');
        $this->command?->info('Usuario demo: admin@ultranews.test / password');
        $this->command?->info('Token API:    php artisan ultranews:issue-token admin@ultranews.test');
    }
}
