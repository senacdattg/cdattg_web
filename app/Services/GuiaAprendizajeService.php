<?php

namespace App\Services;

use App\Repositories\GuiasAprendizajeRepository;
use App\Repositories\EvidenciasRepository;
use App\Repositories\EvidenciaGuiaAprendizajeRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GuiaAprendizajeService
{
    protected GuiasAprendizajeRepository $guiasRepo;
    protected EvidenciasRepository $evidenciasRepo;
    protected EvidenciaGuiaAprendizajeRepository $evidenciaGuiaRepo;

    public function __construct(
        GuiasAprendizajeRepository $guiasRepo,
        EvidenciasRepository $evidenciasRepo,
        EvidenciaGuiaAprendizajeRepository $evidenciaGuiaRepo
    ) {
        $this->guiasRepo = $guiasRepo;
        $this->evidenciasRepo = $evidenciasRepo;
        $this->evidenciaGuiaRepo = $evidenciaGuiaRepo;
    }

    /**
     * Obtiene guías por programa
     *
     * @param int $programaId
     * @return Collection
     */
    public function obtenerPorPrograma(int $programaId): Collection
    {
        return $this->guiasRepo->obtenerPorPrograma($programaId);
    }

    /**
     * Registra evidencia de aprendiz en guía
     *
     * @param int $guiaId
     * @param int $aprendizId
     * @param array $datosEvidencia
     * @return bool
     */
    public function registrarEvidencia(int $guiaId, int $aprendizId, array $datosEvidencia): bool
    {
        return DB::transaction(function () use ($guiaId, $aprendizId, $datosEvidencia) {
            $evidencia = $this->evidenciaGuiaRepo->crear([
                'guia_aprendizaje_id' => $guiaId,
                'aprendiz_id' => $aprendizId,
                'evidencia_id' => $datosEvidencia['evidencia_id'] ?? null,
                'descripcion' => $datosEvidencia['descripcion'] ?? null,
                'archivo' => $datosEvidencia['archivo'] ?? null,
            ]);

            Log::info('Evidencia registrada', [
                'guia_id' => $guiaId,
                'aprendiz_id' => $aprendizId,
                'evidencia_id' => $evidencia->id,
            ]);

            return true;
        });
    }

    /**
     * Califica evidencia de aprendiz
     *
     * @param int $evidenciaGuiaId
     * @param float $calificacion
     * @param string|null $observaciones
     * @return bool
     */
    public function calificarEvidencia(int $evidenciaGuiaId, float $calificacion, ?string $observaciones = null): bool
    {
        return DB::transaction(function () use ($evidenciaGuiaId, $calificacion, $observaciones) {
            $calificado = $this->evidenciaGuiaRepo->calificar($evidenciaGuiaId, $calificacion, $observaciones);

            if ($calificado) {
                Log::info('Evidencia calificada', [
                    'evidencia_guia_id' => $evidenciaGuiaId,
                    'calificacion' => $calificacion,
                ]);
            }

            return $calificado;
        });
    }

    /**
     * Obtiene progreso de aprendiz en guías
     *
     * @param int $aprendizId
     * @return array
     */
    public function obtenerProgresoAprendiz(int $aprendizId): array
    {
        $evidencias = $this->evidenciaGuiaRepo->obtenerPorAprendiz($aprendizId);

        $total = $evidencias->count();
        $calificadas = $evidencias->whereNotNull('calificacion')->count();
        $aprobadas = $evidencias->where('calificacion', '>=', 3.0)->count();

        return [
            'total_evidencias' => $total,
            'evidencias_calificadas' => $calificadas,
            'evidencias_aprobadas' => $aprobadas,
            'porcentaje_completado' => $total > 0 ? round(($calificadas / $total) * 100, 2) : 0,
            'porcentaje_aprobacion' => $calificadas > 0 ? round(($aprobadas / $calificadas) * 100, 2) : 0,
        ];
    }
}

