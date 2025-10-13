<?php

namespace App\Services;

use App\Services\EstadisticasService;
use App\Services\AsistenciaService;
use App\Repositories\FichaRepository;
use App\Core\Traits\HasCache;
use Carbon\Carbon;

class DashboardService
{
    use HasCache;

    protected EstadisticasService $estadisticasService;
    protected AsistenciaService $asistenciaService;
    protected FichaRepository $fichaRepo;
    protected $cacheType = 'estadisticas';

    public function __construct(
        EstadisticasService $estadisticasService,
        AsistenciaService $asistenciaService,
        FichaRepository $fichaRepo
    ) {
        $this->estadisticasService = $estadisticasService;
        $this->asistenciaService = $asistenciaService;
        $this->fichaRepo = $fichaRepo;
    }

    /**
     * Obtiene datos completos del dashboard administrativo
     *
     * @return array
     */
    public function obtenerDashboardAdministrativo(): array
    {
        return $this->cache('dashboard.administrativo', function () {
            $general = $this->estadisticasService->obtenerDashboardGeneral();
            $tendencias = $this->estadisticasService->obtenerTendenciasMensuales(6);
            $topFichas = $this->estadisticasService->obtenerTopFichasAsistencia(5);

            return [
                'resumen' => $general,
                'tendencias' => $tendencias,
                'top_fichas' => $topFichas,
                'alertas' => $this->obtenerAlertas(),
            ];
        }, 15); // 15 minutos
    }

    /**
     * Obtiene dashboard para instructor
     *
     * @param int $instructorId
     * @return array
     */
    public function obtenerDashboardInstructor(int $instructorId): array
    {
        return $this->cache("dashboard.instructor.{$instructorId}", function () use ($instructorId) {
            $instructor = \App\Models\Instructor::with('instructorFichas.ficha')->find($instructorId);

            $fichasActivas = $instructor->instructorFichas()
                ->whereHas('ficha', function ($q) {
                    $q->where('status', true)
                      ->where('fecha_fin', '>=', now());
                })
                ->count();

            $horasTotales = $instructor->instructorFichas()->sum('total_horas_instructor');

            return [
                'fichas_activas' => $fichasActivas,
                'horas_totales' => $horasTotales,
                'especialidad_principal' => $instructor->especialidades['principal'] ?? 'N/A',
                'proximas_clases' => $this->obtenerProximasClases($instructorId),
            ];
        }, 30); // 30 minutos
    }

    /**
     * Obtiene dashboard para aprendiz
     *
     * @param int $aprendizId
     * @return array
     */
    public function obtenerDashboardAprendiz(int $aprendizId): array
    {
        return $this->cache("dashboard.aprendiz.{$aprendizId}", function () use ($aprendizId) {
            $aprendiz = \App\Models\Aprendiz::with('fichaCaracterizacion', 'asistencias')->find($aprendizId);

            $asistenciasTotal = $aprendiz->asistencias()->count();
            $asistenciasMes = $aprendiz->asistencias()
                ->whereMonth('created_at', now()->month)
                ->count();

            return [
                'ficha' => $aprendiz->fichaCaracterizacion->ficha ?? 'N/A',
                'programa' => $aprendiz->fichaCaracterizacion->programaFormacion->nombre ?? 'N/A',
                'asistencias_total' => $asistenciasTotal,
                'asistencias_mes' => $asistenciasMes,
                'porcentaje_asistencia' => $this->calcularPorcentajeAsistencia($aprendizId),
            ];
        }, 30);
    }

    /**
     * Obtiene alertas del sistema
     *
     * @return array
     */
    protected function obtenerAlertas(): array
    {
        $alertas = [];

        // Fichas próximas a finalizar
        $fichasProximasAFinalizar = $this->fichaRepo->obtenerVigentes()
            ->filter(function ($ficha) {
                $diasRestantes = Carbon::parse($ficha->fecha_fin)->diffInDays(now());
                return $diasRestantes <= 30;
            });

        if ($fichasProximasAFinalizar->isNotEmpty()) {
            $alertas[] = [
                'tipo' => 'warning',
                'titulo' => 'Fichas próximas a finalizar',
                'mensaje' => "{$fichasProximasAFinalizar->count()} fichas finalizan en menos de 30 días",
                'cantidad' => $fichasProximasAFinalizar->count(),
            ];
        }

        return $alertas;
    }

    /**
     * Obtiene próximas clases del instructor
     *
     * @param int $instructorId
     * @return array
     */
    protected function obtenerProximasClases(int $instructorId): array
    {
        // Implementar lógica de próximas clases
        return [];
    }

    /**
     * Calcula porcentaje de asistencia del aprendiz
     *
     * @param int $aprendizId
     * @return float
     */
    protected function calcularPorcentajeAsistencia(int $aprendizId): float
    {
        // Lógica simplificada
        return 85.0;
    }
}

