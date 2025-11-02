<?php

namespace App\Listeners\Inventario;

use App\Events\Inventario\ProductoBajoStock;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use App\Models\User;
use App\Models\Inventario\Notification;

class EnviarNotificacionProductoBajoStock
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ProductoBajoStock $event): void
    {
        $producto = $event->producto;
        $superadmins = User::whereHas('roles', function ($query) {
            $query->where('name', 'superadministrador');
        })->get();

        $estado = $producto->cantidad <= 5 ? 'CRÍTICO' : 'BAJO';

        foreach ($superadmins as $superadmin) {
            Notification::create([
                'user_id' => $superadmin->id,
                'message' => "🚨 ALERTA DE STOCK {$estado}: El producto '{$producto->producto}' tiene stock {$estado} (Cantidad: {$producto->cantidad}). Requiere atención inmediata.",
            ]);
        }
    }
}
