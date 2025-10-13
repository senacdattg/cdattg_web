<?php

namespace App\Repositories;

use App\Core\Traits\HasCache;
use App\Models\Pais;
use Illuminate\Database\Eloquent\Collection;

class PaisRepository
{
    use HasCache;

    protected $cacheType = 'parametros';
    protected $cacheTags = ['paises', 'ubicacion'];

    /**
     * Obtiene todos los países
     *
     * @return Collection
     */
    public function obtenerTodos(): Collection
    {
        return $this->cache('todos', function () {
            return Pais::orderBy('nombre')->get();
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

