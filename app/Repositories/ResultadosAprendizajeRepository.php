<?php

namespace App\Repositories;

use App\Models\ResultadosAprendizaje;
use Carbon\Carbon;

class ResultadosAprendizajeRepository
{
    /**
     * Obtiene los resultados de aprendizaje que cumplen con los criterios de fecha:
     * 1. Fecha de inicio posterior a hoy, O
     * 2. Fecha de fin no ha pasado (incluyendo hoy)
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getResultadosVigentes()
    {
        $hoy = Carbon::now()->startOfDay();
        
        return ResultadosAprendizaje::where(function($query) use ($hoy) {
            // Fecha de inicio después de hoy
            $query->where('fecha_inicio', '>', $hoy)
                // O fecha de fin es nula o mayor o igual a hoy
                ->orWhere(function($q) use ($hoy) {
                    $q->where('fecha_fin', '>=', $hoy)
                      ->orWhereNull('fecha_fin');
                });
        })->get();
    }

    
    /**
     * Obtiene los resultados de aprendizaje de una competencia
     *
     * @param int $competenciaId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getResultadosAprendizajePorCompetencia($competenciaId)
    {
        return ResultadosAprendizaje::whereHas('competencia', function($query) use ($competenciaId) {
            $query->where('competencias.id', $competenciaId);
        })->get();
    }
    

    /**
     * Obtiene los resultados de aprendizaje por ID de guía de aprendizaje
     * que cumplen con los criterios de fecha
     *
     * @param int $guiaAprendizajeId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getResultadosVigentesPorGuia($guiaAprendizajeId)
    {
        $hoy = Carbon::now()->startOfDay();
        
        return ResultadosAprendizaje::whereHas('guiasAprendizaje', function($query) use ($guiaAprendizajeId) {
            $query->where('guia_aprendizaje_id', $guiaAprendizajeId);
        })->where(function($query) use ($hoy) {
            $query->where('fecha_inicio', '>', $hoy)
                ->orWhere(function($q) use ($hoy) {
                    $q->where('fecha_fin', '>=', $hoy)
                      ->orWhereNull('fecha_fin');
                });
        })->get();
    }
}