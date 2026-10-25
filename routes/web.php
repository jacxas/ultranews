<?php

use App\Http\Controllers\Admin\ArticleAdminController;
use App\Http\Controllers\Admin\CategoryAdminController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Web\ArticleController;
use App\Http\Controllers\Web\CategoryController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\SearchController;
use App\Http\Controllers\Web\TagController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas públicas del portal
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/tendencias', [HomeController::class, 'trending'])->name('trending');
Route::get('/buscar', [SearchController::class, 'index'])->name('search');
Route::get('/plataforma/{platform}', [HomeController::class, 'platform'])->name('platform');
Route::get('/categoria/{category}', [CategoryController::class, 'show'])->name('categories.show');
Route::get('/etiqueta/{tag}', [TagController::class, 'show'])->name('tags.show');
Route::get('/noticia/{article}', [ArticleController::class, 'show'])->name('articles.show');

/*
|--------------------------------------------------------------------------
| Autenticación del panel
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Panel de administración (sesión + admin)
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'admin'])
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::patch('articles/{article}/toggle', [ArticleAdminController::class, 'toggle'])->name('articles.toggle');
        Route::patch('articles/{article}/restore', [ArticleAdminController::class, 'restore'])->withTrashed()->name('articles.restore');
        Route::delete('articles/{article}/force', [ArticleAdminController::class, 'forceDestroy'])->withTrashed()->name('articles.force');
        Route::resource('articles', ArticleAdminController::class)->except('show');

        Route::resource('categories', CategoryAdminController::class)->except('show');
    });
