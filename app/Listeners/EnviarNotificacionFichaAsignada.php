<?php

namespace App\Listeners;

use App\Events\FichaAsignadaAInstructor;
use App\Services\NotificacionService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class EnviarNotificacionFichaAsignada implements ShouldQueue
{
    use InteractsWithQueue;

    protected NotificacionService $notificacionService;

    /**
     * Create the event listener.
     */
    public function __construct(NotificacionService $notificacionService)
    {
        $this->notificacionService = $notificacionService;
    }

    /**
     * Handle the event.
     */
    public function handle(FichaAsignadaAInstructor $event): void
    {
        try {
            $datosFicha = [
                'numero' => $event->ficha->ficha,
                'programa' => $event->ficha->programaFormacion->nombre ?? 'N/A',
                'fecha_inicio' => $event->ficha->fecha_inicio,
                'fecha_fin' => $event->ficha->fecha_fin,
                'detalles' => $event->detalles,
            ];

            $this->notificacionService->notificarNuevaFichaInstructor($event->instructor, $datosFicha);
        } catch (\Exception $e) {
            Log::error('Error en listener de ficha asignada', [
                'instructor_id' => $event->instructor->id,
                'ficha_id' => $event->ficha->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(FichaAsignadaAInstructor $event, \Throwable $exception): void
    {
        Log::error('Fallo al procesar notificación de ficha asignada', [
            'instructor_id' => $event->instructor->id,
            'ficha_id' => $event->ficha->id,
            'error' => $exception->getMessage(),
        ]);
    }
}

