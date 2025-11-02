<?php

namespace App\Events\Inventario;

use App\Models\Inventario\Orden;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NuevaOrdenCreada
{
    use Dispatchable, SerializesModels;

    public $orden;

    public function __construct(Orden $orden)
    {
        $this->orden = $orden;
    }
}