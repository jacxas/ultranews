<?php

namespace App\Models;

use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Category $category): void {
            if (blank($category->slug)) {
                $category->slug = $category->uniqueSlugFrom($category->name);
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function publishedArticles(): HasMany
    {
        return $this->hasMany(Article::class)->published();
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    protected function uniqueSlugFrom(string $name): string
    {
        $base = Str::slug($name) ?: 'categoria';
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
