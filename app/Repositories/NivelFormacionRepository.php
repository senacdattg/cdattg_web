<?php

namespace App\Repositories;

use App\Core\Traits\HasCache;
use App\Models\NivelFormacion;
use Illuminate\Database\Eloquent\Collection;

class NivelFormacionRepository
{
    use HasCache;

    protected $cacheType = 'parametros';
    protected $cacheTags = ['niveles', 'configuracion'];

    /**
     * Obtiene todos los niveles de formación activos
     *
     * @return Collection
     */
    public function obtenerActivos(): Collection
    {
        return $this->cache('activos', function () {
            return NivelFormacion::where('status', true)
                ->orderBy('nombre')
                ->get();
        }, 1440); // 24 horas
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

