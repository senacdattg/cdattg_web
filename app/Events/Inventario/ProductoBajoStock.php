<?php

namespace App\Events\Inventario;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

use App\Models\Inventario\Producto;

class ProductoBajoStock
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $producto;

    /**
     * Create a new event instance.
     */
    public function __construct(Producto $producto)
    {
        $this->producto = $producto;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
