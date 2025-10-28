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
            $ficha = FichaCaracterizacion::with(['programaFormacion.redConocimiento', 'diasFormacion', 'jornadaFormacion'])->findOrFail($fichaId);
            
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
                'jornada_id' => $ficha->jornada_id,
                'horas_semanales' => 0
            ];

            // Validar cada instructor antes de la asignación
            $instructoresValidos = [];
            $instructorIdConError = null; // Para registrar en logs si hay error
            
            foreach ($instructoresData as $instructorData) {
                $instructor = Instructor::findOrFail($instructorData['instructor_id']);
                $instructorIdConError = $instructor->id; // Guardar ID para posibles errores
                
                // CALCULAR HORAS AUTOMÁTICAMENTE
                $horasCalculadas = $this->calcularHorasTotalesAutomaticas($instructorData, $fichaId);
                $instructorData['total_horas_instructor'] = $horasCalculadas;
                
                // Actualizar datos de la ficha con horas calculadas automáticamente
                $datosFicha['horas_semanales'] = $horasCalculadas;
                $datosFicha['dias_formacion'] = $instructorData['dias_semana'] ?? ($instructorData['dias_formacion'] ?? []);
                
                // Validar disponibilidad
                $disponibilidad = $this->businessRulesService->verificarDisponibilidad($instructor, $datosFicha, $fichaId);
                if (!$disponibilidad['disponible']) {
                    throw new \Exception("El instructor {$instructor->nombre_completo} no está disponible: " . implode(', ', $disponibilidad['razones']));
                }

                $instructoresValidos[] = $instructorData;
                $instructorIdConError = null; // Reset si no hay error
            }

            // Obtener asignaciones existentes
            $asignacionesExistentes = $ficha->instructorFicha()->get();

            // Crear o actualizar asignaciones (sin eliminar las existentes)
            $asignacionesCreadas = [];
            foreach ($instructoresValidos as $instructorData) {
                // Buscar si ya existe una asignación para este instructor
                $asignacionExistente = $asignacionesExistentes->firstWhere('instructor_id', $instructorData['instructor_id']);
                
                if ($asignacionExistente) {
                    // Actualizar asignación existente
                    $asignacionExistente->update([
                        'fecha_inicio' => $instructorData['fecha_inicio'],
                        'fecha_fin' => $instructorData['fecha_fin'],
                        'total_horas_instructor' => $instructorData['total_horas_instructor'],
                        'user_edit_id' => $userId
                    ]);
                    
                    // Eliminar días existentes
                    $asignacionExistente->instructorFichaDias()->delete();
                    
                    // Obtener días seleccionados
                    $diasSeleccionados = [];
                    
                    if (isset($instructorData['dias_semana']) && is_array($instructorData['dias_semana'])) {
                        $diasSeleccionados = $instructorData['dias_semana'];
                    } elseif (isset($instructorData['dias']) && is_array($instructorData['dias'])) {
                        foreach ($instructorData['dias'] as $diaId => $diaInfo) {
                            if (isset($diaInfo['hora_inicio']) && isset($diaInfo['hora_fin'])) {
                                $asignacionExistente->instructorFichaDias()->create([
                                    'dia_id' => $diaId,
                                    'hora_inicio' => $diaInfo['hora_inicio'],
                                    'hora_fin' => $diaInfo['hora_fin']
                                ]);
                            }
                        }
                        $asignacionesCreadas[] = $asignacionExistente;
                        continue; // Ya se procesaron
                    } elseif (isset($instructorData['dias_formacion']) && is_array($instructorData['dias_formacion'])) {
                        $diasSeleccionados = collect($instructorData['dias_formacion'])->pluck('dia_id')->filter()->toArray();
                    }
                    
                    // Crear días tomando horarios de la configuración de la ficha
                    if (!empty($diasSeleccionados)) {
                        foreach ($diasSeleccionados as $diaId) {
                            $diaFormacionFicha = $ficha->diasFormacion->firstWhere('dia_id', $diaId);
                            
                            $horaInicio = $diaFormacionFicha->hora_inicio ?? ($ficha->jornadaFormacion->hora_inicio ?? '08:00');
                            $horaFin = $diaFormacionFicha->hora_fin ?? ($ficha->jornadaFormacion->hora_fin ?? '12:00');
                            
                            $asignacionExistente->instructorFichaDias()->create([
                                'dia_id' => $diaId,
                                'hora_inicio' => $horaInicio,
                                'hora_fin' => $horaFin
                            ]);
                        }
                    }
                    
                    $asignacionesCreadas[] = $asignacionExistente;
                } else {
                    // Crear nueva asignación
                    $instructorFicha = $this->crearAsignacion($instructorData, $fichaId, $userId);
                    $asignacionesCreadas[] = $instructorFicha;
                }
            }

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
            
            // Crear log de error con el ID del instructor que causó el problema (si existe)
            AsignacionInstructorLog::crearLog(
                $instructorIdConError ?? null, // ID del instructor que falló, o null si fue error general
                $fichaId,
                'asignar',
                'error',
                'Error en asignación de instructores: ' . $e->getMessage(),
                $userId,
                [
                    'error' => $e->getMessage(), 
                    'instructores_data' => $instructoresData,
                    'instructor_con_error' => $instructorIdConError
                ]
            );

            Log::error('Error asignando instructores', [
                'ficha_id' => $fichaId,
                'error' => $e->getMessage(),
                'user_id' => $userId,
                'instructor_id_con_error' => $instructorIdConError,
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
        // Obtener la ficha con sus días de formación y jornada
        $ficha = FichaCaracterizacion::with(['jornadaFormacion', 'diasFormacion'])->findOrFail($fichaId);
        
        $instructorFicha = InstructorFichaCaracterizacion::create([
            'instructor_id' => $instructorData['instructor_id'],
            'ficha_id' => $fichaId,
            'fecha_inicio' => $instructorData['fecha_inicio'],
            'fecha_fin' => $instructorData['fecha_fin'],
            'total_horas_instructor' => $instructorData['total_horas_instructor']
        ]);

        // Crear días de formación tomando horarios de la configuración de la ficha
        $diasSeleccionados = [];
        
        // Formato nuevo: dias_semana (array simple de IDs)
        if (isset($instructorData['dias_semana']) && is_array($instructorData['dias_semana'])) {
            $diasSeleccionados = $instructorData['dias_semana'];
        }
        // Formato con horarios específicos: dias (array asociativo)
        elseif (isset($instructorData['dias']) && is_array($instructorData['dias'])) {
            foreach ($instructorData['dias'] as $diaId => $diaInfo) {
                if (isset($diaInfo['hora_inicio']) && isset($diaInfo['hora_fin'])) {
                    $instructorFicha->instructorFichaDias()->create([
                        'dia_id' => $diaId,
                        'hora_inicio' => $diaInfo['hora_inicio'],
                        'hora_fin' => $diaInfo['hora_fin']
                    ]);
                }
            }
            return $instructorFicha; // Salir porque ya se procesaron con horarios específicos
        }
        // Formato antiguo: dias_formacion
        elseif (isset($instructorData['dias_formacion']) && is_array($instructorData['dias_formacion'])) {
            $diasSeleccionados = collect($instructorData['dias_formacion'])->pluck('dia_id')->filter()->toArray();
        }

        // Procesar días seleccionados tomando horarios de la ficha
        if (!empty($diasSeleccionados)) {
            foreach ($diasSeleccionados as $diaId) {
                // Buscar horario del día en la configuración de la ficha
                $diaFormacionFicha = $ficha->diasFormacion->firstWhere('dia_id', $diaId);
                
                $horaInicio = $diaFormacionFicha->hora_inicio ?? ($ficha->jornadaFormacion->hora_inicio ?? '08:00');
                $horaFin = $diaFormacionFicha->hora_fin ?? ($ficha->jornadaFormacion->hora_fin ?? '12:00');
                
                $instructorFicha->instructorFichaDias()->create([
                    'dia_id' => $diaId,
                    'hora_inicio' => $horaInicio,
                    'hora_fin' => $horaFin
                ]);
            }
        }

        return $instructorFicha;
    }

    /**
     * Calcular horas totales automáticamente basado en días de formación y fechas
     */
    public function calcularHorasTotalesAutomaticas(array $instructorData, int $fichaId): int
    {
        try {
            $ficha = FichaCaracterizacion::with(['diasFormacion', 'jornadaFormacion'])->findOrFail($fichaId);
            
            // Obtener días seleccionados
            $diasSeleccionados = [];
            
            if (isset($instructorData['dias_semana']) && is_array($instructorData['dias_semana'])) {
                $diasSeleccionados = $instructorData['dias_semana'];
            } elseif (isset($instructorData['dias']) && is_array($instructorData['dias'])) {
                $diasSeleccionados = array_keys($instructorData['dias']);
            } elseif (isset($instructorData['dias_formacion']) && is_array($instructorData['dias_formacion'])) {
                $diasSeleccionados = collect($instructorData['dias_formacion'])->pluck('dia_id')->filter()->toArray();
            }
            
            if (empty($diasSeleccionados)) {
                return 40; // Valor por defecto si no hay días
            }
            
            // Preparar datos de días con horarios de la ficha
            $diasParaServicio = [];
            foreach ($diasSeleccionados as $diaId) {
                // Buscar horario del día en la configuración de la ficha
                $diaFormacionFicha = $ficha->diasFormacion->firstWhere('dia_id', $diaId);
                
                $horaInicio = $diaFormacionFicha->hora_inicio ?? ($ficha->jornadaFormacion->hora_inicio ?? '08:00');
                $horaFin = $diaFormacionFicha->hora_fin ?? ($ficha->jornadaFormacion->hora_fin ?? '12:00');
                
                $diasParaServicio[] = [
                    'dia_id' => $diaId,
                    'hora_inicio' => $horaInicio,
                    'hora_fin' => $horaFin
                ];
            }
            
            // Crear objeto temporal para el cálculo
            $instructorFichaTemp = new \stdClass();
            $instructorFichaTemp->fecha_inicio = $instructorData['fecha_inicio'];
            $instructorFichaTemp->fecha_fin = $instructorData['fecha_fin'];
            $instructorFichaTemp->ficha = $ficha;
            
            // Calcular horas totales basado en fechas efectivas
            $totalHoras = $this->calcularHorasDesdeFechasEfectivas($instructorFichaTemp, $diasParaServicio);
            
            return $totalHoras > 0 ? $totalHoras : 40; // Mínimo 40 horas por defecto
            
        } catch (\Exception $e) {
            Log::error('Error calculando horas automáticas', [
                'ficha_id' => $fichaId,
                'instructor_data' => $instructorData,
                'error' => $e->getMessage()
            ]);
            
            // 4. Obtener horas por jornada desde ficha_dias_formacion
            $horasPorJornada = 6.5; // Valor por defecto
            
            if ($ficha->diasFormacion && $ficha->diasFormacion->isNotEmpty()) {
                // Obtener el primer día de formación para tomar las horas (asumiendo que todos tienen las mismas horas)
                $primerDia = $ficha->diasFormacion->first();
                if ($primerDia && $primerDia->hora_inicio && $primerDia->hora_fin) {
                    $horasPorJornada = $this->convertirTiempoAHoras(
                        $primerDia->hora_inicio, 
                        $primerDia->hora_fin
                    );
                    
                    Log::info('🕒 Horas obtenidas de ficha_dias_formacion', [
                        'ficha_id' => $fichaId,
                        'hora_inicio' => $primerDia->hora_inicio,
                        'hora_fin' => $primerDia->hora_fin,
                        'horas_calculadas' => $horasPorJornada
                    ]);
                }
            } else {
                Log::warning('Ficha sin días de formación configurados, usando valor por defecto', [
                    'ficha_id' => $fichaId,
                    'horas_por_defecto' => $horasPorJornada
                ]);
            }
            
            // 5. Calcular horas totales
            $horasTotales = $diasFormacionPorSemana * $horasPorJornada * $semanas;
            
            Log::info('🔢 CÁLCULO AUTOMÁTICO DE HORAS', [
                'ficha_id' => $fichaId,
                'fecha_inicio' => $fechaInicio->format('Y-m-d'),
                'fecha_fin' => $fechaFin->format('Y-m-d'),
                'semanas' => $semanas,
                'dias_por_semana' => $diasFormacionPorSemana,
                'horas_por_jornada' => $horasPorJornada,
                'horas_totales_calculadas' => $horasTotales
            ]);
            
            return (int) round($horasTotales);
            
        } catch (\Exception $e) {
            Log::error('Error calculando horas automáticas', [
                'ficha_id' => $fichaId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Fallback: retornar un valor por defecto
            return 40; // 40 horas por defecto
        }
    }

    /**
     * Calcular horas totales desde fechas efectivas de formación
     */
    private function calcularHorasDesdeFechasEfectivas($instructorFicha, array $diasData): int
    {
        try {
            $diasService = app(InstructorFichaDiasService::class);
            $fechasEfectivas = $diasService->generarFechasEfectivas($instructorFicha, $diasData);
            
            $totalHoras = 0;
            foreach ($fechasEfectivas as $fecha) {
                if ($fecha['hora_inicio'] && $fecha['hora_fin']) {
                    $horas = $this->convertirTiempoAHoras($fecha['hora_inicio'], $fecha['hora_fin']);
                    $totalHoras += $horas;
                }
            }
            
            Log::info('Horas calculadas desde fechas efectivas', [
                'total_fechas' => count($fechasEfectivas),
                'total_horas' => $totalHoras
            ]);
            
            return (int) $totalHoras;
        } catch (\Exception $e) {
            Log::error('Error al calcular horas desde fechas efectivas', [
                'error' => $e->getMessage()
            ]);
            return 40; // Valor por defecto
        }
    }

    /**
     * Convertir tiempo de inicio y fin a horas decimales
     */
    private function convertirTiempoAHoras(?string $horaInicio, ?string $horaFin): float
    {
        // Si no hay horas definidas, usar valor por defecto de 6.5 horas
        if (!$horaInicio || !$horaFin) {
            Log::warning('Horas de jornada no definidas, usando valor por defecto', [
                'hora_inicio' => $horaInicio,
                'hora_fin' => $horaFin,
                'valor_defecto' => 6.5
            ]);
            return 6.5; // 6.5 horas por defecto
        }
        
        try {
            $inicio = Carbon::parse($horaInicio);
            $fin = Carbon::parse($horaFin);
            
            $diferenciaMinutos = $inicio->diffInMinutes($fin);
            $horas = $diferenciaMinutos / 60;
            
            return $horas;
        } catch (\Exception $e) {
            Log::error('Error parseando horas de jornada', [
                'hora_inicio' => $horaInicio,
                'hora_fin' => $horaFin,
                'error' => $e->getMessage()
            ]);
            return 6.5; // 6.5 horas por defecto en caso de error
        }
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
                ->with('instructorFichaDias.dia')
                ->firstOrFail();

            $instructor = Instructor::find($instructorId);
            $ficha = FichaCaracterizacion::find($fichaId);

            // NOTA: Se permite la desasignación manteniendo las asistencias registradas
            // Las asistencias se mantienen en la base de datos para conservar el historial
            // $tieneAsistencias = $this->verificarAsistenciasRegistradas($instructorId, $fichaId);
            // if ($tieneAsistencias) {
            //     throw new \Exception('No se puede desasignar el instructor porque ya existen clases o asistencias registradas en el rango de fechas.');
            // }

            // Guardar datos anteriores para el log (incluyendo días de formación)
            $diasFormacion = $asignacion->instructorFichaDias->map(function($dia) {
                return [
                    'dia_id' => $dia->dia_id,
                    'dia_nombre' => $dia->dia->name ?? 'Sin nombre'
                ];
            })->toArray();
            
            $datosAnteriores = [
                'fecha_inicio' => $asignacion->fecha_inicio,
                'fecha_fin' => $asignacion->fecha_fin,
                'total_horas' => $asignacion->total_horas_instructor,
                'dias_formacion' => $diasFormacion
            ];

            // SOLUCIÓN: Modificar las asistencias para que no dependan de esta asignación
            // antes de eliminar la asignación
            $asignacionesAsistencias = \DB::table('asistencia_aprendices')
                ->where('instructor_ficha_id', $asignacion->id)
                ->get();
            
            // Actualizar las asistencias para que apunten a NULL
            \DB::table('asistencia_aprendices')
                ->where('instructor_ficha_id', $asignacion->id)
                ->update(['instructor_ficha_id' => null]);
            
            // Ahora eliminar los días de formación
            $asignacion->instructorFichaDias()->delete();
            
            // Finalmente eliminar la asignación
            $asignacion->delete();

            DB::commit();

            // Crear log de desasignación
            AsignacionInstructorLog::crearLog(
                $instructorId,
                $fichaId,
                'desasignar',
                'exitoso',
                "Instructor {$instructor->nombre_completo} desasignado exitosamente de la ficha {$ficha->ficha}. Las asistencias registradas se mantienen pero se desvinculan de la asignación específica.",
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
                'message' => 'Instructor desasignado exitosamente. Las asistencias registradas se mantienen pero se desvinculan de la asignación específica.'
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
            $ficha = FichaCaracterizacion::with(['programaFormacion.redConocimiento', 'diasFormacion', 'sede.regional', 'jornadaFormacion'])->findOrFail($fichaId);
            
            $regionalId = $ficha->sede->regional_id ?? null;
            
            $datosFicha = [
                'fecha_inicio' => $ficha->fecha_inicio,
                'fecha_fin' => $ficha->fecha_fin,
                'especialidad_requerida' => $ficha->programaFormacion->redConocimiento->nombre ?? null,
                'regional_id' => $regionalId,
                'jornada_id' => $ficha->jornada_id,
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
                $disponibilidad = $this->businessRulesService->verificarDisponibilidad($instructor, $datosFicha, $fichaId);
                $validacionSENA = $this->businessRulesService->validarReglasSENA($instructor, $datosFicha);
                
                $esDisponible = $disponibilidad['disponible'] && $validacionSENA['valido'];
                
                // INSTRUCTOR LÍDER: Siempre marcarlo como disponible para que aparezca en la lista
                if ($instructor->id == $ficha->instructor_id) {
                    $esDisponible = true; // Forzar disponibilidad para instructor líder
                    Log::info('🔍 INSTRUCTOR LÍDER FORZADO COMO DISPONIBLE', [
                        'instructor_id' => $instructor->id,
                        'disponibilidad_original' => $disponibilidad,
                        'validacion_sena_original' => $validacionSENA,
                        'es_disponible_original' => $disponibilidad['disponible'] && $validacionSENA['valido'],
                        'es_disponible_forzado' => true
                    ]);
                }
                
                $disponibles[] = [
                    'instructor' => $instructor,
                    'disponible' => $esDisponible,
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
