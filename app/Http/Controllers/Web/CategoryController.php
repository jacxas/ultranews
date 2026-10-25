<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Contracts\View\View;

class CategoryController extends Controller
{
    /**
     * Noticias publicadas de una categoría.
     */
    public function show(Category $category): View
    {
        $articles = $category->articles()
            ->published()
            ->with('category')
            ->recent()
            ->paginate(12)
            ->withQueryString();

        return view('articles.index', [
            'title' => $category->name,
            'subtitle' => $category->description,
            'articles' => $articles,
            'showRank' => false,
            'accent' => $category->color,
        ]);
    }
}
