<?php

namespace App\Console\Commands;

use App\Core\Services\CacheService;
use Illuminate\Console\Command;

class CacheWarmupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:warmup 
                            {--flush : Limpiar caché antes de precargar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Precarga datos frecuentes en caché';

    protected CacheService $cacheService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(CacheService $cacheService)
    {
        parent::__construct();
        $this->cacheService = $cacheService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if ($this->option('flush')) {
            $this->info('Limpiando caché...');
            $this->cacheService->flush();
        }

        $this->info('Iniciando precarga de caché...');
        
        $this->cacheService->warmup();
        
        $this->info('✓ Caché precargada exitosamente');
        
        return 0;
    }
}

