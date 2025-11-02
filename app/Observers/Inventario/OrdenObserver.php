<?php

namespace App\Observers\Inventario;

use App\Models\Inventario\Orden;

class OrdenObserver
{
    public function created(Orden $orden): void
    {
        event(new \App\Events\Inventario\NuevaOrdenCreada($orden));
    }

    public function updated(Orden $orden): void
    {
        //
    }

    public function deleted(Orden $orden): void
    {
        //
    }

    public function restored(Orden $orden): void
    {
        //
    }

    public function forceDeleted(Orden $orden): void
    {
        //
    }
}