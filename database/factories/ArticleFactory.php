<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Article>
 */
class ArticleFactory extends Factory
{
    protected $model = Article::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $views = fake()->numberBetween(1_000, 5_000_000);
        $published = fake()->boolean(85);

        return [
            'title' => rtrim(fake()->sentence(fake()->numberBetween(4, 9)), '.'),
            'excerpt' => fake()->sentence(12),
            'body' => fake()->paragraphs(3, true),
            'category_id' => Category::factory(),
            'user_id' => null,
            'video_url' => fake()->randomElement($this->videoPool()),
            'source_url' => null,
            'views_count' => $views,
            'likes_count' => (int) round($views * fake()->randomFloat(2, 0.01, 0.08)),
            'shares_count' => (int) round($views * fake()->randomFloat(3, 0.001, 0.01)),
            'comments_count' => (int) round($views * fake()->randomFloat(3, 0.001, 0.005)),
            'status' => $published ? Article::STATUS_PUBLISHED : Article::STATUS_DRAFT,
            'is_featured' => fake()->boolean(10),
            'published_at' => $published ? fake()->dateTimeBetween('-30 days', 'now') : null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => [
            'status' => Article::STATUS_PUBLISHED,
            'published_at' => fake()->dateTimeBetween('-14 days', '-1 hour'),
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn () => [
            'status' => Article::STATUS_DRAFT,
            'published_at' => null,
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn () => ['is_featured' => true]);
    }

    public function platform(string $platform): static
    {
        return $this->state(fn () => [
            'video_url' => fake()->randomElement(
                array_values(array_filter(
                    $this->videoPool(),
                    fn (string $url) => str_contains($url, $platform) || ($platform === 'x' && str_contains($url, 'twitter'))
                )) ?: $this->videoPool()
            ),
        ]);
    }

    /**
     * @return array<int, string>
     */
    protected function videoPool(): array
    {
        return [
            'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'https://youtu.be/9bZkp7q19f0',
            'https://www.youtube.com/shorts/kJQP7kiw5Fk',
            'https://www.tiktok.com/@scout2015/video/6718335390845095173',
            'https://www.instagram.com/reel/CxViralDemo01/',
            'https://vimeo.com/76979871',
            'https://twitter.com/jack/status/20',
        ];
    }
}
