<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TagResource;
use App\Models\Tag;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TagApiController extends Controller
{
    /**
     * GET /api/tags — listar etiquetas con conteo de noticias.
     */
    public function index(): AnonymousResourceCollection
    {
        $tags = Tag::query()
            ->withCount(['articles' => fn ($q) => $q->published()])
            ->orderBy('name')
            ->get();

        return TagResource::collection($tags);
    }
}
