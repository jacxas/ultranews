<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * Detalle de la noticia con el video embebido.
     */
    public function show(Request $request, Article $article): View
    {
        $isAdmin = $request->user()?->isAdmin() ?? false;

        // Los borradores solo son visibles para administradores (modo preview).
        abort_unless($article->isPublished() || $isAdmin, 404);

        $this->registerView($request, $article);

        $related = Article::published()
            ->with('category')
            ->whereKeyNot($article->id)
            ->when(
                $article->category_id,
                fn ($q) => $q->where('category_id', $article->category_id),
                fn ($q) => $q->trending(),
            )
            ->recent()
            ->limit(4)
            ->get();

        return view('articles.show', compact('article', 'related'));
    }

    /**
     * Incrementa el contador de visitas una sola vez por sesión.
     */
    protected function registerView(Request $request, Article $article): void
    {
        if (! $article->isPublished()) {
            return;
        }

        $key = 'viewed_article_'.$article->id;

        if ($request->session()->has($key)) {
            return;
        }

        $request->session()->put($key, true);
        $article->increment('views_count');
    }
}
