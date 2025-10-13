<?php

namespace App\Repositories;

use App\Core\Traits\HasCache;
use App\Models\Regional;
use Illuminate\Database\Eloquent\Collection;

class RegionalRepository
{
    use HasCache;

    protected $cacheType = 'regionales';
    protected $cacheTags = ['regionales', 'configuracion'];

    /**
     * Obtiene todas las regionales activas
     *
     * @return Collection
     */
    public function obtenerActivas(): Collection
    {
        return $this->cache('activas', function () {
            return Regional::where('status', true)
                ->orderBy('nombre')
                ->get();
        }, 720); // 12 horas
    }

    /**
     * Encuentra una regional por ID
     *
     * @param int $id
     * @return Regional|null
     */
    public function encontrar(int $id): ?Regional
    {
        return $this->cache("regional.{$id}", function () use ($id) {
            return Regional::find($id);
        }, 720);
    }

    /**
     * Busca regionales por nombre
     *
     * @param string $termino
     * @return Collection
     */
    public function buscar(string $termino): Collection
    {
        return Regional::where('nombre', 'LIKE', "%{$termino}%")
            ->where('status', true)
            ->orderBy('nombre')
            ->get();
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

