<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Categorías del portal de noticias virales.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Virales', 'slug' => 'virales', 'description' => 'Los videos que están rompiendo internet en este momento.', 'color' => '#e11d48', 'sort_order' => 1],
            ['name' => 'Música', 'slug' => 'musica', 'description' => 'Videoclips, shows y hits musicales que son tendencia mundial.', 'color' => '#a855f7', 'sort_order' => 2],
            ['name' => 'Memes', 'slug' => 'memes', 'description' => 'La cultura meme en video: clásicos eternos y nuevas joyas.', 'color' => '#f97316', 'sort_order' => 3],
            ['name' => 'Gaming', 'slug' => 'gaming', 'description' => 'Gameplays, e-sports y los momentos más épicos del gaming.', 'color' => '#22d3ee', 'sort_order' => 4],
            ['name' => 'Deportes', 'slug' => 'deportes', 'description' => 'Jugadas imposibles, knockouts y festejos que dieron la vuelta al mundo.', 'color' => '#84cc16', 'sort_order' => 5],
            ['name' => 'Insólito', 'slug' => 'insolito', 'description' => 'Lo más extraño, increíble e inesperado captado en video.', 'color' => '#eab308', 'sort_order' => 6],
            ['name' => 'Tecnología', 'slug' => 'tecnologia', 'description' => 'Lanzamientos, demos, fails y hitos de la cultura digital.', 'color' => '#6366f1', 'sort_order' => 7],
            ['name' => 'Animales', 'slug' => 'animales', 'description' => 'Mascotas y animales salvajes que se roban todos los likes.', 'color' => '#10b981', 'sort_order' => 8],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                $category,
            );
        }
    }
}
