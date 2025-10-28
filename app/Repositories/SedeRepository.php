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
                ->with(['centroFormacion', 'municipio'])
                ->orderBy('nombre')
                ->get();
        }, 720); // 12 horas
    }

    /**
     * Obtiene sedes por centro de formación
     *
     * @param int $centroId
     * @return Collection
     */
    public function obtenerPorCentro(int $centroId): Collection
    {
        return $this->cache("centro.{$centroId}.sedes", function () use ($centroId) {
            return Sede::where('centro_formacion_id', $centroId)
                ->where('status', true)
                ->orderBy('nombre')
                ->get();
        }, 720);
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
            return Sede::with(['centroFormacion', 'municipio.departamento', 'ambientes', 'pisos'])
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

