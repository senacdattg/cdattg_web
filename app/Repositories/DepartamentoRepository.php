<?php

namespace App\Repositories;

use App\Core\Traits\HasCache;
use App\Models\Departamento;
use Illuminate\Database\Eloquent\Collection;

class DepartamentoRepository
{
    use HasCache;


    public function __construct()
    {
        $this->cacheType = 'parametros';
        $this->cacheTags = ['departamentos', 'ubicacion'];
    }    /**
     * Obtiene todos los departamentos
     *
     * @return Collection
     */
    public function obtenerTodos(): Collection
    {
        return $this->cache('todos', function () {
            return Departamento::select(['id', 'departamento as nombre', 'pais_id', 'status'])
                ->orderBy('departamento')
                ->get();
        }, 1440); // 24 horas
    }

    /**
     * Obtiene departamentos por país
     *
     * @param int $paisId
     * @return Collection
     */
    public function obtenerPorPais(int $paisId): Collection
    {
        return $this->cache("pais.{$paisId}.departamentos", function () use ($paisId) {
            return Departamento::select(['id', 'departamento as nombre', 'pais_id'])
                ->where('pais_id', $paisId)
                ->orderBy('departamento')
                ->get();
        }, 1440);
    }

    /**
     * Busca departamentos por nombre
     *
     * @param string $termino
     * @param int|null $paisId
     * @return Collection
     */
    public function buscar(string $termino, ?int $paisId = null): Collection
    {
        return Departamento::select(['id', 'departamento as nombre', 'pais_id'])
            ->when($paisId, fn ($query) => $query->where('pais_id', $paisId))
            ->where('departamento', 'LIKE', "%{$termino}%")
            ->orderBy('departamento')
            ->limit(20)
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

