<?php

namespace App\Repositories;

use App\Core\Traits\HasCache;
use App\Models\GuiasAprendizaje;
use Illuminate\Database\Eloquent\Collection;

class GuiasAprendizajeRepository
{
    use HasCache;

    protected $cacheType = 'programas';
    protected $cacheTags = ['guias', 'aprendizaje'];

    /**
     * Obtiene guías por programa
     *
     * @param int $programaId
     * @return Collection
     */
    public function obtenerPorPrograma(int $programaId): Collection
    {
        return $this->cache("programa.{$programaId}.guias", function () use ($programaId) {
            return GuiasAprendizaje::where('programa_formacion_id', $programaId)
                ->with(['resultadosAprendizaje'])
                ->orderBy('numero_guia')
                ->get();
        }, 360); // 6 horas
    }

    /**
     * Obtiene guías activas
     *
     * @return Collection
     */
    public function obtenerActivas(): Collection
    {
        return $this->cache('activas', function () {
            return GuiasAprendizaje::where('status', true)
                ->with(['programaFormacion'])
                ->orderBy('numero_guia')
                ->get();
        }, 360);
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

