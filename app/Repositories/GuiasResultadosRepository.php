<?php

namespace App\Repositories;

use App\Models\GuiasResultados;
use Illuminate\Database\Eloquent\Collection;

class GuiasResultadosRepository
{
    /**
     * Obtiene guías por resultado de aprendizaje
     *
     * @param int $resultadoId
     * @return Collection
     */
    public function obtenerPorResultado(int $resultadoId): Collection
    {
        return GuiasResultados::where('resultado_aprendizaje_id', $resultadoId)
            ->with(['guiaAprendizaje'])
            ->get();
    }

    /**
     * Crea relación guía-resultado
     *
     * @param array $datos
     * @return GuiasResultados
     */
    public function crear(array $datos): GuiasResultados
    {
        return GuiasResultados::create($datos);
    }
}

