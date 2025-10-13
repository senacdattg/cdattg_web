<?php

namespace App\Console\Commands;

use App\Services\EstadisticasService;
use Illuminate\Console\Command;

class GenerarEstadisticasCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'estadisticas:generar 
                            {--mes= : Mes específico (1-12)}
                            {--anio= : Año específico}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera estadísticas del sistema';

    protected EstadisticasService $estadisticasService;

    /**
     * Create a new command instance.
     */
    public function __construct(EstadisticasService $estadisticasService)
    {
        parent::__construct();
        $this->estadisticasService = $estadisticasService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generando estadísticas...');

        try {
            $estadisticas = $this->estadisticasService->obtenerDashboardGeneral();

            $this->table(
                ['Métrica', 'Valor'],
                [
                    ['Total Aprendices', $estadisticas['aprendices']['total']],
                    ['Aprendices Activos', $estadisticas['aprendices']['activos']],
                    ['Total Fichas', $estadisticas['fichas']['total']],
                    ['Fichas Vigentes', $estadisticas['fichas']['vigentes']],
                    ['Total Instructores', $estadisticas['instructores']],
                    ['Asistencias Hoy', $estadisticas['asistencias_hoy']],
                ]
            );

            $this->info('✓ Estadísticas generadas exitosamente');

            return 0;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
    }
}

