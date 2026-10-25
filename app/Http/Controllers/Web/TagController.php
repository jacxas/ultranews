<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Tag;
use Illuminate\Contracts\View\View;

class TagController extends Controller
{
    /**
     * Noticias publicadas etiquetadas con un tag.
     */
    public function show(Tag $tag): View
    {
        $articles = $tag->articles()
            ->published()
            ->with('category')
            ->recent()
            ->paginate(12)
            ->withQueryString();

        return view('articles.index', [
            'title' => '#'.$tag->name,
            'subtitle' => 'Todas las noticias etiquetadas con '.$tag->name,
            'articles' => $articles,
            'showRank' => false,
        ]);
    }
}
