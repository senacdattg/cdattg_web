<?php

namespace App\Repositories;

use App\Models\Evidencias;

class EvidenciasRepository
{
    public function getUltimaEvidencia($idGuiaAprendizaje)
    {
        return Evidencias::whereHas('guiasAprendizaje', function ($query) use ($idGuiaAprendizaje) {
            $query->where('guia_aprendizajes.id', $idGuiaAprendizaje);
        })->latest()->first();
    }
}
