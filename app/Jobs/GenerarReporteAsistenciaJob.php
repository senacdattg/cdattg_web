<?php

namespace App\Jobs;

use App\Services\ReporteService;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class GenerarReporteAsistenciaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $fichaId;
    public string $fechaInicio;
    public string $fechaFin;
    public string $formato;
    public User $usuario;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 300;

    /**
     * Create a new job instance.
     */
    public function __construct(int $fichaId, string $fechaInicio, string $fechaFin, string $formato, User $usuario)
    {
        $this->fichaId = $fichaId;
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
        $this->formato = $formato;
        $this->usuario = $usuario;
    }

    /**
     * Execute the job.
     */
    public function handle(ReporteService $reporteService): void
    {
        try {
            Log::info('Iniciando generación de reporte', [
                'ficha_id' => $this->fichaId,
                'usuario_id' => $this->usuario->id,
            ]);

            $archivo = $reporteService->generarReporteAsistencia(
                $this->fichaId,
                $this->fechaInicio,
                $this->fechaFin,
                $this->formato
            );

            // Enviar notificación al usuario
            // Mail::to($this->usuario->email)->send(new ReporteGenerado($archivo));

            Log::info('Reporte generado exitosamente', [
                'ficha_id' => $this->fichaId,
                'archivo' => $archivo,
                'usuario_id' => $this->usuario->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Error generando reporte', [
                'ficha_id' => $this->fichaId,
                'error' => $e->getMessage(),
            ]);

            // Reintentará automáticamente si falla
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Fallo definitivo al generar reporte', [
            'ficha_id' => $this->fichaId,
            'usuario_id' => $this->usuario->id,
            'error' => $exception->getMessage(),
        ]);

        // Notificar al usuario del fallo
        // Mail::to($this->usuario->email)->send(new ReporteFallido());
    }
}

