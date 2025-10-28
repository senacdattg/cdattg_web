<?php

namespace App\Repositories;

use App\Core\Traits\HasCache;
use App\Models\CentroFormacion;
use Illuminate\Database\Eloquent\Collection;

class CentroFormacionRepository
{
    use HasCache;


    public function __construct()
    {
        $this->cacheType = 'parametros';
        $this->cacheTags = ['centros', 'infraestructura'];
    }    /**
     * Obtiene todos los centros activos
     *
     * @return Collection
     */
    public function obtenerActivos(): Collection
    {
        return $this->cache('activos', function () {
            return CentroFormacion::where('status', true)
                ->with(['regional', 'sedes'])
                ->orderBy('nombre')
                ->get();
        }, 720); // 12 horas
    }

    /**
     * Obtiene centros por regional
     *
     * @param int $regionalId
     * @return Collection
     */
    public function obtenerPorRegional(int $regionalId): Collection
    {
        return $this->cache("regional.{$regionalId}.centros", function () use ($regionalId) {
            return CentroFormacion::where('regional_id', $regionalId)
                ->where('status', true)
                ->orderBy('nombre')
                ->get();
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

