<?php

namespace App\Repositories;

use App\Models\AprendizFicha;
use Illuminate\Database\Eloquent\Collection;

class AprendizFichaRepository
{
    /**
     * Obtiene aprendices por ficha
     *
     * @param int $fichaId
     * @return Collection
     */
    public function obtenerPorFicha(int $fichaId): Collection
    {
        return AprendizFicha::where('ficha_caracterizacion_id', $fichaId)
            ->with(['aprendiz.persona'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Obtiene fichas por aprendiz
     *
     * @param int $aprendizId
     * @return Collection
     */
    public function obtenerPorAprendiz(int $aprendizId): Collection
    {
        return AprendizFicha::where('aprendiz_id', $aprendizId)
            ->with(['ficha.programaFormacion'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Crea asignación de aprendiz a ficha
     *
     * @param array $datos
     * @return AprendizFicha
     */
    public function crear(array $datos): AprendizFicha
    {
        return AprendizFicha::create($datos);
    }

    /**
     * Elimina asignación
     *
     * @param int $id
     * @return bool
     */
    public function eliminar(int $id): bool
    {
        return AprendizFicha::where('id', $id)->delete();
    }

    /**
     * Verifica si aprendiz está en ficha
     *
     * @param int $aprendizId
     * @param int $fichaId
     * @return bool
     */
    public function estaEnFicha(int $aprendizId, int $fichaId): bool
    {
        return AprendizFicha::where('aprendiz_id', $aprendizId)
            ->where('ficha_caracterizacion_id', $fichaId)
            ->exists();
    }
}

