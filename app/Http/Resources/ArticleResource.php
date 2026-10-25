<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Article
 */
class ArticleResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $embed = $this->embed();

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'url' => route('articles.show', $this),
            'excerpt' => $this->excerpt,
            'body' => $this->body,
            'status' => $this->status,
            'is_featured' => (bool) $this->is_featured,

            'category' => new CategoryResource($this->whenLoaded('category')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'author' => $this->when(
                $this->relationLoaded('author') && $this->author !== null,
                fn () => [
                    'id' => $this->author->id,
                    'name' => $this->author->name,
                ],
            ),

            'video' => $embed !== null ? [
                'platform' => $embed['platform'],
                'id' => $embed['video_id'],
                'url' => $this->video_url,
                'embed_url' => $embed['embed_url'],
                'thumbnail' => $this->thumbnail_url ?: $embed['thumbnail'],
            ] : null,

            'source_url' => $this->source_url,

            'stats' => [
                'views' => (int) $this->views_count,
                'likes' => (int) $this->likes_count,
                'shares' => (int) $this->shares_count,
                'comments' => (int) $this->comments_count,
                'trending_score' => (float) $this->trending_score,
            ],

            'seo' => [
                'meta_title' => $this->meta_title,
                'meta_description' => $this->meta_description,
            ],

            'published_at' => $this->published_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
