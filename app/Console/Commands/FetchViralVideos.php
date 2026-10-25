<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\Category;
use App\Services\TrendingService;
use App\Services\VideoEmbedService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class FetchViralVideos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ultranews:fetch-viral
        {--platform=youtube : Plataforma a consultar (youtube)}
        {--region= : Código de región ISO-3166-1 (AR, MX, ES, US...). Por defecto usa la configuración}
        {--limit=10 : Cantidad máxima de videos a importar}
        {--category= : Slug de la categoría destino. Por defecto usa la configuración}
        {--dry-run : Muestra lo que se importaría sin guardar nada}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importa los videos virales más populares (YouTube Data API) como noticias de UltraNews';

    /**
     * Execute the console command.
     */
    public function handle(VideoEmbedService $videos, TrendingService $trending): int
    {
        $platform = Str::lower((string) $this->option('platform'));

        if ($platform !== 'youtube') {
            $this->error("La plataforma «{$platform}» todavía no está soportada. Única disponible: youtube.");
            $this->line('Tip: los videos de TikTok/Instagram se pueden cargar manualmente desde el panel de administración.');

            return self::FAILURE;
        }

        $apiKey = (string) config('services.youtube.key');

        if ($apiKey === '') {
            $this->error('Falta la clave de la API de YouTube. Configurá YOUTUBE_API_KEY en tu archivo .env');
            $this->line('Podés crear una gratis en: https://console.cloud.google.com/apis/credentials');

            return self::FAILURE;
        }

        $region = (string) ($this->option('region') ?: config('ultranews.ingestion.region', 'AR'));
        $limit = max(1, min(50, (int) $this->option('limit')));
        $dryRun = (bool) $this->option('dry-run');

        $this->info("Consultando videos más populares de YouTube (región: {$region})...");

        $response = Http::timeout(20)
            ->acceptJson()
            ->get(config('services.youtube.base_uri').'/videos', [
                'part' => 'snippet,statistics',
                'chart' => 'mostPopular',
                'regionCode' => $region,
                'maxResults' => $limit,
                'key' => $apiKey,
            ]);

        if ($response->failed()) {
            $this->error('La API de YouTube respondió con error '.$response->status());
            $this->line(Str::limit($response->body(), 300));

            return self::FAILURE;
        }

        $items = $response->json('items', []);

        if ($items === []) {
            $this->warn('La API no devolvió videos.');

            return self::SUCCESS;
        }

        $categorySlug = (string) ($this->option('category') ?: config('ultranews.ingestion.category', 'virales'));
        $category = null;

        if (! $dryRun) {
            $category = Category::firstOrCreate(
                ['slug' => Str::slug($categorySlug)],
                ['name' => Str::headline($categorySlug), 'description' => 'Videos virales importados automáticamente.'],
            );
        }

        $created = 0;
        $updated = 0;
        $rows = [];

        foreach ($items as $item) {
            $videoId = $item['id'] ?? null;
            $snippet = $item['snippet'] ?? [];
            $stats = $item['statistics'] ?? [];

            if (blank($videoId) || blank($snippet['title'] ?? null)) {
                continue;
            }

            $watchUrl = 'https://www.youtube.com/watch?v='.$videoId;
            $embed = $videos->parse($watchUrl);

            $attributes = [
                'title' => Str::limit($snippet['title'], 250, ''),
                'excerpt' => Str::limit(trim(preg_replace('/\s+/', ' ', $snippet['description'] ?? '') ?? ''), 480),
                'video_url' => $watchUrl,
                'video_platform' => $embed['platform'] ?? 'youtube',
                'video_id' => $videoId,
                'thumbnail_url' => $snippet['thumbnails']['maxres']['url']
                    ?? $snippet['thumbnails']['high']['url']
                    ?? $embed['thumbnail']
                    ?? null,
                'views_count' => (int) ($stats['viewCount'] ?? 0),
                'likes_count' => (int) ($stats['likeCount'] ?? 0),
                'comments_count' => (int) ($stats['commentCount'] ?? 0),
                'published_at' => isset($snippet['publishedAt']) ? Carbon::parse($snippet['publishedAt']) : now(),
                'source_url' => isset($snippet['channelId'])
                    ? 'https://www.youtube.com/channel/'.$snippet['channelId']
                    : null,
            ];

            $existing = Article::withTrashed()
                ->where('video_platform', 'youtube')
                ->where('video_id', $videoId)
                ->first();

            if ($dryRun) {
                $rows[] = [
                    $videoId,
                    Str::limit($attributes['title'], 55),
                    number_format($attributes['views_count']),
                    $existing ? 'actualizar' : 'crear',
                ];

                continue;
            }

            if ($existing !== null) {
                $existing->fill($attributes);
                $existing->restore();
                $existing->save();
                $updated++;
            } else {
                Article::create($attributes + [
                    'category_id' => $category?->id,
                    'status' => Article::STATUS_PUBLISHED,
                ]);
                $created++;
            }
        }

        if ($dryRun) {
            $this->table(['Video ID', 'Título', 'Views', 'Acción'], $rows);
            $this->info('Dry run: no se guardaron cambios.');

            return self::SUCCESS;
        }

        $trending->refreshAll();

        $this->info("Listo: {$created} noticias nuevas, {$updated} actualizadas.");

        return self::SUCCESS;
    }
}
