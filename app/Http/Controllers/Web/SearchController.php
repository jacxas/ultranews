<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Búsqueda de noticias publicadas (?q=...).
     */
    public function index(Request $request): View
    {
        $term = trim((string) $request->query('q', ''));

        $articles = Article::published()
            ->with('category')
            ->search($term)
            ->recent()
            ->paginate(12)
            ->withQueryString();

        return view('articles.index', [
            'title' => $term !== '' ? "Resultados para «{$term}»" : 'Buscar noticias',
            'subtitle' => $term !== ''
                ? $articles->total().' noticia(s) encontrada(s).'
                : 'Escribí una palabra clave para buscar entre las noticias de videos virales.',
            'articles' => $articles,
            'showRank' => false,
            'searchTerm' => $term,
        ]);
    }
}
