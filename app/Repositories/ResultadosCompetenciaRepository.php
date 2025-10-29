<?php

namespace App\Repositories;

use App\Core\Traits\HasCache;
use App\Models\ResultadosCompetencia;
use Illuminate\Database\Eloquent\Collection;

class ResultadosCompetenciaRepository
{
    use HasCache;


    public function __construct()
    {
        $this->cacheType = 'programas';
        $this->cacheTags = ['resultados_competencia', 'competencias'];
    }    /**
     * Obtiene resultados por competencia
     *
     * @param int $competenciaId
     * @return Collection
     */
    public function obtenerPorCompetencia(int $competenciaId): Collection
    {
        return $this->cache("competencia.{$competenciaId}.resultados", function () use ($competenciaId) {
            return ResultadosCompetencia::where('competencia_id', $competenciaId)
                ->with(['resultadoAprendizaje'])
                ->orderBy('orden')
                ->get();
        }, 360); // 6 horas
    }

    /**
     * Invalida caché
     *
     * @return void
     */
    public function invalidarCache(): void
    {
        $this->flushCache();
    }
}

