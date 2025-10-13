<?php

namespace App\Repositories;

use App\Core\Traits\HasCache;
use App\Models\RedConocimiento;
use Illuminate\Database\Eloquent\Collection;

class RedConocimientoRepository
{
    use HasCache;

    protected $cacheType = 'parametros';
    protected $cacheTags = ['redes_conocimiento', 'configuracion'];

    /**
     * Obtiene todas las redes de conocimiento activas
     *
     * @return Collection
     */
    public function obtenerActivas(): Collection
    {
        return $this->cache('activas', function () {
            return RedConocimiento::where('status', true)
                ->orderBy('nombre')
                ->get();
        }, 720); // 12 horas
    }

    /**
     * Obtiene redes por regional
     *
     * @param int $regionalId
     * @return Collection
     */
    public function obtenerPorRegional(int $regionalId): Collection
    {
        return $this->cache("regional.{$regionalId}.redes", function () use ($regionalId) {
            return RedConocimiento::where('regionals_id', $regionalId)
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

