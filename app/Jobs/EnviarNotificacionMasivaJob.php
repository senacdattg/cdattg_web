<?php

namespace App\Jobs;

use App\Services\NotificacionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class EnviarNotificacionMasivaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Collection $aprendices;
    public string $mensaje;
    public string $tipo;

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
    public $timeout = 600;

    /**
     * Create a new job instance.
     */
    public function __construct(Collection $aprendices, string $mensaje, string $tipo = 'general')
    {
        $this->aprendices = $aprendices;
        $this->mensaje = $mensaje;
        $this->tipo = $tipo;
    }

    /**
     * Execute the job.
     */
    public function handle(NotificacionService $notificacionService): void
    {
        try {
            Log::info('Iniciando envío masivo de notificaciones', [
                'total_aprendices' => $this->aprendices->count(),
                'tipo' => $this->tipo,
            ]);

            $enviados = $notificacionService->notificarAprendices($this->aprendices, $this->mensaje);

            Log::info('Notificaciones masivas completadas', [
                'total' => $this->aprendices->count(),
                'enviados' => $enviados,
                'fallidos' => $this->aprendices->count() - $enviados,
            ]);
        } catch (\Exception $e) {
            Log::error('Error en envío masivo de notificaciones', [
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
        Log::error('Fallo definitivo en envío masivo', [
            'total_aprendices' => $this->aprendices->count(),
            'error' => $exception->getMessage(),
        ]);
    }
}

