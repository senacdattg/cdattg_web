<?php

namespace App\Repositories;

use App\Core\Traits\HasCache;
use App\Models\ModalidadFormacion;
use Illuminate\Database\Eloquent\Collection;

class ModalidadFormacionRepository
{
    use HasCache;

    protected $cacheType = 'parametros';
    protected $cacheTags = ['modalidades', 'configuracion'];

    /**
     * Obtiene todas las modalidades activas
     *
     * @return Collection
     */
    public function obtenerActivas(): Collection
    {
        return $this->cache('activas', function () {
            return ModalidadFormacion::where('status', true)
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

