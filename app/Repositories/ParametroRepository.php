<?php

namespace App\Repositories;

use App\Models\Parametro;

class ParametroRepository
{
    public function getDiasFormacion()
    {
        return Parametro::whereHas('temas', function($query) {
            $query->where('tema_id', 4);
        })->get()->toArray();
    }
}