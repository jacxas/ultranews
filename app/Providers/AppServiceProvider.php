<?php

namespace App\Providers;

use App\Models\Category;
use App\Services\TrendingService;
use App\Services\VideoEmbedService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(VideoEmbedService::class);

        $this->app->singleton(
            TrendingService::class,
            fn ($app) => new TrendingService(
                gravity: $app['config']->get('ultranews.trending.gravity', 1.6),
                weights: $app['config']->get('ultranews.trending.weights', []),
            ),
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Paginación con markup neutro (estilizada en public/css/ultranews.css).
        Paginator::defaultView('pagination::bootstrap-5');
        Paginator::defaultSimpleView('pagination::simple-bootstrap-5');

        // Categorías disponibles en la navegación del layout público.
        View::composer('layouts.app', function ($view): void {
            $view->with('navCategories', Category::ordered()->limit(10)->get());
        });
    }
}
