<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\AsistenciaAprendiz;

class AsistenciaCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $asistencia;

    /**
     * Create a new event instance.
     */
    public function __construct(AsistenciaAprendiz $asistencia)
    {
        $this->asistencia = $asistencia;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('asistencias'),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'type' => 'asistencia_created',
            'data' => [
                'id' => $this->asistencia->id,
                'instructor_ficha_id' => $this->asistencia->instructor_ficha_id,
                'aprendiz_ficha_id' => $this->asistencia->aprendiz_ficha_id,
                'hora_ingreso' => $this->asistencia->hora_ingreso,
                'hora_salida' => $this->asistencia->hora_salida,
                'created_at' => $this->asistencia->created_at,
                'updated_at' => $this->asistencia->updated_at,
                // Información del aprendiz si está disponible
                'aprendiz' => $this->asistencia->aprendizFicha ? [
                    'id' => $this->asistencia->aprendizFicha->aprendiz->id,
                    'persona' => [
                        'nombre_completo' => $this->asistencia->aprendizFicha->aprendiz->persona->getNombreCompletoAttribute(),
                        'numero_documento' => $this->asistencia->aprendizFicha->aprendiz->persona->numero_documento,
                    ]
                ] : null,
                // Información de la ficha si está disponible
                'ficha' => $this->asistencia->aprendizFicha ? [
                    'id' => $this->asistencia->aprendizFicha->ficha->id,
                    'numero_ficha' => $this->asistencia->aprendizFicha->ficha->ficha,
                ] : null,
            ],
            'timestamp' => now()->toISOString(),
        ];
    }
}
