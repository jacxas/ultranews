<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryApiController extends Controller
{
    /**
     * GET /api/categories — listar categorías con conteo de noticias publicadas.
     */
    public function index(): AnonymousResourceCollection
    {
        $categories = Category::query()
            ->ordered()
            ->withCount(['articles' => fn ($q) => $q->published()])
            ->get();

        return CategoryResource::collection($categories);
    }

    /**
     * GET /api/categories/{slug} — detalle de una categoría.
     */
    public function show(Category $category): CategoryResource
    {
        $category->loadCount(['articles' => fn ($q) => $q->published()]);

        return new CategoryResource($category);
    }
}
