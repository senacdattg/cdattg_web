<?php

namespace App\Repositories;

use App\Models\Aprendiz;
use App\Core\Traits\HasCache;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class AprendizRepository
{
    use HasCache;


    public function __construct()
    {
        $this->cacheType = 'aprendices';
        $this->cacheTags = ['aprendices', 'personas'];
    }    /**
     * Obtiene el aprendiz más reciente por persona con relaciones
     *
     * @param array $filtros
     * @return LengthAwarePaginator
     */
    public function obtenerAprendicesConFiltros(array $filtros = []): LengthAwarePaginator
    {
        $query = Aprendiz::with(['persona.tipoDocumento', 'fichaCaracterizacion.programaFormacion'])
            ->whereIn('id', function($subquery) {
                $subquery->select(DB::raw('MAX(id)'))
                    ->from('aprendices')
                    ->groupBy('persona_id');
            });

        // Filtro de búsqueda por nombre o documento
        if (!empty($filtros['search'])) {
            $search = $filtros['search'];
            $query->whereHas('persona', function ($q) use ($search) {
                $q->where('primer_nombre', 'LIKE', "%{$search}%")
                    ->orWhere('segundo_nombre', 'LIKE', "%{$search}%")
                    ->orWhere('primer_apellido', 'LIKE', "%{$search}%")
                    ->orWhere('segundo_apellido', 'LIKE', "%{$search}%")
                    ->orWhere('numero_documento', 'LIKE', "%{$search}%");
            });
        }

        // Filtro por ficha
        if (!empty($filtros['ficha_id'])) {
            $query->where('ficha_caracterizacion_id', $filtros['ficha_id']);
        }

        // Filtro por estado
        if (isset($filtros['estado'])) {
            $query->where('estado', $filtros['estado']);
        }

        $perPage = $filtros['per_page'] ?? 15;
        
        return $query->orderBy('id', 'desc')->paginate($perPage);
    }

    /**
     * Encuentra un aprendiz con todas sus relaciones
     *
     * @param int $id
     * @return Aprendiz
     */
    public function encontrarConRelaciones(int $id): ?Aprendiz
    {
        return Aprendiz::with([
            'persona.tipoDocumento',
            'fichaCaracterizacion.programaFormacion',
            'fichaCaracterizacion.jornadaFormacion',
            'fichaCaracterizacion.modalidadFormacion',
            'asistencias' => function ($query) {
                $query->latest()->limit(20);
            }
        ])->find($id);
    }

    /**
     * Obtiene aprendices por ficha con sus relaciones
     *
     * @param int $fichaId
     * @return Collection
     */
    public function obtenerPorFicha(int $fichaId): Collection
    {
        return Aprendiz::with(['persona.tipoDocumento'])
            ->where('ficha_caracterizacion_id', $fichaId)
            ->where('estado', true)
            ->orderBy('persona_id')
            ->get();
    }

    /**
     * Crea un nuevo aprendiz
     *
     * @param array $datos
     * @return Aprendiz
     */
    public function crear(array $datos): Aprendiz
    {
        return Aprendiz::create($datos);
    }

    /**
     * Actualiza un aprendiz existente
     *
     * @param int $id
     * @param array $datos
     * @return bool
     */
    public function actualizar(int $id, array $datos): bool
    {
        return Aprendiz::where('id', $id)->update($datos);
    }

    /**
     * Elimina un aprendiz (soft delete)
     *
     * @param int $id
     * @return bool
     */
    public function eliminar(int $id): bool
    {
        $aprendiz = Aprendiz::find($id);
        return $aprendiz ? $aprendiz->delete() : false;
    }

    /**
     * Busca aprendices por término de búsqueda
     *
     * @param string $termino
     * @param int $limite
     * @return Collection
     */
    public function buscar(string $termino, int $limite = 10): Collection
    {
        return Aprendiz::with('persona')
            ->whereHas('persona', function ($query) use ($termino) {
                $query->where('primer_nombre', 'LIKE', "%{$termino}%")
                    ->orWhere('segundo_nombre', 'LIKE', "%{$termino}%")
                    ->orWhere('primer_apellido', 'LIKE', "%{$termino}%")
                    ->orWhere('segundo_apellido', 'LIKE', "%{$termino}%")
                    ->orWhere('numero_documento', 'LIKE', "%{$termino}%");
            })
            ->limit($limite)
            ->get();
    }

    /**
     * Cuenta aprendices por ficha
     *
     * @param int $fichaId
     * @return int
     */
    public function contarPorFicha(int $fichaId): int
    {
        return Aprendiz::where('ficha_caracterizacion_id', $fichaId)
            ->where('estado', true)
            ->count();
    }

    /**
     * Verifica si una persona ya es aprendiz
     *
     * @param int $personaId
     * @return bool
     */
    public function esAprendiz(int $personaId): bool
    {
        return Aprendiz::where('persona_id', $personaId)->exists();
    }

    /**
     * Obtiene estadísticas de aprendices (con caché)
     *
     * @return array
     */
    public function obtenerEstadisticas(): array
    {
        return $this->cache('estadisticas', function () {
            return [
                'total' => Aprendiz::count(),
                'activos' => Aprendiz::where('estado', true)->count(),
                'inactivos' => Aprendiz::where('estado', false)->count(),
                'por_ficha' => Aprendiz::select('ficha_caracterizacion_id', DB::raw('count(*) as total'))
                    ->where('estado', true)
                    ->groupBy('ficha_caracterizacion_id')
                    ->get()
                    ->pluck('total', 'ficha_caracterizacion_id')
                    ->toArray(),
            ];
        }, 15); // 15 minutos
    }

    /**
     * Invalida caché al crear, actualizar o eliminar
     *
     * @return void
     */
    public function invalidarCache(): void
    {
        $this->flushCache();
    }
}

