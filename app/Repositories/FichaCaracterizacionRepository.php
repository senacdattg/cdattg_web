<?php

namespace App\Repositories;

use App\Models\FichaCaracterizacion;

class FichaCaracterizacionRepository
{
    public function getFichaCaracterizacion($fichaCaracterizacionId)
    {
        return FichaCaracterizacion::where('id', $fichaCaracterizacionId)->first();
    }
}
