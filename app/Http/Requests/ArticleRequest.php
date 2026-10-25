<?php

namespace App\Http\Requests;

use App\Models\Article;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validación compartida de noticias (panel de administración y API REST).
 *
 * Semántica: los campos ausentes no se modifican; los presentes (incluso
 * null) se actualizan. `tags` es una lista de nombres separados por comas.
 */
class ArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    protected function prepareForValidation(): void
    {
        // En formularios web la casilla sin marcar no envía el campo.
        if (! $this->expectsJson()) {
            $this->merge([
                'is_featured' => $this->boolean('is_featured'),
            ]);
        }
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $article = $this->route('article');

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable', 'string', 'max:255',
                Rule::unique('articles', 'slug')->ignore($article instanceof Article ? $article->id : null),
            ],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['nullable', 'string'],
            'category_id' => ['nullable', 'integer', Rule::exists('categories', 'id')],
            'video_url' => ['nullable', 'url', 'max:2048'],
            'source_url' => ['nullable', 'url', 'max:2048'],
            'thumbnail_url' => ['nullable', 'url', 'max:2048'],
            'status' => ['sometimes', Rule::in([Article::STATUS_DRAFT, Article::STATUS_PUBLISHED])],
            'is_featured' => ['sometimes', 'boolean'],
            'views_count' => ['sometimes', 'integer', 'min:0'],
            'likes_count' => ['sometimes', 'integer', 'min:0'],
            'shares_count' => ['sometimes', 'integer', 'min:0'],
            'comments_count' => ['sometimes', 'integer', 'min:0'],
            'published_at' => ['nullable', 'date'],
            'tags' => ['nullable', 'string', 'max:500'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Atributos validados listos para fill/create (sin `tags`).
     *
     * @return array<string, mixed>
     */
    public function articleAttributes(): array
    {
        $attributes = $this->validated();

        unset($attributes['tags']);

        return $attributes;
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'El título de la noticia es obligatorio.',
            'video_url.url' => 'La URL del video no tiene un formato válido.',
            'status.in' => 'El estado debe ser «draft» o «published».',
        ];
    }
}
