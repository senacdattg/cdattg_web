<?php

namespace App\Services;

use App\Models\Instructor;
use App\Models\FichaCaracterizacion;
use App\Models\InstructorFichaCaracterizacion;
use App\Models\AsignacionInstructorLog;
use App\Services\InstructorBusinessRulesService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AsignacionInstructorService
{
    protected $businessRulesService;

    public function __construct(InstructorBusinessRulesService $businessRulesService)
    {
        $this->businessRulesService = $businessRulesService;
    }

    /**
     * Asignar instructores a una ficha con validaciones completas
     */
    public function asignarInstructores(array $instructoresData, int $fichaId, int $instructorPrincipalId, int $userId): array
    {
        DB::beginTransaction();
        
        try {
            $ficha = FichaCaracterizacion::with(['programaFormacion.redConocimiento', 'diasFormacion'])->findOrFail($fichaId);
            
            // Validar que la ficha esté activa
            if (!$ficha->status) {
                throw new \Exception('La ficha no está activa');
            }

            // Preparar datos para validaciones
            $datosFicha = [
                'fecha_inicio' => $ficha->fecha_inicio,
                'fecha_fin' => $ficha->fecha_fin,
                'especialidad_requerida' => $ficha->programaFormacion->redConocimiento->nombre ?? null,
                'regional_id' => $ficha->regional_id,
                'horas_semanales' => 0
            ];

            // Validar cada instructor antes de la asignación
            $instructoresValidos = [];
            foreach ($instructoresData as $instructorData) {
                $instructor = Instructor::findOrFail($instructorData['instructor_id']);
                
                // Actualizar datos de la ficha con horas específicas
                $datosFicha['horas_semanales'] = $instructorData['total_horas_instructor'];
                
                // Validar disponibilidad
                $disponibilidad = $this->businessRulesService->verificarDisponibilidad($instructor, $datosFicha);
                if (!$disponibilidad['disponible']) {
                    throw new \Exception("El instructor {$instructor->nombre_completo} no está disponible: " . implode(', ', $disponibilidad['razones']));
                }

                // Validar reglas SENA
                $validacionSENA = $this->businessRulesService->validarReglasSENA($instructor, $datosFicha);
                if (!$validacionSENA['valido']) {
                    throw new \Exception("El instructor {$instructor->nombre_completo} no cumple las reglas SENA: " . implode(', ', $validacionSENA['errores']));
                }

                $instructoresValidos[] = $instructorData;
            }

            // Eliminar asignaciones existentes
            $asignacionesAnteriores = $ficha->instructorFicha()->get();
            $ficha->instructorFicha()->delete();

            // Crear log de asignaciones anteriores
            foreach ($asignacionesAnteriores as $asignacion) {
                AsignacionInstructorLog::crearLog(
                    $asignacion->instructor_id,
                    $fichaId,
                    'desasignar',
                    'exitoso',
                    'Asignación eliminada para reasignación',
                    $userId,
                    ['motivo' => 'reasignacion_completa'],
                    [
                        'fecha_inicio' => $asignacion->fecha_inicio,
                        'fecha_fin' => $asignacion->fecha_fin,
                        'total_horas' => $asignacion->total_horas_instructor
                    ]
                );
            }

            // Crear nuevas asignaciones
            $asignacionesCreadas = [];
            foreach ($instructoresValidos as $instructorData) {
                $instructorFicha = $this->crearAsignacion($instructorData, $fichaId, $userId);
                $asignacionesCreadas[] = $instructorFicha;
            }

            // Actualizar instructor principal
            $ficha->update([
                'instructor_id' => $instructorPrincipalId,
                'user_edit_id' => $userId
            ]);

            DB::commit();

            // Crear logs de éxito
            foreach ($asignacionesCreadas as $asignacion) {
                AsignacionInstructorLog::crearLog(
                    $asignacion->instructor_id,
                    $fichaId,
                    'asignar',
                    'exitoso',
                    'Instructor asignado exitosamente a la ficha',
                    $userId,
                    [
                        'fecha_inicio' => $asignacion->fecha_inicio,
                        'fecha_fin' => $asignacion->fecha_fin,
                        'total_horas' => $asignacion->total_horas_instructor,
                        'es_principal' => $asignacion->instructor_id == $instructorPrincipalId
                    ],
                    null,
                    [
                        'fecha_inicio' => $asignacion->fecha_inicio,
                        'fecha_fin' => $asignacion->fecha_fin,
                        'total_horas' => $asignacion->total_horas_instructor
                    ]
                );
            }

            Log::info('Instructores asignados exitosamente', [
                'ficha_id' => $fichaId,
                'instructor_principal_id' => $instructorPrincipalId,
                'total_instructores' => count($asignacionesCreadas),
                'user_id' => $userId
            ]);

            return [
                'success' => true,
                'message' => 'Instructores asignados exitosamente',
                'asignaciones' => $asignacionesCreadas,
                'total_asignados' => count($asignacionesCreadas)
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Crear log de error
            AsignacionInstructorLog::crearLog(
                0, // instructor_id temporal para errores generales
                $fichaId,
                'asignar',
                'error',
                'Error en asignación de instructores: ' . $e->getMessage(),
                $userId,
                ['error' => $e->getMessage(), 'instructores_data' => $instructoresData]
            );

            Log::error('Error asignando instructores', [
                'ficha_id' => $fichaId,
                'error' => $e->getMessage(),
                'user_id' => $userId,
                'instructores_data' => $instructoresData
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Crear una asignación individual de instructor
     */
    private function crearAsignacion(array $instructorData, int $fichaId, int $userId): InstructorFichaCaracterizacion
    {
        $instructorFicha = InstructorFichaCaracterizacion::create([
            'instructor_id' => $instructorData['instructor_id'],
            'ficha_id' => $fichaId,
            'fecha_inicio' => $instructorData['fecha_inicio'],
            'fecha_fin' => $instructorData['fecha_fin'],
            'total_horas_instructor' => $instructorData['total_horas_instructor']
        ]);

        // Crear días de formación si se proporcionaron
        if (isset($instructorData['dias_formacion']) && is_array($instructorData['dias_formacion'])) {
            foreach ($instructorData['dias_formacion'] as $diaData) {
                $instructorFicha->instructorFichaDias()->create([
                    'dia_id' => $diaData['dia_id']
                ]);
            }
        }

        return $instructorFicha;
    }

    /**
     * Desasignar un instructor específico de una ficha
     */
    public function desasignarInstructor(int $instructorId, int $fichaId, int $userId): array
    {
        DB::beginTransaction();
        
        try {
            $asignacion = InstructorFichaCaracterizacion::where('instructor_id', $instructorId)
                ->where('ficha_id', $fichaId)
                ->firstOrFail();

            $instructor = Instructor::find($instructorId);
            $ficha = FichaCaracterizacion::find($fichaId);

            // Verificar si hay asistencias registradas
            $tieneAsistencias = $this->verificarAsistenciasRegistradas($instructorId, $fichaId);
            if ($tieneAsistencias) {
                throw new \Exception('No se puede desasignar el instructor porque ya existen clases o asistencias registradas en el rango de fechas.');
            }

            // Guardar datos anteriores para el log
            $datosAnteriores = [
                'fecha_inicio' => $asignacion->fecha_inicio,
                'fecha_fin' => $asignacion->fecha_fin,
                'total_horas' => $asignacion->total_horas_instructor
            ];

            $asignacion->delete();

            // Actualizar instructor principal si es necesario
            if ($ficha && $ficha->instructor_id == $instructorId) {
                $nuevaAsignacion = $ficha->instructorFicha()->first();
                $nuevoInstructorPrincipal = $nuevaAsignacion ? $nuevaAsignacion->instructor_id : null;
                
                $ficha->update([
                    'instructor_id' => $nuevoInstructorPrincipal,
                    'user_edit_id' => $userId
                ]);
            }

            DB::commit();

            // Crear log de desasignación
            AsignacionInstructorLog::crearLog(
                $instructorId,
                $fichaId,
                'desasignar',
                'exitoso',
                "Instructor {$instructor->nombre_completo} desasignado exitosamente de la ficha {$ficha->ficha}",
                $userId,
                ['motivo' => 'desasignacion_manual'],
                $datosAnteriores
            );

            Log::info('Instructor desasignado exitosamente', [
                'instructor_id' => $instructorId,
                'ficha_id' => $fichaId,
                'user_id' => $userId
            ]);

            return [
                'success' => true,
                'message' => 'Instructor desasignado exitosamente'
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Crear log de error
            AsignacionInstructorLog::crearLog(
                $instructorId,
                $fichaId,
                'desasignar',
                'error',
                'Error al desasignar instructor: ' . $e->getMessage(),
                $userId,
                ['error' => $e->getMessage()]
            );

            Log::error('Error desasignando instructor', [
                'instructor_id' => $instructorId,
                'ficha_id' => $fichaId,
                'error' => $e->getMessage(),
                'user_id' => $userId
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Verificar si hay asistencias registradas para un instructor en una ficha
     */
    private function verificarAsistenciasRegistradas(int $instructorId, int $fichaId): bool
    {
        // Verificar si existen asistencias de aprendices asociadas a este instructor-ficha
        $asignacion = InstructorFichaCaracterizacion::where('instructor_id', $instructorId)
            ->where('ficha_id', $fichaId)
            ->first();

        if (!$asignacion) {
            return false;
        }

        // Verificar asistencias en el rango de fechas
        $tieneAsistencias = \App\Models\AsistenciaAprendiz::where('instructor_ficha_id', $asignacion->id)
            ->exists();

        return $tieneAsistencias;
    }

    /**
     * Obtener instructores disponibles para una ficha específica
     */
    public function obtenerInstructoresDisponibles(int $fichaId): array
    {
        try {
            $ficha = FichaCaracterizacion::with(['programaFormacion.redConocimiento', 'diasFormacion', 'sede.regional'])->findOrFail($fichaId);
            
            $regionalId = $ficha->sede->regional_id ?? null;
            
            $datosFicha = [
                'fecha_inicio' => $ficha->fecha_inicio,
                'fecha_fin' => $ficha->fecha_fin,
                'especialidad_requerida' => $ficha->programaFormacion->redConocimiento->nombre ?? null,
                'regional_id' => $regionalId,
                'horas_semanales' => 0
            ];

            $instructores = Instructor::with(['persona', 'regional'])
                ->where('status', true);
            
            // Solo filtrar por regional si existe
            if ($regionalId) {
                $instructores->where('regional_id', $regionalId);
            }
            
            $instructores = $instructores->get();

            Log::info('Instructores encontrados para ficha', [
                'ficha_id' => $fichaId,
                'regional_id' => $regionalId,
                'total_instructores' => $instructores->count(),
                'instructores' => $instructores->pluck('id')->toArray()
            ]);

            $disponibles = [];
            foreach ($instructores as $instructor) {
                $disponibilidad = $this->businessRulesService->verificarDisponibilidad($instructor, $datosFicha);
                $validacionSENA = $this->businessRulesService->validarReglasSENA($instructor, $datosFicha);
                
                $disponibles[] = [
                    'instructor' => $instructor,
                    'disponible' => $disponibilidad['disponible'] && $validacionSENA['valido'],
                    'razones_no_disponible' => array_merge($disponibilidad['razones'], $validacionSENA['errores']),
                    'conflictos' => $disponibilidad['conflictos'] ?? [],
                    'advertencias' => $validacionSENA['advertencias'] ?? []
                ];
            }

            Log::info('Instructores disponibles procesados', [
                'ficha_id' => $fichaId,
                'total_disponibles' => count($disponibles),
                'disponibles_ids' => array_map(fn($d) => $d['instructor']->id, $disponibles)
            ]);

            return $disponibles;

        } catch (\Exception $e) {
            Log::error('Error obteniendo instructores disponibles', [
                'ficha_id' => $fichaId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [];
        }
    }

    /**
     * Obtener estadísticas de asignaciones
     */
    public function obtenerEstadisticasAsignaciones(Carbon $fechaInicio = null, Carbon $fechaFin = null): array
    {
        $fechaInicio = $fechaInicio ?? now()->startOfMonth();
        $fechaFin = $fechaFin ?? now()->endOfMonth();

        $estadisticas = AsignacionInstructorLog::obtenerEstadisticas($fechaInicio, $fechaFin);
        
        // Estadísticas adicionales
        $totalAsignacionesActivas = InstructorFichaCaracterizacion::whereHas('ficha', function($q) {
            $q->where('status', true)
              ->where('fecha_fin', '>=', now()->toDateString());
        })->count();

        $instructoresConFichas = Instructor::whereHas('instructorFichas', function($q) {
            $q->whereHas('ficha', function($subQ) {
                $subQ->where('status', true)
                     ->where('fecha_fin', '>=', now()->toDateString());
            });
        })->count();

        return array_merge($estadisticas, [
            'total_asignaciones_activas' => $totalAsignacionesActivas,
            'instructores_con_fichas' => $instructoresConFichas,
            'promedio_fichas_por_instructor' => $instructoresConFichas > 0 ? round($totalAsignacionesActivas / $instructoresConFichas, 2) : 0
        ]);
    }
}
