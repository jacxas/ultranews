<?php

namespace App\Models;

use Database\Factories\TagFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Tag extends Model
{
    /** @use HasFactory<TagFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    protected static function booted(): void
    {
        static::saving(function (Tag $tag): void {
            if (blank($tag->slug)) {
                $tag->slug = $tag->uniqueSlugFrom($tag->name);
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class);
    }

    protected function uniqueSlugFrom(string $name): string
    {
        $base = Str::slug($name) ?: 'etiqueta';
        $slug = $base;
        $i = 2;

        while ($this->slugAlreadyUsed($slug)) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }

    protected function slugAlreadyUsed(string $slug): bool
    {
        $query = static::query()->where('slug', $slug);

        if ($this->getKey()) {
            $query->whereKeyNot($this->getKey());
        }

        return $query->exists();
    }
}
