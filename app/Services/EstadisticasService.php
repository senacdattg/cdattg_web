<?php

namespace App\Services;

use App\Repositories\AprendizRepository;
use App\Repositories\FichaRepository;
use App\Repositories\AsistenciaAprendizRepository;
use App\Core\Traits\HasCache;
use Illuminate\Support\Facades\DB;

class EstadisticasService
{
    use HasCache;

    protected AprendizRepository $aprendizRepo;
    protected FichaRepository $fichaRepo;
    protected AsistenciaAprendizRepository $asistenciaRepo;

    public function __construct(
        AprendizRepository $aprendizRepo,
        FichaRepository $fichaRepo,
        AsistenciaAprendizRepository $asistenciaRepo
    ) {
        $this->aprendizRepo = $aprendizRepo;
        $this->fichaRepo = $fichaRepo;
        $this->asistenciaRepo = $asistenciaRepo;
        $this->cacheType = 'estadisticas';
    }

    /**
     * Obtiene dashboard general del sistema
     *
     * @return array
     */
    public function obtenerDashboardGeneral(): array
    {
        return $this->cache('dashboard.general', function () {
            return [
                'aprendices' => $this->aprendizRepo->obtenerEstadisticas(),
                'fichas' => $this->fichaRepo->obtenerEstadisticas(),
                'instructores' => \App\Models\Instructor::count(),
                'asistencias_hoy' => \App\Models\AsistenciaAprendiz::whereDate('created_at', today())->count(),
            ];
        }, 15); // 15 minutos
    }

    /**
     * Obtiene estadísticas de asistencia por ficha
     *
     * @param int $fichaId
     * @param string|null $fechaInicio
     * @param string|null $fechaFin
     * @return array
     */
    public function obtenerEstadisticasAsistencia(int $fichaId, ?string $fechaInicio = null, ?string $fechaFin = null): array
    {
        $cacheKey = $this->cacheKey('asistencia', $fichaId, $fechaInicio, $fechaFin);

        return $this->cache($cacheKey, function () use ($fichaId, $fechaInicio, $fechaFin) {
            $estadisticas = $this->asistenciaRepo->obtenerEstadisticas($fichaId, $fechaInicio, $fechaFin);
            
            $totalAprendices = $this->aprendizRepo->contarPorFicha($fichaId);
            
            return [
                ...$estadisticas,
                'total_aprendices_ficha' => $totalAprendices,
                'porcentaje_asistencia' => $totalAprendices > 0 
                    ? round(($estadisticas['aprendices_unicos'] / $totalAprendices) * 100, 2)
                    : 0,
            ];
        }, 10); // 10 minutos
    }

    /**
     * Obtiene top fichas con mejor asistencia
     *
     * @param int $limite
     * @return array
     */
    public function obtenerTopFichasAsistencia(int $limite = 10): array
    {
        return $this->cache("top_fichas.{$limite}", function () use ($limite) {
            return DB::table('asistencia_aprendices as aa')
                ->join('ficha_caracterizacions as fc', 'aa.caracterizacion_id', '=', 'fc.id')
                ->select('fc.id', 'fc.ficha', DB::raw('COUNT(DISTINCT aa.numero_identificacion) as total_aprendices'))
                ->groupBy('fc.id', 'fc.ficha')
                ->orderBy('total_aprendices', 'desc')
                ->limit($limite)
                ->get()
                ->toArray();
        }, 30); // 30 minutos
    }

    /**
     * Obtiene tendencias mensuales
     *
     * @param int $meses
     * @return array
     */
    public function obtenerTendenciasMensuales(int $meses = 6): array
    {
        return $this->cache("tendencias.{$meses}", function () use ($meses) {
            $tendencias = [];

            for ($i = 0; $i < $meses; $i++) {
                $fecha = now()->subMonths($i);
                $mesAnio = $fecha->format('Y-m');

                $tendencias[] = [
                    'mes' => $fecha->format('M Y'),
                    'aprendices_nuevos' => \App\Models\Aprendiz::whereYear('created_at', $fecha->year)
                        ->whereMonth('created_at', $fecha->month)
                        ->count(),
                    'asistencias' => \App\Models\AsistenciaAprendiz::whereYear('created_at', $fecha->year)
                        ->whereMonth('created_at', $fecha->month)
                        ->count(),
                ];
            }

            return array_reverse($tendencias);
        }, 60); // 1 hora
    }
}

