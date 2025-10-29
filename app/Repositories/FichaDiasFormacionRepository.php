<?php

namespace App\Repositories;

use App\Models\FichaDiasFormacion;
use Illuminate\Database\Eloquent\Collection;

class FichaDiasFormacionRepository
{
    /**
     * Obtiene días de formación por ficha
     *
     * @param int $fichaId
     * @return Collection
     */
    public function obtenerPorFicha(int $fichaId): Collection
    {
        return FichaDiasFormacion::where('ficha_caracterizacion_id', $fichaId)
            ->with(['diaFormacion'])
            ->orderBy('dia_formacion_id')
            ->get();
    }

    /**
     * Crea relación ficha-día
     *
     * @param array $datos
     * @return FichaDiasFormacion
     */
    public function crear(array $datos): FichaDiasFormacion
    {
        return FichaDiasFormacion::create($datos);
    }

    /**
     * Elimina días de una ficha
     *
     * @param int $fichaId
     * @return bool
     */
    public function eliminarPorFicha(int $fichaId): bool
    {
        return FichaDiasFormacion::where('ficha_caracterizacion_id', $fichaId)->delete();
    }

    /**
     * Asigna múltiples días a una ficha
     *
     * @param int $fichaId
     * @param array $diasIds
     * @return int
     */
    public function asignarDias(int $fichaId, array $diasIds): int
    {
        $registros = array_map(function ($diaId) use ($fichaId) {
            return [
                'ficha_caracterizacion_id' => $fichaId,
                'dia_formacion_id' => $diaId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, $diasIds);

        FichaDiasFormacion::insert($registros);

        return count($registros);
    }
}

