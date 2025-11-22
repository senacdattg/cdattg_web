<?php

namespace App\Services;

use App\Models\InstructorFichaDias;
use App\Models\InstructorFichaCaracterizacion;
use App\Models\FichaCaracterizacion;
use App\Models\Instructor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class InstructorFichaDiasService
{
    /**
     * Asigna días de formación a un instructor en una ficha específica.
     *
     * @param int $instructorFichaId ID de la relación instructor-ficha
     * @param array $diasData Array de días con estructura: [['dia_id' => 12, 'hora_inicio' => '08:00', 'hora_fin' => '12:00'], ...]
     * @return array
     */
    public function asignarDiasInstructor(int $instructorFichaId, array $diasData): array
    {
        try {
            \Log::info('Iniciando asignación de días', [
                'instructor_ficha_id' => $instructorFichaId,
                'dias_data' => $diasData
            ]);

            DB::beginTransaction();

            // Obtener la relación instructor-ficha
            $instructorFicha = InstructorFichaCaracterizacion::with(['instructor', 'ficha'])->findOrFail($instructorFichaId);
            
            \Log::info('Instructor-ficha encontrado', ['instructor_ficha' => $instructorFicha]);
            
            // Validar disponibilidad del instructor
            $validacion = $this->validarDisponibilidadInstructor($instructorFicha, $diasData);
            
            if (!$validacion['disponible']) {
                $conflictos = $validacion['conflictos'];
                $mensaje = 'El instructor tiene conflictos de horario, jornada o fechas con otras fichas asignadas:';
                
                $detallesConflictos = [];
                foreach ($conflictos as $conflicto) {
                    $detalle = "• {$conflicto['dia_nombre']}: Ficha {$conflicto['ficha_conflicto']} ({$conflicto['programa_conflicto']}) - ";
                    $detalle .= "Jornada: {$conflicto['jornada_conflicto']}, ";
                    $detalle .= "Fechas: {$conflicto['fecha_inicio_conflicto']} a {$conflicto['fecha_fin_conflicto']}, ";
                    $detalle .= "Horario: {$conflicto['horario_conflicto']} (solicitado: {$conflicto['horario_solicitado']})";
                    $detallesConflictos[] = $detalle;
                }
                
                $mensajeCompleto = $mensaje . "\n\n" . implode("\n", $detallesConflictos);
                
                return [
                    'success' => false,
                    'message' => $mensajeCompleto,
                    'conflictos' => $conflictos
                ];
            }

            // Eliminar días existentes para esta relación
            InstructorFichaDias::where('instructor_ficha_id', $instructorFichaId)->delete();

            // Crear los nuevos días
            $diasCreados = [];
            foreach ($diasData as $diaData) {
                $diaCreado = InstructorFichaDias::create([
                    'instructor_ficha_id' => $instructorFichaId,
                    'dia_id' => $diaData['dia_id'],
                    'hora_inicio' => $diaData['hora_inicio'] ?? null,
                    'hora_fin' => $diaData['hora_fin'] ?? null,
                ]);
                
                $diasCreados[] = $diaCreado;
            }

            // Generar fechas efectivas de formación
            $fechasEfectivas = $this->generarFechasEfectivas($instructorFicha, $diasData);

            DB::commit();

            Log::info('✓ Días de formación asignados exitosamente', [
                'instructor_ficha_id' => $instructorFichaId,
                'instructor_id' => $instructorFicha->instructor_id,
                'ficha_id' => $instructorFicha->ficha_id,
                'cantidad_dias' => count($diasCreados),
                'cantidad_fechas_efectivas' => count($fechasEfectivas)
            ]);

            return [
                'success' => true,
                'message' => 'Días de formación asignados correctamente',
                'dias_asignados' => $diasCreados,
                'fechas_efectivas' => $fechasEfectivas,
                'total_sesiones' => count($fechasEfectivas)
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('✗ Error al asignar días de formación', [
                'instructor_ficha_id' => $instructorFichaId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Error al asignar días: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Valida la disponibilidad del instructor en los días y horarios especificados.
     * Valida: fechas, jornada, días y horarios
     *
     * @param InstructorFichaCaracterizacion $instructorFicha
     * @param array $diasData
     * @return array
     */
    public function validarDisponibilidadInstructor(InstructorFichaCaracterizacion $instructorFicha, array $diasData): array
    {
        $conflictos = [];
        
        $fechaInicio = Carbon::parse($instructorFicha->fecha_inicio);
        $fechaFin = Carbon::parse($instructorFicha->fecha_fin);
        $jornadaIdFicha = $instructorFicha->ficha->jornada_id ?? null;
        $diasIdsNuevos = collect($diasData)->pluck('dia_id')->toArray();
        
        // Buscar otras asignaciones del mismo instructor que puedan tener conflictos
        $otrasAsignacionesFicha = InstructorFichaCaracterizacion::where('instructor_id', $instructorFicha->instructor_id)
            ->where('id', '!=', $instructorFicha->id)
            ->whereHas('ficha', function($q) use ($jornadaIdFicha) {
                $q->where('status', true);
                // Solo validar conflictos en la misma jornada
                if ($jornadaIdFicha) {
                    $q->where('jornada_id', $jornadaIdFicha);
                }
            })
            ->where(function($q) use ($fechaInicio, $fechaFin) {
                $q->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
                  ->orWhereBetween('fecha_fin', [$fechaInicio, $fechaFin])
                  ->orWhere(function($subQ) use ($fechaInicio, $fechaFin) {
                      $subQ->where('fecha_inicio', '<=', $fechaInicio)
                           ->where('fecha_fin', '>=', $fechaFin);
                  });
            })
            ->with(['ficha.jornadaFormacion', 'instructorFichaDias.dia'])
            ->get();
        
        // Validar conflictos por día y horario
        foreach ($diasData as $diaData) {
            $diaId = $diaData['dia_id'];
            $horaInicio = $diaData['hora_inicio'] ?? null;
            $horaFin = $diaData['hora_fin'] ?? null;

            foreach ($otrasAsignacionesFicha as $otraAsignacionFicha) {
                // Buscar si la otra asignación tiene el mismo día
                $diaExistente = $otraAsignacionFicha->instructorFichaDias->firstWhere('dia_id', $diaId);
                
                if ($diaExistente) {
                    // Hay conflicto de día, ahora validar horario si ambos tienen horarios
                    $hayConflictoHorario = false;
                    
                    if ($horaInicio && $horaFin && $diaExistente->hora_inicio && $diaExistente->hora_fin) {
                        $hayConflictoHorario = $this->hayConflictoHorario(
                            $horaInicio, 
                            $horaFin, 
                            $diaExistente->hora_inicio, 
                            $diaExistente->hora_fin
                        );
                    } elseif ($horaInicio && $horaFin && (!$diaExistente->hora_inicio || !$diaExistente->hora_fin)) {
                        // Si la nueva asignación tiene horario pero la existente no, considerar conflicto
                        $hayConflictoHorario = true;
                    } elseif (!$horaInicio || !$horaFin) {
                        // Si la nueva asignación no tiene horario, considerar conflicto si hay día en común
                        $hayConflictoHorario = true;
                    }
                    
                    if ($hayConflictoHorario) {
                        $conflictos[] = [
                            'dia_id' => $diaId,
                            'dia_nombre' => $this->obtenerNombreDia($diaId),
                            'ficha_conflicto' => $otraAsignacionFicha->ficha->ficha ?? 'N/A',
                            'programa_conflicto' => $otraAsignacionFicha->ficha->programaFormacion->nombre ?? 'N/A',
                            'jornada_conflicto' => $otraAsignacionFicha->ficha->jornadaFormacion->jornada ?? 'N/A',
                            'fecha_inicio_conflicto' => Carbon::parse($otraAsignacionFicha->fecha_inicio)->format('d/m/Y'),
                            'fecha_fin_conflicto' => Carbon::parse($otraAsignacionFicha->fecha_fin)->format('d/m/Y'),
                            'horario_conflicto' => $diaExistente->hora_inicio && $diaExistente->hora_fin 
                                ? $diaExistente->hora_inicio . ' - ' . $diaExistente->hora_fin 
                                : 'Sin horario',
                            'horario_solicitado' => $horaInicio && $horaFin 
                                ? $horaInicio . ' - ' . $horaFin 
                                : 'Sin horario'
                        ];
                    }
                }
            }
        }

        return [
            'disponible' => empty($conflictos),
            'conflictos' => $conflictos
        ];
    }

    /**
     * Verifica si hay conflicto entre dos rangos horarios.
     */
    private function hayConflictoHorario(string $inicio1, string $fin1, string $inicio2, string $fin2): bool
    {
        $inicio1 = Carbon::parse($inicio1);
        $fin1 = Carbon::parse($fin1);
        $inicio2 = Carbon::parse($inicio2);
        $fin2 = Carbon::parse($fin2);

        return !($fin1->lte($inicio2) || $inicio1->gte($fin2));
    }

    /**
     * Genera las fechas efectivas de formación dentro del rango de la ficha.
     *
     * @param InstructorFichaCaracterizacion|object $instructorFicha
     * @param array $diasData
     * @return array
     */
    public function generarFechasEfectivas($instructorFicha, array $diasData): array
    {
        $fechasEfectivas = [];
        
        // Obtener el rango de fechas de la ficha (soporta tanto modelo como objeto)
        $fechaInicio = $instructorFicha->fecha_inicio ?? ($instructorFicha->ficha->fecha_inicio ?? null);
        $fechaFin = $instructorFicha->fecha_fin ?? ($instructorFicha->ficha->fecha_fin ?? null);

        if (!$fechaInicio || !$fechaFin) {
            return [];
        }

        $fechaActual = Carbon::parse($fechaInicio);
        $fechaFinal = Carbon::parse($fechaFin);

        // Mapear IDs de días a números de día de la semana
        $diasSemanaMap = $this->mapearDiasANumerosSemana($diasData);

        // Iterar por cada día en el rango
        while ($fechaActual->lte($fechaFinal)) {
            $diaSemana = $fechaActual->dayOfWeek; // 0=Domingo, 1=Lunes, ..., 6=Sábado
            
            // Verificar si este día de la semana está en los días asignados
            if (isset($diasSemanaMap[$diaSemana])) {
                $diaInfo = $diasSemanaMap[$diaSemana];
                
                $fechasEfectivas[] = [
                    'fecha' => $fechaActual->format('Y-m-d'),
                    'dia_semana' => $fechaActual->locale('es')->isoFormat('dddd'),
                    'dia_id' => $diaInfo['dia_id'],
                    'hora_inicio' => $diaInfo['hora_inicio'] ?? null,
                    'hora_fin' => $diaInfo['hora_fin'] ?? null
                ];
            }

            $fechaActual->addDay();
        }

        return $fechasEfectivas;
    }

    /**
     * Mapea los IDs de días de parámetros a números de día de la semana.
     * 
     * @param array $diasData
     * @return array
     */
    private function mapearDiasANumerosSemana(array $diasData): array
    {
        $mapa = [];
        
        // Mapeo de dia_id a dayOfWeek (Carbon)
        // 12=Lunes->1, 13=Martes->2, 14=Miércoles->3, 15=Jueves->4, 16=Viernes->5, 17=Sábado->6, 18=Domingo->0
        $diaIdANumero = [
            12 => 1, // Lunes
            13 => 2, // Martes
            14 => 3, // Miércoles
            15 => 4, // Jueves
            16 => 5, // Viernes
            17 => 6, // Sábado
            18 => 0  // Domingo
        ];

        foreach ($diasData as $diaData) {
            $diaId = $diaData['dia_id'];
            if (isset($diaIdANumero[$diaId])) {
                $numeroDia = $diaIdANumero[$diaId];
                $mapa[$numeroDia] = $diaData;
            }
        }

        return $mapa;
    }

    /**
     * Obtiene el nombre del día según su ID.
     */
    private function obtenerNombreDia(int $diaId): string
    {
        $dias = [
            12 => 'Lunes',
            13 => 'Martes',
            14 => 'Miércoles',
            15 => 'Jueves',
            16 => 'Viernes',
            17 => 'Sábado',
            18 => 'Domingo'
        ];

        return $dias[$diaId] ?? 'Desconocido';
    }

    /**
     * Obtiene los días asignados a un instructor en una ficha.
     *
     * @param int $instructorFichaId
     * @return array
     */
    public function obtenerDiasAsignados(int $instructorFichaId): array
    {
        $dias = InstructorFichaDias::where('instructor_ficha_id', $instructorFichaId)
            ->with('dia')
            ->get();

        return $dias->map(function($dia) {
            return [
                'id' => $dia->id,
                'dia_id' => $dia->dia_id,
                'dia_nombre' => $this->obtenerNombreDia($dia->dia_id),
                'hora_inicio' => $dia->hora_inicio,
                'hora_fin' => $dia->hora_fin
            ];
        })->toArray();
    }

    /**
     * Verifica si un instructor está disponible en un día y horario específico.
     *
     * @param int $instructorId
     * @param int $diaId
     * @param string|null $horaInicio
     * @param string|null $horaFin
     * @param int|null $excludeInstructorFichaId
     * @return bool
     */
    public function estaDisponible(int $instructorId, int $diaId, ?string $horaInicio = null, ?string $horaFin = null, ?int $excludeInstructorFichaId = null): bool
    {
        $query = InstructorFichaDias::whereHas('instructorFicha', function($q) use ($instructorId, $excludeInstructorFichaId) {
            $q->where('instructor_id', $instructorId);
            if ($excludeInstructorFichaId) {
                $q->where('id', '!=', $excludeInstructorFichaId);
            }
        })->where('dia_id', $diaId);

        if (!$horaInicio || !$horaFin) {
            return $query->count() == 0;
        }

        $asignaciones = $query->get();

        foreach ($asignaciones as $asignacion) {
            if ($asignacion->hora_inicio && $asignacion->hora_fin) {
                if ($this->hayConflictoHorario($horaInicio, $horaFin, $asignacion->hora_inicio, $asignacion->hora_fin)) {
                    return false;
                }
            }
        }

        return true;
    }
}

