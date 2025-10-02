<?php

namespace App\Events;

use App\Models\Aprendiz;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AprendizAsignadoAFicha implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $aprendiz;
    public $fichaId;

    /**
     * Create a new event instance.
     */
    public function __construct(Aprendiz $aprendiz, $fichaId)
    {
        $this->aprendiz = $aprendiz;
        $this->fichaId = $fichaId;

        Log::info('Evento AprendizAsignadoAFicha disparado', [
            'aprendiz_id' => $aprendiz->id,
            'persona_id' => $aprendiz->persona_id,
            'ficha_id' => $fichaId
        ]);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('aprendices-fichas'),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'type' => 'aprendiz_asignado_ficha',
            'data' => [
                'aprendiz' => [
                    'id' => $this->aprendiz->id,
                    'persona_id' => $this->aprendiz->persona_id,
                    'ficha_caracterizacion_id' => $this->aprendiz->ficha_caracterizacion_id,
                    'estado' => $this->aprendiz->estado,
                    'created_at' => $this->aprendiz->created_at,
                ],
                'ficha_id' => $this->fichaId,
                'persona' => $this->aprendiz->persona ? [
                    'id' => $this->aprendiz->persona->id,
                    'nombre_completo' => $this->aprendiz->persona->nombre_completo,
                    'numero_documento' => $this->aprendiz->persona->numero_documento,
                    'email' => $this->aprendiz->persona->email,
                ] : null,
                'ficha' => $this->aprendiz->fichaCaracterizacion ? [
                    'id' => $this->aprendiz->fichaCaracterizacion->id,
                    'ficha' => $this->aprendiz->fichaCaracterizacion->ficha,
                    'programa' => $this->aprendiz->fichaCaracterizacion->programaFormacion ? [
                        'nombre' => $this->aprendiz->fichaCaracterizacion->programaFormacion->nombre,
                    ] : null,
                ] : null,
            ],
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'aprendiz.asignado.ficha';
    }
}
