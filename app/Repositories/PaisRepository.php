<?php

namespace App\Repositories;

use App\Core\Traits\HasCache;
use App\Models\Pais;
use Illuminate\Database\Eloquent\Collection;

class PaisRepository
{
    use HasCache;


    public function __construct()
    {
        $this->cacheType = 'parametros';
        $this->cacheTags = ['paises', 'ubicacion'];
    }    /**
     * Obtiene todos los países
     *
     * @return Collection
     */
    public function obtenerTodos(): Collection
    {
        return $this->cacheWithTags('todos', function () {
            return Pais::select(['id', 'pais', 'status'])
                ->where('status', 1)
                ->orderBy('pais')
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

