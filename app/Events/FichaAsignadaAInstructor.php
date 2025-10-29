<?php

namespace App\Events;

use App\Models\Instructor;
use App\Models\FichaCaracterizacion;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FichaAsignadaAInstructor implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Instructor $instructor;
    public FichaCaracterizacion $ficha;
    public array $detalles;

    /**
     * Create a new event instance.
     */
    public function __construct(Instructor $instructor, FichaCaracterizacion $ficha, array $detalles = [])
    {
        $this->instructor = $instructor;
        $this->ficha = $ficha;
        $this->detalles = $detalles;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('instructores.' . $this->instructor->id),
            new Channel('fichas.' . $this->ficha->id),
        ];
    }

    /**
     * Datos que se enviarán en el broadcast
     *
     * @return array
     */
    public function broadcastWith(): array
    {
        return [
            'instructor' => [
                'id' => $this->instructor->id,
                'nombre' => $this->instructor->persona->nombre_completo ?? 'N/A',
            ],
            'ficha' => [
                'id' => $this->ficha->id,
                'numero' => $this->ficha->ficha,
                'programa' => $this->ficha->programaFormacion->nombre ?? 'N/A',
            ],
            'detalles' => $this->detalles,
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Nombre del evento en el broadcast
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'ficha.asignada';
    }
}

