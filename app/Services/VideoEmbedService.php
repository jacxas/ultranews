<?php

namespace App\Services;

/**
 * Parsea URLs de videos de redes sociales y devuelve los datos necesarios
 * para embeberlos: plataforma, ID, URL de embed, URL original y miniatura.
 *
 * Plataformas soportadas: YouTube, TikTok, Instagram, X/Twitter, Vimeo y
 * Facebook. Cualquier otra URL válida se marca como "external".
 */
class VideoEmbedService
{
    /**
     * @return array{platform: string, video_id: ?string, embed_url: ?string, watch_url: string, thumbnail: ?string}|null
     */
    public function parse(?string $url): ?array
    {
        $url = trim((string) $url);

        if ($url === '') {
            return null;
        }

        // URLs sin esquema ("youtu.be/xyz") se normalizan.
        if (! preg_match('~^https?://~i', $url)) {
            $url = 'https://'.$url;
        }

        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            return null;
        }

        return $this->parseYouTube($url)
            ?? $this->parseTikTok($url)
            ?? $this->parseInstagram($url)
            ?? $this->parseX($url)
            ?? $this->parseVimeo($url)
            ?? $this->parseFacebook($url)
            ?? $this->external($url);
    }

    /**
     * ¿La URL pertenece a una plataforma embebible con iframe?
     */
    public function isEmbeddable(?string $url): bool
    {
        $data = $this->parse($url);

        return $data !== null && filled($data['embed_url']);
    }

    /** @return array{platform: string, video_id: ?string, embed_url: ?string, watch_url: string, thumbnail: ?string}|null */
    protected function parseYouTube(string $url): ?array
    {
        $pattern = '~(?:youtube(?:-nocookie)?\.com/(?:watch\?(?:[^#\s]*&)?v=|shorts/|embed/|live/|v/)|youtu\.be/)([A-Za-z0-9_-]{6,})~i';

        if (! preg_match($pattern, $url, $matches)) {
            return null;
        }

        $id = $matches[1];

        return [
            'platform' => 'youtube',
            'video_id' => $id,
            'embed_url' => 'https://www.youtube.com/embed/'.$id,
            'watch_url' => 'https://www.youtube.com/watch?v='.$id,
            'thumbnail' => 'https://i.ytimg.com/vi/'.$id.'/hqdefault.jpg',
        ];
    }

    /** @return array{platform: string, video_id: ?string, embed_url: ?string, watch_url: string, thumbnail: ?string}|null */
    protected function parseTikTok(string $url): ?array
    {
        if (! preg_match('~tiktok\.com~i', $url)) {
            return null;
        }

        // Enlace canónico: tiktok.com/@usuario/video/1234567890123456789
        if (preg_match('~tiktok\.com/(?:@[\w.\-]+/video|v)/(\d+)~i', $url, $matches)) {
            $id = $matches[1];

            return [
                'platform' => 'tiktok',
                'video_id' => $id,
                'embed_url' => 'https://www.tiktok.com/embed/v2/'.$id,
                'watch_url' => $url,
                'thumbnail' => null,
            ];
        }

        // Enlaces cortos (vm.tiktok.com/...): no se puede resolver el ID sin HTTP.
        return [
            'platform' => 'tiktok',
            'video_id' => null,
            'embed_url' => null,
            'watch_url' => $url,
            'thumbnail' => null,
        ];
    }

    /** @return array{platform: string, video_id: ?string, embed_url: ?string, watch_url: string, thumbnail: ?string}|null */
    protected function parseInstagram(string $url): ?array
    {
        if (! preg_match('~instagram\.com/(?:reel|reels|p|tv)/([A-Za-z0-9_-]+)~i', $url, $matches)) {
            return null;
        }

        $type = preg_match('~instagram\.com/(reels?|p|tv)/~i', $url, $typeMatches) ? $typeMatches[1] : 'reel';
        $type = $type === 'reels' ? 'reel' : $type;
        $id = $matches[1];

        return [
            'platform' => 'instagram',
            'video_id' => $id,
            'embed_url' => sprintf('https://www.instagram.com/%s/%s/embed', $type, $id),
            'watch_url' => $url,
            'thumbnail' => null,
        ];
    }

    /** @return array{platform: string, video_id: ?string, embed_url: ?string, watch_url: string, thumbnail: ?string}|null */
    protected function parseX(string $url): ?array
    {
        if (! preg_match('~(?:twitter|x)\.com/[^/]+/status(?:es)?/(\d+)~i', $url, $matches)) {
            return null;
        }

        return [
            'platform' => 'x',
            'video_id' => $matches[1],
            // X no ofrece iframe: se embebe con blockquote + widgets.js en la vista.
            'embed_url' => null,
            'watch_url' => $url,
            'thumbnail' => null,
        ];
    }

    /** @return array{platform: string, video_id: ?string, embed_url: ?string, watch_url: string, thumbnail: ?string}|null */
    protected function parseVimeo(string $url): ?array
    {
        if (! preg_match('~vimeo\.com/(?:video/)?(\d+)~i', $url, $matches)) {
            return null;
        }

        $id = $matches[1];

        return [
            'platform' => 'vimeo',
            'video_id' => $id,
            'embed_url' => 'https://player.vimeo.com/video/'.$id,
            'watch_url' => 'https://vimeo.com/'.$id,
            'thumbnail' => null,
        ];
    }

    /** @return array{platform: string, video_id: ?string, embed_url: ?string, watch_url: string, thumbnail: ?string}|null */
    protected function parseFacebook(string $url): ?array
    {
        if (! preg_match('~facebook\.com~i', $url)) {
            return null;
        }

        if (! preg_match('~/(?:videos?|watch|reel|story\.php)~i', $url)) {
            return null;
        }

        return [
            'platform' => 'facebook',
            'video_id' => null,
            'embed_url' => 'https://www.facebook.com/plugins/video.php?href='.rawurlencode($url).'&show_text=false',
            'watch_url' => $url,
            'thumbnail' => null,
        ];
    }

    /** @return array{platform: string, video_id: ?string, embed_url: ?string, watch_url: string, thumbnail: ?string} */
    protected function external(string $url): array
    {
        return [
            'platform' => 'external',
            'video_id' => null,
            'embed_url' => null,
            'watch_url' => $url,
            'thumbnail' => null,
        ];
    }
}
