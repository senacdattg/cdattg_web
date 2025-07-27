<?php

namespace App\Repositories;

use App\Models\Competencia;
use Carbon\Carbon;
use App\Models\FichaCaracterizacion;

class CompetenciaRepository
{
    /**
     * Obtiene las competencias que cumplen con los criterios de fecha:
     * 1. Fecha de inicio posterior a hoy, O
     * 2. Fecha de fin no ha pasado (incluyendo hoy)
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getCompetenciasVigentes()
    {
        $hoy = Carbon::now()->startOfDay();
        
        return Competencia::where(function($query) use ($hoy) {
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
     * Obtiene la competencia actual basada en las fechas
     * 
     * @return Competencia|null
     */
    public function getCompetenciaActual(FichaCaracterizacion $fichaCaracterizacion)
    {

        $hoy = Carbon::now();

        $programaFormacion = $fichaCaracterizacion->programaFormacion;

        $competenciaActual = $programaFormacion->competenciaActual();
        
        return $competenciaActual->where('fecha_inicio', '<=', $hoy)
            ->where(function($query) use ($hoy) {
                $query->where('fecha_fin', '>=', $hoy)
                      ->orWhereNull('fecha_fin');
            })
            ->first();
    }

    /**
     * Obtiene las competencias que inician después de hoy
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getProximasCompetencias()
    {
        return Competencia::where('fecha_inicio', '>', Carbon::now())
            ->orderBy('fecha_inicio')
            ->get();
    }
}
