<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Planificador de UltraNews
|--------------------------------------------------------------------------
|
| Estas tareas se ejecutan cuando configurás el cron del scheduler de
| Laravel en el servidor:  * * * * * php artisan schedule:run
|
*/

// Importar los videos más populares de YouTube cada hora.
Schedule::command('ultranews:fetch-viral')
    ->hourly()
    ->withoutOverlapping()
    ->onOneServer();

// Recalcular el score de tendencias (decae con el tiempo) cada 15 minutos.
Schedule::command('ultranews:refresh-trending')
    ->everyFifteenMinutes()
    ->withoutOverlapping()
    ->onOneServer();
