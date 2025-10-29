<?php

namespace App\Repositories;

use App\Core\Traits\HasCache;
use App\Models\FichaCaracterizacion;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class FichaRepository
{
    use HasCache;


    public function __construct()
    {
        $this->cacheType = 'fichas';
        $this->cacheTags = ['fichas', 'caracterizacion'];
    }    /**
     * Obtiene todas las fichas activas con relaciones
     *
     * @return Collection
     */
    public function obtenerActivas(): Collection
    {
        return $this->cache('activas', function () {
            return FichaCaracterizacion::where('status', 1)
                ->with(['programaFormacion', 'jornadaFormacion', 'modalidadFormacion', 'ambiente.piso.bloque.sede'])
                ->orderBy('ficha')
                ->get();
        }, 60); // 1 hora
    }

    /**
     * Obtiene fichas con filtros y paginación
     *
     * @param array $filtros
     * @return LengthAwarePaginator
     */
    public function obtenerConFiltros(array $filtros = []): LengthAwarePaginator
    {
        $query = FichaCaracterizacion::with([
            'programaFormacion.redConocimiento',
            'jornadaFormacion',
            'modalidadFormacion',
            'ambiente.piso.bloque.sede',
            'regional'
        ]);

        if (!empty($filtros['search'])) {
            $search = $filtros['search'];
            $query->where(function ($q) use ($search) {
                $q->where('ficha', 'LIKE', "%{$search}%")
                    ->orWhereHas('programaFormacion', function ($pq) use ($search) {
                        $pq->where('nombre', 'LIKE', "%{$search}%");
                    });
            });
        }

        if (isset($filtros['status'])) {
            $query->where('status', $filtros['status']);
        }

        if (!empty($filtros['programa_id'])) {
            $query->where('programa_formacion_id', $filtros['programa_id']);
        }

        if (!empty($filtros['jornada_id'])) {
            $query->where('jornada_formacion_id', $filtros['jornada_id']);
        }

        if (!empty($filtros['regional_id'])) {
            $query->where('regional_id', $filtros['regional_id']);
        }

        $perPage = $filtros['per_page'] ?? 15;

        return $query->orderBy('fecha_inicio', 'desc')->paginate($perPage);
    }

    /**
     * Encuentra una ficha con todas sus relaciones
     *
     * @param int $id
     * @return FichaCaracterizacion|null
     */
    public function encontrarConRelaciones(int $id): ?FichaCaracterizacion
    {
        return $this->cache("ficha.{$id}", function () use ($id) {
            return FichaCaracterizacion::with([
                'programaFormacion.redConocimiento',
                'jornadaFormacion',
                'modalidadFormacion',
                'ambiente.piso.bloque.sede',
                'regional',
                'diasFormacion'
            ])->find($id);
        }, 60);
    }

    /**
     * Obtiene fichas vigentes (no finalizadas)
     *
     * @return Collection
     */
    public function obtenerVigentes(): Collection
    {
        return $this->cache('vigentes', function () {
            return FichaCaracterizacion::where('status', 1)
                ->where('fecha_fin', '>=', now())
                ->with(['programaFormacion', 'jornadaFormacion'])
                ->orderBy('fecha_inicio')
                ->get();
        }, 30);
    }

    /**
     * Obtiene fichas por programa
     *
     * @param int $programaId
     * @return Collection
     */
    public function obtenerPorPrograma(int $programaId): Collection
    {
        return $this->cache("programa.{$programaId}.fichas", function () use ($programaId) {
            return FichaCaracterizacion::where('programa_formacion_id', $programaId)
                ->where('status', 1)
                ->orderBy('ficha')
                ->get();
        }, 60);
    }

    /**
     * Crea una nueva ficha
     *
     * @param array $datos
     * @return FichaCaracterizacion
     */
    public function crear(array $datos): FichaCaracterizacion
    {
        $ficha = FichaCaracterizacion::create($datos);
        $this->invalidarCache();
        return $ficha;
    }

    /**
     * Actualiza una ficha
     *
     * @param int $id
     * @param array $datos
     * @return bool
     */
    public function actualizar(int $id, array $datos): bool
    {
        $actualizado = FichaCaracterizacion::where('id', $id)->update($datos);
        $this->invalidarCache();
        return $actualizado;
    }

    /**
     * Elimina una ficha
     *
     * @param int $id
     * @return bool
     */
    public function eliminar(int $id): bool
    {
        $eliminado = FichaCaracterizacion::where('id', $id)->delete();
        $this->invalidarCache();
        return $eliminado;
    }

    /**
     * Obtiene estadísticas de fichas
     *
     * @return array
     */
    public function obtenerEstadisticas(): array
    {
        return $this->cache('estadisticas', function () {
            return [
                'total' => FichaCaracterizacion::count(),
                'activas' => FichaCaracterizacion::where('status', 1)->count(),
                'vigentes' => FichaCaracterizacion::where('status', 1)
                    ->where('fecha_fin', '>=', now())
                    ->count(),
                'finalizadas' => FichaCaracterizacion::where('fecha_fin', '<', now())->count(),
            ];
        }, 15);
    }

    /**
     * Invalida toda la caché
     *
     * @return void
     */
    public function invalidarCache(): void
    {
        $this->flushCache();
    }
}

