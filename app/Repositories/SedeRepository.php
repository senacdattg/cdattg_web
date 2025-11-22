<?php

namespace App\Repositories;

use App\Core\Traits\HasCache;
use App\Models\Sede;
use Illuminate\Database\Eloquent\Collection;

class SedeRepository
{
    use HasCache;


    public function __construct()
    {
        $this->cacheType = 'parametros';
        $this->cacheTags = ['sedes', 'infraestructura'];
    }    /**
     * Obtiene todas las sedes activas
     *
     * @return Collection
     */
    public function obtenerActivas(): Collection
    {
        return $this->cache('activas', function () {
            return Sede::where('status', true)
                ->with(['municipio', 'regional'])
                ->orderBy('sede')
                ->get();
        }, 720); // 12 horas
    }

    /**
     * Obtiene sedes por centro de formación
     * NOTA: Este método está obsoleto ya que la tabla sedes no tiene centro_formacion_id
     * Se mantiene por compatibilidad pero retorna todas las sedes activas
     *
     * @param int $centroId
     * @return Collection
     */
    public function obtenerPorCentro(int $centroId): Collection
    {
        // La tabla sedes no tiene centro_formacion_id, retornar todas las sedes activas
        return $this->obtenerActivas();
    }

    /**
     * Encuentra una sede con sus relaciones
     *
     * @param int $id
     * @return Sede|null
     */
    public function encontrarConRelaciones(int $id): ?Sede
    {
        return $this->cache("sede.{$id}", function () use ($id) {
            return Sede::with(['municipio.departamento', 'regional'])
                ->find($id);
        }, 720);
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

