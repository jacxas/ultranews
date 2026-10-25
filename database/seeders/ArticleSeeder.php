<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    /**
     * Noticias demo basadas en videos virales reales de todas las épocas.
     * Idempotente: usa video_url como clave de deduplicación.
     */
    public function run(): void
    {
        $author = User::where('email', 'admin@ultranews.test')->first();

        $articles = [
            [
                'title' => 'Baby Shark rompe todos los récords: el video más visto en la historia de YouTube',
                'category' => 'musica',
                'tags' => 'youtube, viral, música, récord',
                'video' => 'https://www.youtube.com/watch?v=XqZsoesa55w',
                'views' => 15_400_000_000, 'likes' => 33_000_000, 'shares' => 9_100_000, 'comments' => 12_500_000,
                'published' => now()->subDays(9)->subHours(3),
                'featured' => true,
                'excerpt' => 'El fenómeno infantil surcoreano superó los 15 mil millones de reproducciones y se consolidó como el video número uno de todos los tiempos.',
                'body' => "Lo que empezó como una canción infantil de Pinkfong se convirtió en el mayor fenómeno de la historia de YouTube. Baby Shark Dance superó los 15 mil millones de vistas, una cifra mayor que la población del planeta.\n\nEl video destronó a Despacito en 2020 y desde entonces no dejó de sumar reproducciones, con desafíos de baile en TikTok y versiones en decenas de idiomas.",
            ],
            [
                'title' => 'Despacito: el hit latino que sigue sumando miles de millones de vistas',
                'category' => 'musica',
                'tags' => 'youtube, viral, música',
                'video' => 'https://www.youtube.com/watch?v=kJQP7kiw5Fk',
                'views' => 8_700_000_000, 'likes' => 55_000_000, 'shares' => 12_000_000, 'comments' => 8_900_000,
                'published' => now()->subDays(7)->subHours(5),
                'featured' => true,
                'excerpt' => 'Luis Fonsi y Daddy Yankee siguen haciendo historia: el videoclip filmado en La Perla, Puerto Rico, no para de crecer.',
                'body' => "Despacito fue el video que le mostró al mundo la potencia del contenido latino en internet. Con más de 8 mil millones de reproducciones, sigue siendo uno de los clips más vistos de la plataforma.\n\nSu coreografía y el remix con Justin Bieber lo volvieron un fenómeno global que abrió la puerta a toda una generación de artistas urbanos.",
            ],
            [
                'title' => 'Gangnam Style, el baile que hizo estallar el contador de YouTube',
                'category' => 'musica',
                'tags' => 'youtube, viral, música, clásico',
                'video' => 'https://www.youtube.com/watch?v=9bZkp7q19f0',
                'views' => 5_300_000_000, 'likes' => 28_000_000, 'shares' => 10_500_000, 'comments' => 6_200_000,
                'published' => now()->subDays(6)->subHours(8),
                'excerpt' => 'PSY y su baile del caballo rompieron el contador de vistas de YouTube en 2014 y marcaron el inicio de la era viral global.',
                'body' => "Cuando Gangnam Style superó los 2.147 millones de vistas, YouTube tuvo que actualizar su contador: el video de PSY había roto el límite de enteros de 32 bits.\n\nFue el primer video en llegar a mil millones de reproducciones y el que convenció a la industria de que los virales podían mover más que la televisión.",
            ],
            [
                'title' => 'See You Again: el homenaje a Paul Walker que hizo llorar a internet',
                'category' => 'musica',
                'tags' => 'youtube, viral, música',
                'video' => 'https://www.youtube.com/watch?v=RgKAFK5djSk',
                'views' => 6_600_000_000, 'likes' => 44_000_000, 'shares' => 11_200_000, 'comments' => 5_100_000,
                'published' => now()->subDays(5)->subHours(2),
                'excerpt' => 'Wiz Khalifa y Charlie Puth crearon el tema más emotivo de la saga Rápidos y Furiosos, con más de 6 mil millones de vistas.',
                'body' => "La canción de cierre de Furious 7 se convirtió en un himno mundial de despedida. Durante años fue el video más visto de YouTube.\n\nSu estribillo sigue usándose en millones de videos de homenaje en TikTok y Reels.",
            ],
            [
                'title' => 'Shape of You: Ed Sheeran y el clip que dominó los algoritmos',
                'category' => 'musica',
                'tags' => 'youtube, viral, música',
                'video' => 'https://www.youtube.com/watch?v=JGwWNGJdvx8',
                'views' => 6_300_000_000, 'likes' => 31_000_000, 'shares' => 7_800_000, 'comments' => 3_400_000,
                'published' => now()->subDays(8)->subHours(6),
                'excerpt' => 'El video de la pelea de boxeo más famosa del pop acumula más de 6 mil millones de reproducciones.',
                'body' => "Con una narrativa simple y un gancho imposible de olvidar, Shape of You se volvió omnipresente en 2017.\n\nHoy sigue siendo una de las canciones más usadas en challenges de baile de todo el mundo.",
            ],
            [
                'title' => 'Uptown Funk: la fiesta de Bruno Mars que no envejece',
                'category' => 'musica',
                'tags' => 'youtube, viral, música, clásico',
                'video' => 'https://www.youtube.com/watch?v=OPf0YbXqDm0',
                'views' => 5_500_000_000, 'likes' => 24_000_000, 'shares' => 8_300_000, 'comments' => 2_100_000,
                'published' => now()->subDays(10)->subHours(4),
                'excerpt' => 'Mark Ronson y Bruno Mars firmaron uno de los videoclips más bailados de la década.',
                'body' => "Uptown Funk mezcló funk retro con producción moderna y se volvió ineludible en fiestas, casamientos y playlists.\n\nEl videoclip, con su estética callejera y coreografías impecables, superó los 5 mil millones de vistas.",
            ],
            [
                'title' => 'Never Gonna Give You Up: el rickroll sigue vivo y nadie lo detiene',
                'category' => 'memes',
                'tags' => 'youtube, memes, clásico, humor',
                'video' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'views' => 1_800_000_000, 'likes' => 18_500_000, 'shares' => 6_700_000, 'comments' => 2_300_000,
                'published' => now()->subDays(2)->subHours(1),
                'excerpt' => 'El meme más longevo de internet convirtió a Rick Astley en leyenda: cada link sospechoso puede llevarte a su baile.',
                'body' => "Desde 2007, el rickroll engaña a millones de usuarios con enlaces disfrazados. Lejos de cansar, el meme se renueva cada año.\n\nRick Astley lo abrazó con humor y hoy el video acumula casi 2 mil millones de reproducciones.",
            ],
            [
                'title' => 'Charlie Bit My Finger: el video casero que definió una era de YouTube',
                'category' => 'virales',
                'tags' => 'youtube, viral, clásico, humor',
                'video' => 'https://www.youtube.com/watch?v=_OBlgSz8sSM',
                'views' => 895_000_000, 'likes' => 4_100_000, 'shares' => 2_200_000, 'comments' => 900_000,
                'published' => now()->subDays(4)->subHours(9),
                'excerpt' => 'Dos hermanos británicos y un dedo mordido: el video familiar más famoso de la historia de internet.',
                'body' => "Grabado en 2007 con una cámara casera, Charlie Bit My Finger fue durante años el video más visto de YouTube.\n\nEn 2021 fue vendido como NFT por más de 760 mil dólares, y sus protagonistas siguen dando entrevistas sobre el clip que les cambió la vida.",
            ],
            [
                'title' => 'Harlem Shake: cuando todo internet bailó el mismo meme durante un mes',
                'category' => 'memes',
                'tags' => 'youtube, memes, humor, tendencia',
                'video' => 'https://www.youtube.com/watch?v=8vJiSSAMNWw',
                'views' => 190_000_000, 'likes' => 2_600_000, 'shares' => 3_900_000, 'comments' => 420_000,
                'published' => now()->subDays(3)->subHours(7),
                'excerpt' => 'El tema de Baauer desató miles de imitaciones y obligó a Billboard a cambiar sus reglas de conteo.',
                'body' => "En 2013, oficinas, equipos de deporte y escuelas enteras grabaron su versión del Harlem Shake.\n\nEl fenómeno fue tan masivo que Billboard empezó a contar las vistas de YouTube para su ranking Hot 100, y la canción debutó en el puesto número uno.",
            ],
            [
                'title' => 'Dramatic Chipmunk: la ardilla más expresiva de internet',
                'category' => 'memes',
                'tags' => 'memes, clásico, humor, youtube',
                'video' => 'https://www.youtube.com/watch?v=HIFyVLDzKOw',
                'views' => 46_000_000, 'likes' => 380_000, 'shares' => 1_500_000, 'comments' => 95_000,
                'published' => now()->subDays(12)->subHours(2),
                'excerpt' => 'Cinco segundos de suspenso y zoom que se volvieron uno de los memes fundacionales de YouTube.',
                'body' => "Aunque todos le dicen ardilla, el protagonista es en realidad un perrito de la pradera.\n\nEl clip de 2007 sigue usándose como reacción dramática en comentarios, streams y edits de todo tipo.",
            ],
            [
                'title' => 'Keyboard Cat: el gato tecladista que nunca pierde vigencia',
                'category' => 'animales',
                'tags' => 'animales, memes, clásico, youtube',
                'video' => 'https://www.youtube.com/watch?v=J---aiyznGQ',
                'views' => 75_000_000, 'likes' => 640_000, 'shares' => 2_800_000, 'comments' => 130_000,
                'published' => now()->subDays(11)->subHours(5),
                'excerpt' => 'Fatso, el gato que "tocaba" el teclado desde 1984, se transformó en el remix obligatorio de todos los fails.',
                'body' => "El video original fue grabado por Charlie Schmidt hace décadas, pero YouTube lo convirtió en leyenda.\n\nHoy Keyboard Cat tiene su propia cuenta en TikTok y merchandising oficial.",
            ],
            [
                'title' => 'Double Rainbow: el video que convirtió un fenómeno meteorológico en meme',
                'category' => 'insolito',
                'tags' => 'youtube, viral, clásico, humor',
                'video' => 'https://www.youtube.com/watch?v=OQSNhk5ICTI',
                'views' => 50_000_000, 'likes' => 470_000, 'shares' => 1_900_000, 'comments' => 180_000,
                'published' => now()->subDays(14)->subHours(3),
                'excerpt' => '"¿Qué significa esto?": la emoción genuina de Paul Vasquez ante un doble arcoíris marcó a toda una generación.',
                'body' => "Subido en 2010, el video explotó después de que Jimmy Kimmel lo compartiera en vivo.\n\nLa frase 'double rainbow all the way' se volvió parte del vocabulario de internet y Vasquez fue homenajeado incluso tras su muerte en 2020.",
            ],
            [
                'title' => 'Evolution of Dance: el primer video en llegar a los 100 millones',
                'category' => 'virales',
                'tags' => 'youtube, viral, clásico',
                'video' => 'https://www.youtube.com/watch?v=dMH0bHeiRNg',
                'views' => 325_000_000, 'likes' => 1_700_000, 'shares' => 1_100_000, 'comments' => 260_000,
                'published' => now()->subDays(15)->subHours(6),
                'excerpt' => 'Judson Laipply bailando 30 clásicos en 6 minutos fue el primer gran fenómeno de YouTube, allá por 2006.',
                'body' => "Antes de que existieran los 'influencers', Evolution of Dance demostró que un video casero podía llenar estadios.\n\nLaipply sigue presentando el show en vivo y el clip original continúa sumando vistas casi 20 años después.",
            ],
            [
                'title' => 'El primer TikTok viral de la historia cumplió años: la patineta que lo inició todo',
                'category' => 'virales',
                'tags' => 'tiktok, viral, clásico',
                'video' => 'https://www.tiktok.com/@scout2015/video/6718335390845095173',
                'views' => 4_500_000, 'likes' => 890_000, 'shares' => 310_000, 'comments' => 45_000,
                'published' => now()->subDays(1)->subHours(4),
                'excerpt' => 'El clip del chico en patineta con gorra de los Blackhawks, subido en 2019, es considerado el primer video viral de la plataforma.',
                'body' => "Cuando Musical.ly se fusionó con TikTok, este clip de apenas unos segundos se propagó por toda la plataforma.\n\nHoy es una pieza de museo digital: miles de duetos y homenajes lo mantienen vivo.",
            ],
            [
                'title' => 'La selfie de los Oscars: el tuit más retuiteado durante años',
                'category' => 'virales',
                'tags' => 'x, viral, clásico',
                'video' => 'https://twitter.com/TheEllenShow/status/440322224407314432',
                'views' => 3_200_000, 'likes' => 2_100_000, 'shares' => 3_400_000, 'comments' => 96_000,
                'published' => now()->subDays(2)->subHours(9),
                'excerpt' => 'Bradley Cooper, Jennifer Lawrence, Meryl Streep y más estrellas en una sola foto: el momento más viral de la historia de X.',
                'body' => "La selfie de Ellen DeGeneres en los Oscars 2014 rompió récords de retuits y hasta tumbó servidores.\n\nSe convirtió en el símbolo de lo que significa 'volverse viral' en redes sociales.",
            ],
            [
                'title' => '"Just setting up my twttr": el mensaje que empezó todo',
                'category' => 'tecnologia',
                'tags' => 'x, tecnología, clásico',
                'video' => 'https://x.com/jack/status/20',
                'views' => 900_000, 'likes' => 220_000, 'shares' => 180_000, 'comments' => 32_000,
                'published' => now()->subDays(16)->subHours(1),
                'excerpt' => 'El tuit número 20 de la historia, escrito por Jack Dorsey en 2006, sigue acumulando likes casi dos décadas después.',
                'body' => "Cinco palabras lanzaron la era de las redes sociales en tiempo real.\n\nEl post fue subastado como NFT en 2021 por más de 2,9 millones de dólares.",
            ],
            [
                'title' => 'The New Vimeo Player: el demo técnico que mostró el futuro del video',
                'category' => 'tecnologia',
                'tags' => 'vimeo, tecnología, viral',
                'video' => 'https://vimeo.com/76979871',
                'views' => 2_800_000, 'likes' => 140_000, 'shares' => 95_000, 'comments' => 4_200,
                'published' => now()->subDays(13)->subHours(8),
                'excerpt' => 'El video demo de Vimeo se volvió un clásico para probar reproductores y pantallas nuevas.',
                'body' => "Con su estética minimalista y animación impecable, el clip se transformó en referencia de calidad de video en la web.\n\nTodavía se usa para probar televisores, monitores y conexiones de streaming.",
            ],
        ];

        foreach ($articles as $data) {
            $category = Category::where('slug', $data['category'])->first();

            $article = Article::withTrashed()->firstOrNew(['video_url' => $data['video']]);

            $article->fill([
                'title' => $data['title'],
                'excerpt' => $data['excerpt'],
                'body' => $data['body'],
                'views_count' => $data['views'],
                'likes_count' => $data['likes'],
                'shares_count' => $data['shares'],
                'comments_count' => $data['comments'],
                'status' => Article::STATUS_PUBLISHED,
                'is_featured' => $data['featured'] ?? false,
                'published_at' => $data['published'],
            ]);

            $article->category()->associate($category);
            $article->user_id = $author?->id;
            $article->save();

            $article->syncTagsFromList($data['tags']);
        }
    }
}
