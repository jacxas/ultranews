<?php

use App\Http\Controllers\Api\ArticleApiController;
use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\TagApiController;
use App\Http\Controllers\Api\TrendingApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API REST de UltraNews
|--------------------------------------------------------------------------
|
| Rutas públicas (solo lectura) y rutas protegidas con token personal:
|
|   Authorization: Bearer <token>
|
| Los tokens se generan con:  php artisan ultranews:issue-token {email}
| La escritura exige además que el usuario sea administrador.
|
*/

// Lectura pública
Route::get('articles', [ArticleApiController::class, 'index'])->name('api.articles.index');
Route::get('trending', [TrendingApiController::class, 'index'])->name('api.trending');
Route::get('categories', [CategoryApiController::class, 'index'])->name('api.categories.index');
Route::get('categories/{category}', [CategoryApiController::class, 'show'])->name('api.categories.show');
Route::get('tags', [TagApiController::class, 'index'])->name('api.tags.index');
Route::get('articles/{article}', [ArticleApiController::class, 'show'])->name('api.articles.show');

// Contador de reproducciones (público)
Route::post('articles/{article}/view', [ArticleApiController::class, 'view'])->name('api.articles.view');

// Escritura (token de API + administrador)
Route::middleware(['api.token', 'admin'])->group(function () {
    Route::post('articles', [ArticleApiController::class, 'store'])->name('api.articles.store');
    Route::match(['put', 'patch'], 'articles/{article}', [ArticleApiController::class, 'update'])->name('api.articles.update');
    Route::delete('articles/{article}', [ArticleApiController::class, 'destroy'])->name('api.articles.destroy');
});
