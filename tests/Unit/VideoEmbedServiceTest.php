<?php

namespace Tests\Unit;

use App\Services\VideoEmbedService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class VideoEmbedServiceTest extends TestCase
{
    private VideoEmbedService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new VideoEmbedService;
    }

    #[DataProvider('videoUrls')]
    public function test_parses_video_urls(string $url, string $platform, ?string $videoId, ?string $embedContains): void
    {
        $result = $this->service->parse($url);

        $this->assertNotNull($result);
        $this->assertSame($platform, $result['platform']);
        $this->assertSame($videoId, $result['video_id']);

        if ($embedContains !== null) {
            $this->assertNotNull($result['embed_url']);
            $this->assertStringContainsString($embedContains, $result['embed_url']);
        } else {
            $this->assertNull($result['embed_url']);
        }
    }

    /**
     * @return array<string, array{0: string, 1: string, 2: ?string, 3: ?string}>
     */
    public static function videoUrls(): array
    {
        return [
            'youtube watch' => [
                'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'youtube', 'dQw4w9WgXcQ', 'youtube.com/embed/dQw4w9WgXcQ',
            ],
            'youtube watch con parámetros extra' => [
                'https://www.youtube.com/watch?v=dQw4w9WgXcQ&t=120s',
                'youtube', 'dQw4w9WgXcQ', 'youtube.com/embed/dQw4w9WgXcQ',
            ],
            'youtube watch con parámetro antes de v' => [
                'https://www.youtube.com/watch?feature=share&v=dQw4w9WgXcQ',
                'youtube', 'dQw4w9WgXcQ', 'youtube.com/embed/dQw4w9WgXcQ',
            ],
            'youtu.be corto' => [
                'https://youtu.be/9bZkp7q19f0',
                'youtube', '9bZkp7q19f0', 'youtube.com/embed/9bZkp7q19f0',
            ],
            'youtube shorts' => [
                'https://www.youtube.com/shorts/kJQP7kiw5Fk',
                'youtube', 'kJQP7kiw5Fk', 'youtube.com/embed/kJQP7kiw5Fk',
            ],
            'youtube embed directo' => [
                'https://www.youtube.com/embed/OPf0YbXqDm0',
                'youtube', 'OPf0YbXqDm0', 'youtube.com/embed/OPf0YbXqDm0',
            ],
            'youtube sin esquema' => [
                'youtu.be/dQw4w9WgXcQ',
                'youtube', 'dQw4w9WgXcQ', 'youtube.com/embed/dQw4w9WgXcQ',
            ],
            'tiktok canónico' => [
                'https://www.tiktok.com/@scout2015/video/6718335390845095173',
                'tiktok', '6718335390845095173', 'tiktok.com/embed/v2/6718335390845095173',
            ],
            'tiktok enlace corto (sin ID resoluble)' => [
                'https://vm.tiktok.com/ZM8abcdEf/',
                'tiktok', null, null,
            ],
            'instagram reel' => [
                'https://www.instagram.com/reel/Cx1234abcd/',
                'instagram', 'Cx1234abcd', 'instagram.com/reel/Cx1234abcd/embed',
            ],
            'instagram post' => [
                'https://www.instagram.com/p/Cx1234abcd/',
                'instagram', 'Cx1234abcd', 'instagram.com/p/Cx1234abcd/embed',
            ],
            'x status' => [
                'https://x.com/jack/status/20',
                'x', '20', null,
            ],
            'twitter status clásico' => [
                'https://twitter.com/TheEllenShow/status/440322224407314432',
                'x', '440322224407314432', null,
            ],
            'vimeo' => [
                'https://vimeo.com/76979871',
                'vimeo', '76979871', 'player.vimeo.com/video/76979871',
            ],
            'facebook watch' => [
                'https://www.facebook.com/watch/?v=1234567890',
                'facebook', null, 'facebook.com/plugins/video.php',
            ],
            'url externa' => [
                'https://example.com/mi-video-viral',
                'external', null, null,
            ],
        ];
    }

    public function test_devuelve_null_para_urls_vacias(): void
    {
        $this->assertNull($this->service->parse(null));
        $this->assertNull($this->service->parse(''));
        $this->assertNull($this->service->parse('   '));
    }

    public function test_youtube_genera_miniatura(): void
    {
        $result = $this->service->parse('https://www.youtube.com/watch?v=dQw4w9WgXcQ');

        $this->assertSame('https://i.ytimg.com/vi/dQw4w9WgXcQ/hqdefault.jpg', $result['thumbnail']);
    }

    public function test_is_embeddable(): void
    {
        $this->assertTrue($this->service->isEmbeddable('https://youtu.be/dQw4w9WgXcQ'));
        $this->assertFalse($this->service->isEmbeddable('https://x.com/jack/status/20'));
        $this->assertFalse($this->service->isEmbeddable(null));
    }
}
