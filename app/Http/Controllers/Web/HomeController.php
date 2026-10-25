<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    /**
     * Portada: destacada del día, tendencias y últimas noticias.
     */
    public function index(): View
    {
        $featured = Article::published()
            ->with('category')
            ->where(fn ($q) => $q->where('is_featured', true)->orWhereNotNull('video_url'))
            ->recent()
            ->first();

        $trending = Article::published()
            ->with('category')
            ->whereKeyNot($featured?->id)
            ->trending()
            ->limit(10)
            ->get();

        $latest = Article::published()
            ->with('category')
            ->whereKeyNot($trending->pluck('id')->push($featured?->id)->filter())
            ->recent()
            ->limit(9)
            ->get();

        $platforms = $this->platformStats();

        return view('home', compact('featured', 'trending', 'latest', 'platforms'));
    }

    /**
     * Ranking completo de tendencias.
     */
    public function trending(): View
    {
        $articles = Article::published()
            ->with('category')
            ->trending()
            ->paginate(15)
            ->withQueryString();

        return view('articles.index', [
            'title' => '🔥 Tendencias ahora',
            'subtitle' => 'Las noticias de videos virales con mayor engagement en este momento.',
            'articles' => $articles,
            'showRank' => true,
        ]);
    }

    /**
     * Noticias de una plataforma de video (YouTube, TikTok, Instagram...).
     */
    public function platform(string $platform): View
    {
        $platform = Str::lower($platform);

        $allowed = array_diff(config('ultranews.platforms', []), ['external']);

        abort_unless(in_array($platform, $allowed, true), 404);

        $articles = Article::published()
            ->with('category')
            ->byPlatform($platform)
            ->recent()
            ->paginate(12)
            ->withQueryString();

        return view('articles.index', [
            'title' => 'Videos virales en '.Str::headline($platform),
            'subtitle' => 'Las noticias con videos de '.Str::headline($platform).' que están explotando en las redes.',
            'articles' => $articles,
            'showRank' => false,
        ]);
    }

    /**
     * Conteo de noticias publicadas por plataforma de video.
     *
     * @return \Illuminate\Support\Collection<int, object>
     */
    protected function platformStats()
    {
        return Article::published()
            ->select('video_platform as platform')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(views_count) as views')
            ->whereNotNull('video_platform')
            ->groupBy('video_platform')
            ->orderByDesc('total')
            ->get();
    }
}
