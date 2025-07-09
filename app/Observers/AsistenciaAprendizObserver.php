<?php

namespace App\Observers;

use App\Models\AsistenciaAprendiz;
use App\Events\AsistenciaCreated;

class AsistenciaAprendizObserver
{
    /**
     * Handle the AsistenciaAprendiz "created" event.
     */
    public function created(AsistenciaAprendiz $asistenciaAprendiz): void
    {
        // Disparar evento de WebSocket cuando se crea una nueva asistencia
        event(new AsistenciaCreated($asistenciaAprendiz));
    }

    /**
     * Handle the AsistenciaAprendiz "updated" event.
     */
    public function updated(AsistenciaAprendiz $asistenciaAprendiz): void
    {
        // Si se actualiza la hora de salida, también notificar
        if ($asistenciaAprendiz->wasChanged('hora_salida')) {
            event(new AsistenciaCreated($asistenciaAprendiz));
        }
    }

    /**
     * Handle the AsistenciaAprendiz "deleted" event.
     */
    public function deleted(AsistenciaAprendiz $asistenciaAprendiz): void
    {
        //
    }

    /**
     * Handle the AsistenciaAprendiz "restored" event.
     */
    public function restored(AsistenciaAprendiz $asistenciaAprendiz): void
    {
        //
    }

    /**
     * Handle the AsistenciaAprendiz "force deleted" event.
     */
    public function forceDeleted(AsistenciaAprendiz $asistenciaAprendiz): void
    {
        //
    }
}
