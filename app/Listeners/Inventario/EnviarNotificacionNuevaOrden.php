<?php

namespace App\Listeners\Inventario;

use App\Events\Inventario\NuevaOrdenCreada;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use App\Models\User;
use App\Models\Inventario\Notification;

class EnviarNotificacionNuevaOrden
{
    public function handle(NuevaOrdenCreada $event): void
    {
        $orden = $event->orden;
        $superadmins = User::whereHas('roles', function ($query) {
            $query->where('name', 'superadministrador');
        })->get();

        $tipoOrden = $orden->tipoOrden ? $orden->tipoOrden->parametro->name : 'Orden';

        foreach ($superadmins as $superadmin) {
            Notification::create([
                'user_id' => $superadmin->id,
                'message' => "📋 NUEVA {$tipoOrden}: Se ha creado una nueva orden (ID: {$orden->id}). Descripción: {$orden->descripcion_orden}",
            ]);
        }
    }
}