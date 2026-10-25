<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ArticleRequest;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ArticleAdminController extends Controller
{
    /**
     * Listado de noticias (con filtros y papelera).
     */
    public function index(Request $request): View
    {
        $trashed = $request->boolean('trashed');
        $status = (string) $request->query('status', '');
        $term = trim((string) $request->query('q', ''));

        $articles = Article::query()
            ->with('category')
            ->when($trashed, fn ($q) => $q->onlyTrashed(), fn ($q) => $q->withTrashed())
            ->when($status !== '', fn ($q) => $q->where('status', $status))
            ->search($term)
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.articles.index', compact('articles', 'trashed', 'status', 'term'));
    }

    /**
     * Formulario de alta.
     */
    public function create(): View
    {
        return view('admin.articles.form', [
            'article' => new Article(['status' => Article::STATUS_DRAFT]),
            'categories' => Category::ordered()->get(),
            'tagList' => '',
        ]);
    }

    /**
     * Guardar noticia nueva.
     */
    public function store(ArticleRequest $request): RedirectResponse
    {
        $article = Article::create($request->articleAttributes() + [
            'user_id' => $request->user()->id,
        ]);

        $article->syncTagsFromList($request->input('tags'));

        return redirect()
            ->route('admin.articles.index')
            ->with('status', "Noticia «{$article->title}» creada correctamente.");
    }

    /**
     * Formulario de edición.
     */
    public function edit(Article $article): View
    {
        return view('admin.articles.form', [
            'article' => $article,
            'categories' => Category::ordered()->get(),
            'tagList' => $article->tags->pluck('name')->implode(', '),
        ]);
    }

    /**
     * Actualizar noticia.
     */
    public function update(ArticleRequest $request, Article $article): RedirectResponse
    {
        $article->fill($request->articleAttributes());
        $article->save();
        $article->syncTagsFromList($request->input('tags'));

        return redirect()
            ->route('admin.articles.index')
            ->with('status', "Noticia «{$article->title}» actualizada.");
    }

    /**
     * Alternar entre borrador y publicada.
     */
    public function toggle(Article $article): RedirectResponse
    {
        $article->status = $article->status === Article::STATUS_PUBLISHED
            ? Article::STATUS_DRAFT
            : Article::STATUS_PUBLISHED;

        if ($article->status === Article::STATUS_PUBLISHED && is_null($article->published_at)) {
            $article->published_at = now();
        }

        $article->save();

        return back()->with('status', "«{$article->title}» ahora está ".($article->status === Article::STATUS_PUBLISHED ? 'publicada' : 'en borrador').'.' );
    }

    /**
     * Eliminar (soft delete).
     */
    public function destroy(Article $article): RedirectResponse
    {
        $article->delete();

        return redirect()
            ->route('admin.articles.index')
            ->with('status', "«{$article->title}» enviada a la papelera.");
    }

    /**
     * Restaurar desde la papelera.
     */
    public function restore(Request $request, Article $article): RedirectResponse
    {
        $article->restore();

        return redirect()
            ->route('admin.articles.index')
            ->with('status', "«{$article->title}» restaurada.");
    }

    /**
     * Borrado definitivo.
     */
    public function forceDestroy(Request $request, Article $article): RedirectResponse
    {
        $title = $article->title;
        $article->forceDelete();

        return redirect()
            ->route('admin.articles.index')
            ->with('status', "«{$title}» eliminada definitivamente.");
    }
}
