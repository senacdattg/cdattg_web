<?php

namespace App\Jobs;

use App\Services\AsistenciaService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcesarAsistenciasMasivasJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public array $asistencias;
    public int $caracterizacionId;

    public $tries = 3;
    public $timeout = 600;

    /**
     * Create a new job instance.
     */
    public function __construct(array $asistencias, int $caracterizacionId)
    {
        $this->asistencias = $asistencias;
        $this->caracterizacionId = $caracterizacionId;
    }

    /**
     * Execute the job.
     */
    public function handle(AsistenciaService $asistenciaService): void
    {
        try {
            Log::info('Iniciando procesamiento masivo de asistencias', [
                'total' => count($this->asistencias),
                'caracterizacion_id' => $this->caracterizacionId,
            ]);

            $cantidad = $asistenciaService->registrarAsistenciaLote(
                $this->asistencias, 
                $this->caracterizacionId
            );

            Log::info('Procesamiento masivo completado', [
                'registradas' => $cantidad,
            ]);
        } catch (\Exception $e) {
            Log::error('Error en procesamiento masivo', [
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Fallo definitivo en procesamiento masivo de asistencias', [
            'total' => count($this->asistencias),
            'error' => $exception->getMessage(),
        ]);
    }
}

