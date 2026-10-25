<?php

namespace App\Models;

use App\Services\TrendingService;
use App\Services\VideoEmbedService;
use Database\Factories\ArticleFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Article extends Model
{
    /** @use HasFactory<ArticleFactory> */
    use HasFactory, SoftDeletes;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'body',
        'category_id',
        'user_id',
        'video_url',
        'video_platform',
        'video_id',
        'thumbnail_url',
        'source_url',
        'views_count',
        'likes_count',
        'shares_count',
        'comments_count',
        'trending_score',
        'status',
        'is_featured',
        'published_at',
        'meta_title',
        'meta_description',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'is_featured' => 'boolean',
            'views_count' => 'integer',
            'likes_count' => 'integer',
            'shares_count' => 'integer',
            'comments_count' => 'integer',
            'trending_score' => 'float',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Article $article): void {
            if (blank($article->slug)) {
                $article->slug = $article->uniqueSlugFrom($article->title);
            }

            // Parsear el video cada vez que cambia la URL (o si falta plataforma).
            if ($article->isDirty('video_url') || blank($article->video_platform)) {
                $embed = app(VideoEmbedService::class)->parse($article->video_url);

                if ($embed !== null) {
                    $article->video_platform = $embed['platform'];
                    $article->video_id = $embed['video_id'];

                    if (blank($article->thumbnail_url) && filled($embed['thumbnail'])) {
                        $article->thumbnail_url = $embed['thumbnail'];
                    }
                }
            }

            // Fecha de publicación automática al pasar a "published".
            if ($article->status === self::STATUS_PUBLISHED && is_null($article->published_at)) {
                $article->published_at = now();
            }

            // El score de tendencias siempre se mantiene fresco al guardar.
            $article->trending_score = app(TrendingService::class)->scoreFor($article);
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /* ---------------------------------------------------------------------
     | Relaciones
     * ------------------------------------------------------------------ */

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /* ---------------------------------------------------------------------
     | Scopes
     * ------------------------------------------------------------------ */

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', self::STATUS_PUBLISHED)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeDrafts(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeTrending(Builder $query): Builder
    {
        return $query->orderByDesc('trending_score')->orderByDesc('id');
    }

    public function scopeRecent(Builder $query): Builder
    {
        return $query->orderByDesc('published_at')->orderByDesc('id');
    }

    public function scopeMostViewed(Builder $query): Builder
    {
        return $query->orderByDesc('views_count')->orderByDesc('id');
    }

    public function scopeByPlatform(Builder $query, string $platform): Builder
    {
        return $query->where('video_platform', Str::lower($platform));
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        return $query->where(function (Builder $sub) use ($term): void {
            $sub->where('title', 'like', "%{$term}%")
                ->orWhere('excerpt', 'like', "%{$term}%")
                ->orWhere('body', 'like', "%{$term}%");
        });
    }

    /* ---------------------------------------------------------------------
     | Helpers
     * ------------------------------------------------------------------ */

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED
            && $this->published_at !== null
            && $this->published_at->lessThanOrEqualTo(now());
    }

    /**
     * Datos del video embebido (plataforma, embed, miniatura, enlace).
     *
     * @return array{platform: string, video_id: ?string, embed_url: ?string, watch_url: string, thumbnail: ?string}|null
     */
    public function embed(): ?array
    {
        if (blank($this->video_url)) {
            return null;
        }

        return app(VideoEmbedService::class)->parse($this->video_url);
    }

    /**
     * Engagement ponderado según la configuración de tendencias.
     */
    public function engagement(): int
    {
        return app(TrendingService::class)->engagementFor($this);
    }

    /**
     * Sincroniza etiquetas a partir de una lista separada por comas.
     */
    public function syncTagsFromList(?string $list): void
    {
        if (is_null($list)) {
            return;
        }

        $names = collect(explode(',', $list))
            ->map(fn (string $name) => trim($name))
            ->filter(fn (string $name) => $name !== '')
            ->unique(fn (string $name) => Str::lower($name))
            ->values();

        $ids = $names->map(function (string $name) {
            $slug = Str::slug($name) ?: Str::lower($name);

            return Tag::firstOrCreate(
                ['slug' => $slug],
                ['name' => $name],
            )->id;
        })->all();

        $this->tags()->sync($ids);
    }

    protected function uniqueSlugFrom(string $title): string
    {
        $base = Str::slug($title) ?: 'noticia';
        $slug = $base;
        $i = 2;

        while ($this->slugAlreadyUsed($slug)) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }

    protected function slugAlreadyUsed(string $slug): bool
    {
        $query = static::withTrashed()->where('slug', $slug);

        if ($this->getKey()) {
            $query->whereKeyNot($this->getKey());
        }

        return $query->exists();
    }
}
