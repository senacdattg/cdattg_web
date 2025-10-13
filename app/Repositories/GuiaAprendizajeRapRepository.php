<?php

namespace App\Repositories;

use App\Models\GuiaAprendizajeRap;
use Illuminate\Database\Eloquent\Collection;

class GuiaAprendizajeRapRepository
{
    /**
     * Obtiene guías por resultado de aprendizaje
     *
     * @param int $rapId
     * @return Collection
     */
    public function obtenerPorRap(int $rapId): Collection
    {
        return GuiaAprendizajeRap::where('resultado_aprendizaje_id', $rapId)
            ->with(['guiaAprendizaje'])
            ->get();
    }

    /**
     * Obtiene guías por guía de aprendizaje
     *
     * @param int $guiaId
     * @return Collection
     */
    public function obtenerPorGuia(int $guiaId): Collection
    {
        return GuiaAprendizajeRap::where('guia_aprendizaje_id', $guiaId)
            ->with(['resultadoAprendizaje'])
            ->get();
    }

    /**
     * Crea relación guía-RAP
     *
     * @param array $datos
     * @return GuiaAprendizajeRap
     */
    public function crear(array $datos): GuiaAprendizajeRap
    {
        return GuiaAprendizajeRap::create($datos);
    }
}

