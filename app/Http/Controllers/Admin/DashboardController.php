<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    /**
     * Panel principal con métricas del portal.
     */
    public function index(): View
    {
        $stats = [
            'articles' => Article::count(),
            'published' => Article::where('status', Article::STATUS_PUBLISHED)->count(),
            'drafts' => Article::drafts()->count(),
            'categories' => Category::count(),
            'tags' => Tag::count(),
            'views' => (int) Article::sum('views_count'),
            'featured' => Article::featured()->count(),
        ];

        $topTrending = Article::published()->with('category')->trending()->limit(5)->get();
        $latestArticles = Article::with('category')->latest()->limit(5)->get();

        $byPlatform = Article::query()
            ->select('video_platform as platform')
            ->selectRaw('COUNT(*) as total')
            ->whereNotNull('video_platform')
            ->groupBy('video_platform')
            ->orderByDesc('total')
            ->get();

        return view('admin.dashboard', compact('stats', 'topTrending', 'latestArticles', 'byPlatform'));
    }
}
