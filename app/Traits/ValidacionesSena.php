<?php

namespace App\Traits;

use App\Models\FichaCaracterizacion;
use App\Models\Ambiente;
use App\Models\Instructor;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

trait ValidacionesSena
{
    /**
     * Valida la disponibilidad de un ambiente en un rango de fechas específico.
     *
     * @param int $ambienteId ID del ambiente
     * @param string $fechaInicio Fecha de inicio
     * @param string $fechaFin Fecha de fin
     * @param int|null $excluirFichaId ID de ficha a excluir (para actualizaciones)
     * @return array Resultado de la validación
     */
    protected function validarDisponibilidadAmbiente($ambienteId, $fechaInicio, $fechaFin, $excluirFichaId = null)
    {
        try {
            // Verificar que el ambiente existe y está activo
            $ambiente = Ambiente::find($ambienteId);
            if (!$ambiente) {
                return [
                    'valido' => false,
                    'mensaje' => 'El ambiente seleccionado no existe.'
                ];
            }

            // Verificar que el ambiente esté disponible (sin restricciones de mantenimiento)
            if (isset($ambiente->estado) && $ambiente->estado === 'MANTENIMIENTO') {
                return [
                    'valido' => false,
                    'mensaje' => 'El ambiente está en mantenimiento y no está disponible.'
                ];
            }

            // Buscar fichas que usen el mismo ambiente en el mismo rango de fechas
            $query = FichaCaracterizacion::where('ambiente_id', $ambienteId)
                ->where('status', true)
                ->where(function ($q) use ($fechaInicio, $fechaFin) {
                    $q->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
                        ->orWhereBetween('fecha_fin', [$fechaInicio, $fechaFin])
                        ->orWhere(function ($subQuery) use ($fechaInicio, $fechaFin) {
                            $subQuery->where('fecha_inicio', '<=', $fechaInicio)
                                ->where('fecha_fin', '>=', $fechaFin);
                        });
                });

            // Excluir la ficha actual si se está actualizando
            if ($excluirFichaId) {
                $query->where('id', '!=', $excluirFichaId);
            }

            $fichasConflictivas = $query->get();

            if ($fichasConflictivas->count() > 0) {
                $fichasConflictivasStr = $fichasConflictivas->pluck('ficha')->implode(', ');
                return [
                    'valido' => false,
                    'mensaje' => "El ambiente no está disponible en las fechas seleccionadas. Ya está siendo usado por las fichas: {$fichasConflictivasStr}."
                ];
            }

            return [
                'valido' => true,
                'mensaje' => 'El ambiente está disponible.'
            ];

        } catch (\Exception $e) {
            return [
                'valido' => false,
                'mensaje' => 'Error al validar disponibilidad del ambiente: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Valida la disponibilidad de un instructor en un rango de fechas específico.
     *
     * @param int $instructorId ID del instructor
     * @param string $fechaInicio Fecha de inicio
     * @param string $fechaFin Fecha de fin
     * @param int|null $excluirFichaId ID de ficha a excluir (para actualizaciones)
     * @return array Resultado de la validación
     */
    protected function validarDisponibilidadInstructor($instructorId, $fechaInicio, $fechaFin, $excluirFichaId = null)
    {
        try {
            // Verificar que el instructor existe y está activo
            $instructor = Instructor::with('persona')->find($instructorId);
            if (!$instructor) {
                return [
                    'valido' => false,
                    'mensaje' => 'El instructor seleccionado no existe.'
                ];
            }

            // Verificar que el instructor esté activo (si tiene campo status)
            if (isset($instructor->status) && !$instructor->status) {
                return [
                    'valido' => false,
                    'mensaje' => 'El instructor no está activo en el sistema.'
                ];
            }

            // Buscar fichas donde el instructor sea instructor principal
            $queryPrincipal = FichaCaracterizacion::where('instructor_id', $instructorId)
                ->where('status', true)
                ->where(function ($q) use ($fechaInicio, $fechaFin) {
                    $q->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
                        ->orWhereBetween('fecha_fin', [$fechaInicio, $fechaFin])
                        ->orWhere(function ($subQuery) use ($fechaInicio, $fechaFin) {
                            $subQuery->where('fecha_inicio', '<=', $fechaInicio)
                                ->where('fecha_fin', '>=', $fechaFin);
                        });
                });

            // Excluir la ficha actual si se está actualizando
            if ($excluirFichaId) {
                $queryPrincipal->where('id', '!=', $excluirFichaId);
            }

            $fichasPrincipales = $queryPrincipal->get();

            // Buscar fichas donde el instructor esté asignado como auxiliar
            $queryAuxiliar = DB::table('instructor_fichas_caracterizacion')
                ->join('fichas_caracterizacion', 'instructor_fichas_caracterizacion.ficha_id', '=', 'fichas_caracterizacion.id')
                ->where('instructor_fichas_caracterizacion.instructor_id', $instructorId)
                ->where('fichas_caracterizacion.status', true)
                ->where(function ($q) use ($fechaInicio, $fechaFin) {
                    $q->whereBetween('instructor_fichas_caracterizacion.fecha_inicio', [$fechaInicio, $fechaFin])
                        ->orWhereBetween('instructor_fichas_caracterizacion.fecha_fin', [$fechaInicio, $fechaFin])
                        ->orWhere(function ($subQuery) use ($fechaInicio, $fechaFin) {
                            $subQuery->where('instructor_fichas_caracterizacion.fecha_inicio', '<=', $fechaInicio)
                                ->where('instructor_fichas_caracterizacion.fecha_fin', '>=', $fechaFin);
                        });
                });

            // Excluir la ficha actual si se está actualizando
            if ($excluirFichaId) {
                $queryAuxiliar->where('instructor_fichas_caracterizacion.ficha_id', '!=', $excluirFichaId);
            }

            $fichasAuxiliares = $queryAuxiliar->get();

            $totalConflictos = $fichasPrincipales->count() + $fichasAuxiliares->count();

            if ($totalConflictos > 0) {
                $fichasConflictivas = collect()
                    ->merge($fichasPrincipales->pluck('ficha'))
                    ->merge($fichasAuxiliares->pluck('ficha'))
                    ->unique()
                    ->implode(', ');

                return [
                    'valido' => false,
                    'mensaje' => "El instructor {$instructor->persona->primer_nombre} {$instructor->persona->primer_apellido} no está disponible en las fechas seleccionadas. Ya está asignado a las fichas: {$fichasConflictivas}."
                ];
            }

            return [
                'valido' => true,
                'mensaje' => 'El instructor está disponible.'
            ];

        } catch (\Exception $e) {
            return [
                'valido' => false,
                'mensaje' => 'Error al validar disponibilidad del instructor: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Valida que el número de ficha sea único por programa de formación.
     *
     * @param string $numeroFicha Número de ficha
     * @param int $programaId ID del programa de formación
     * @param int|null $excluirFichaId ID de ficha a excluir (para actualizaciones)
     * @return array Resultado de la validación
     */
    protected function validarFichaUnicaPorPrograma($numeroFicha, $programaId, $excluirFichaId = null)
    {
        try {
            $query = FichaCaracterizacion::where('ficha', $numeroFicha)
                ->where('programa_formacion_id', $programaId);

            // Excluir la ficha actual si se está actualizando
            if ($excluirFichaId) {
                $query->where('id', '!=', $excluirFichaId);
            }

            $fichaExistente = $query->first();

            if ($fichaExistente) {
                return [
                    'valido' => false,
                    'mensaje' => "Ya existe una ficha con el número '{$numeroFicha}' en este programa de formación."
                ];
            }

            return [
                'valido' => true,
                'mensaje' => 'El número de ficha es válido para este programa.'
            ];

        } catch (\Exception $e) {
            return [
                'valido' => false,
                'mensaje' => 'Error al validar unicidad de la ficha: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Valida las reglas de negocio específicas del SENA.
     *
     * @param array $datos Datos de la ficha
     * @param int|null $excluirFichaId ID de ficha a excluir (para actualizaciones)
     * @return array Resultado de la validación
     */
    protected function validarReglasNegocioSena($datos, $excluirFichaId = null)
    {
        $errores = [];

        try {
            // 1. Validar que las fechas sean laborales (Lunes a Viernes)
            if (isset($datos['fecha_inicio']) && isset($datos['fecha_fin'])) {
                $fechaInicio = Carbon::parse($datos['fecha_inicio']);
                $fechaFin = Carbon::parse($datos['fecha_fin']);

                // Verificar que no sean fines de semana
                if ($fechaInicio->isWeekend()) {
                    $errores[] = 'La fecha de inicio no puede ser un fin de semana (sábado o domingo).';
                }

                if ($fechaFin->isWeekend()) {
                    $errores[] = 'La fecha de fin no puede ser un fin de semana (sábado o domingo).';
                }

                // Verificar que la duración mínima sea de 1 mes
                $duracionDias = $fechaInicio->diffInDays($fechaFin);
                if ($duracionDias < 30) {
                    $errores[] = 'La duración mínima de un programa debe ser de 30 días.';
                }

                // Verificar que la duración máxima no exceda 2 años
                if ($duracionDias > 730) {
                    $errores[] = 'La duración máxima de un programa no puede exceder 2 años (730 días).';
                }
            }

            // 2. Validar horarios según jornada
            if (isset($datos['jornada_id']) && isset($datos['fecha_inicio'])) {
                $validacionJornada = $this->validarHorariosSegunJornada($datos['jornada_id'], $datos['fecha_inicio']);
                if (!$validacionJornada['valido']) {
                    $errores[] = $validacionJornada['mensaje'];
                }
            }

            // 3. Validar que el ambiente pertenezca a la misma sede del programa
            if (isset($datos['ambiente_id']) && isset($datos['sede_id'])) {
                $validacionAmbienteSede = $this->validarAmbientePerteneceASede($datos['ambiente_id'], $datos['sede_id']);
                if (!$validacionAmbienteSede['valido']) {
                    $errores[] = $validacionAmbienteSede['mensaje'];
                }
            }

            // 4. Validar que el instructor pertenezca a la misma regional
            if (isset($datos['instructor_id']) && isset($datos['sede_id'])) {
                $validacionInstructorRegional = $this->validarInstructorPerteneceARegional($datos['instructor_id'], $datos['sede_id']);
                if (!$validacionInstructorRegional['valido']) {
                    $errores[] = $validacionInstructorRegional['mensaje'];
                }
            }

            // 5. Validar límite de aprendices por ficha según programa
            if (isset($datos['programa_formacion_id'])) {
                $validacionLimiteAprendices = $this->validarLimiteAprendicesPorPrograma($datos['programa_formacion_id']);
                if (!$validacionLimiteAprendices['valido']) {
                    $errores[] = $validacionLimiteAprendices['mensaje'];
                }
            }

            if (count($errores) > 0) {
                return [
                    'valido' => false,
                    'mensaje' => implode(' ', $errores)
                ];
            }

            return [
                'valido' => true,
                'mensaje' => 'Todas las reglas de negocio se cumplen correctamente.'
            ];

        } catch (\Exception $e) {
            return [
                'valido' => false,
                'mensaje' => 'Error al validar reglas de negocio: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Valida los horarios según la jornada seleccionada.
     *
     * @param int $jornadaId ID de la jornada
     * @param string $fechaInicio Fecha de inicio
     * @return array Resultado de la validación
     */
    private function validarHorariosSegunJornada($jornadaId, $fechaInicio)
    {
        try {
            $fechaInicio = Carbon::parse($fechaInicio);
            $diaSemana = $fechaInicio->dayOfWeek; // 0 = Domingo, 1 = Lunes, etc.

            // Configuración de jornadas y días permitidos
            $configuracionJornadas = [
                1 => ['nombre' => 'MAÑANA', 'dias_permitidos' => [1, 2, 3, 4, 5]], // Lunes a Viernes
                2 => ['nombre' => 'TARDE', 'dias_permitidos' => [1, 2, 3, 4, 5]], // Lunes a Viernes
                3 => ['nombre' => 'NOCHE', 'dias_permitidos' => [1, 2, 3, 4, 5]], // Lunes a Viernes
                4 => ['nombre' => 'FIN DE SEMANA', 'dias_permitidos' => [6]], // Sábado
                5 => ['nombre' => 'MIXTA', 'dias_permitidos' => [1, 2, 3, 4, 5, 6]], // Lunes a Sábado
            ];

            if (isset($configuracionJornadas[$jornadaId])) {
                $diasPermitidos = $configuracionJornadas[$jornadaId]['dias_permitidos'];
                if (!in_array($diaSemana, $diasPermitidos)) {
                    return [
                        'valido' => false,
                        'mensaje' => "La fecha de inicio no es compatible con la jornada {$configuracionJornadas[$jornadaId]['nombre']}."
                    ];
                }
            }

            return [
                'valido' => true,
                'mensaje' => 'Los horarios son válidos para la jornada seleccionada.'
            ];

        } catch (\Exception $e) {
            return [
                'valido' => false,
                'mensaje' => 'Error al validar horarios: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Valida que el ambiente pertenezca a la misma sede.
     *
     * @param int $ambienteId ID del ambiente
     * @param int $sedeId ID de la sede
     * @return array Resultado de la validación
     */
    private function validarAmbientePerteneceASede($ambienteId, $sedeId)
    {
        try {
            $ambiente = Ambiente::find($ambienteId);
            if (!$ambiente) {
                return [
                    'valido' => false,
                    'mensaje' => 'El ambiente seleccionado no existe.'
                ];
            }

            if ($ambiente->sede_id != $sedeId) {
                return [
                    'valido' => false,
                    'mensaje' => 'El ambiente seleccionado no pertenece a la sede de la ficha.'
                ];
            }

            return [
                'valido' => true,
                'mensaje' => 'El ambiente pertenece a la sede correcta.'
            ];

        } catch (\Exception $e) {
            return [
                'valido' => false,
                'mensaje' => 'Error al validar ambiente: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Valida que el instructor pertenezca a la misma regional.
     *
     * @param int $instructorId ID del instructor
     * @param int $sedeId ID de la sede
     * @return array Resultado de la validación
     */
    private function validarInstructorPerteneceARegional($instructorId, $sedeId)
    {
        try {
            $instructor = Instructor::find($instructorId);
            $sede = \App\Models\Sede::find($sedeId);

            if (!$instructor || !$sede) {
                return [
                    'valido' => false,
                    'mensaje' => 'El instructor o la sede no existen.'
                ];
            }

            if ($instructor->regional_id != $sede->regional_id) {
                return [
                    'valido' => false,
                    'mensaje' => 'El instructor debe pertenecer a la misma regional que la sede de la ficha.'
                ];
            }

            return [
                'valido' => true,
                'mensaje' => 'El instructor pertenece a la regional correcta.'
            ];

        } catch (\Exception $e) {
            return [
                'valido' => false,
                'mensaje' => 'Error al validar instructor: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Valida el límite de aprendices por programa.
     *
     * @param int $programaId ID del programa
     * @return array Resultado de la validación
     */
    private function validarLimiteAprendicesPorPrograma($programaId)
    {
        try {
            $programa = \App\Models\ProgramaFormacion::find($programaId);
            if (!$programa) {
                return [
                    'valido' => false,
                    'mensaje' => 'El programa de formación no existe.'
                ];
            }

            // Límites según el tipo de programa (estos valores pueden configurarse)
            $limitesPorTipo = [
                'TÉCNICO' => 30,
                'TECNÓLOGO' => 25,
                'AUXILIAR' => 35,
                'OPERARIO' => 40
            ];

            $tipoPrograma = $programa->nivel ?? 'TÉCNICO';
            $limiteMaximo = $limitesPorTipo[$tipoPrograma] ?? 30;

            // Contar aprendices actuales en el programa
            $aprendicesActuales = DB::table('aprendiz_fichas_caracterizacion')
                ->join('fichas_caracterizacion', 'aprendiz_fichas_caracterizacion.ficha_id', '=', 'fichas_caracterizacion.id')
                ->where('fichas_caracterizacion.programa_formacion_id', $programaId)
                ->where('fichas_caracterizacion.status', true)
                ->count();

            if ($aprendicesActuales >= $limiteMaximo) {
                return [
                    'valido' => false,
                    'mensaje' => "Se ha alcanzado el límite máximo de {$limiteMaximo} aprendices para este programa de formación."
                ];
            }

            return [
                'valido' => true,
                'mensaje' => 'El límite de aprendices está dentro del rango permitido.'
            ];

        } catch (\Exception $e) {
            return [
                'valido' => false,
                'mensaje' => 'Error al validar límite de aprendices: ' . $e->getMessage()
            ];
        }
    }
}
