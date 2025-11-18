<?php

namespace App\Console\Commands;

use App\Services\PersonaIngresoSalidaService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcesarSalidasPendientesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ingreso-salida:procesar-salidas-pendientes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Procesa las salidas pendientes a la medianoche y genera un reporte';

    protected PersonaIngresoSalidaService $personaIngresoSalidaService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(PersonaIngresoSalidaService $personaIngresoSalidaService)
    {
        parent::__construct();
        $this->personaIngresoSalidaService = $personaIngresoSalidaService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Iniciando procesamiento de salidas pendientes...');

        try {
            $resultado = $this->personaIngresoSalidaService
                ->procesarSalidasPendientes();

            $this->info("✓ Procesadas {$resultado['total_procesadas']} salidas pendientes");
            $this->info("✓ Reporte generado con ID: {$resultado['reporte_id']}");

            Log::info('Salidas pendientes procesadas automáticamente', [
                'total_procesadas' => $resultado['total_procesadas'],
                'reporte_id' => $resultado['reporte_id'],
            ]);

            return 0;
        } catch (\Exception $e) {
            $this->error('Error procesando salidas pendientes: ' . $e->getMessage());
            Log::error('Error procesando salidas pendientes', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return 1;
        }
    }
}

