<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Algoritmo de tendencias
    |--------------------------------------------------------------------------
    |
    | UltraNews ordena las noticias por un "trending score" al estilo
    | Hacker News: score = engagement / (edad_horas + 2) ^ gravedad.
    | Los pesos definen cuánto vale cada interacción social.
    |
    */

    'trending' => [
        'gravity' => (float) env('ULTRANEWS_TRENDING_GRAVITY', 1.6),

        'weights' => [
            'views' => 1,
            'likes' => 2,
            'shares' => 3,
            'comments' => 4,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Ingestión de videos virales
    |--------------------------------------------------------------------------
    |
    | Configuración del comando `ultranews:fetch-viral`, que importa los
    | videos más populares de YouTube (chart mostPopular) como noticias.
    |
    */

    'ingestion' => [
        'region' => env('ULTRANEWS_REGION', 'AR'),
        'category' => env('ULTRANEWS_INGEST_CATEGORY', 'virales'),
        'limit' => 10,
    ],

    /*
    |--------------------------------------------------------------------------
    | Plataformas soportadas
    |--------------------------------------------------------------------------
    |
    | Plataformas de video/redes sociales que el VideoEmbedService sabe
    | parsear para generar embeds, IDs y miniaturas.
    |
    */

    'platforms' => [
        'youtube',
        'tiktok',
        'instagram',
        'x',
        'vimeo',
        'facebook',
        'external',
    ],

];
