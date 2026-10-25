<?php

namespace App\Console\Commands;

use App\Services\TrendingService;
use Illuminate\Console\Command;

class RefreshTrending extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ultranews:refresh-trending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalcula el score de tendencias de todas las noticias (el score decae con el tiempo)';

    /**
     * Execute the console command.
     */
    public function handle(TrendingService $trending): int
    {
        $count = $trending->refreshAll();

        $this->info("Scores de tendencias recalculados para {$count} artículos.");

        return self::SUCCESS;
    }
}
