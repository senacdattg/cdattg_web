<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NuevaAsistenciaRegistrada implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $asistenciaData;

    /**
     * Create a new event instance.
     */
    public function __construct($asistenciaData)
    {
        $this->asistenciaData = $asistenciaData;
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
            'id' => $this->asistenciaData['id'],
            'aprendiz' => $this->asistenciaData['aprendiz'],
            'estado' => $this->asistenciaData['estado'],
            'timestamp' => $this->asistenciaData['timestamp'],
            'tipo' => 'nueva_asistencia',
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'NuevaAsistenciaRegistrada';
    }
}
