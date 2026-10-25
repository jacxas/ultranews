<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    /**
     * Etiquetas más usadas del portal.
     */
    public function run(): void
    {
        $tags = [
            'viral', 'tendencia', 'youtube', 'tiktok', 'shorts', 'reels',
            'humor', 'fail', 'música', 'gaming', 'challenge', 'clásico',
            'animales', 'tecnología', 'récord',
        ];

        foreach ($tags as $tag) {
            Tag::firstOrCreate(
                ['slug' => Str::slug($tag)],
                ['name' => $tag],
            );
        }
    }
}
