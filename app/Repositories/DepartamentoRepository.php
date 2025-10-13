<?php

namespace App\Repositories;

use App\Core\Traits\HasCache;
use App\Models\Departamento;
use Illuminate\Database\Eloquent\Collection;

class DepartamentoRepository
{
    use HasCache;

    protected $cacheType = 'parametros';
    protected $cacheTags = ['departamentos', 'ubicacion'];

    /**
     * Obtiene todos los departamentos
     *
     * @return Collection
     */
    public function obtenerTodos(): Collection
    {
        return $this->cache('todos', function () {
            return Departamento::orderBy('nombre')->get();
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
            return Departamento::where('pais_id', $paisId)
                ->orderBy('nombre')
                ->get();
        }, 1440);
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

