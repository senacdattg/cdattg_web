<?php

namespace App\Services;

use App\Models\FichaCaracterizacion;
use App\Models\Ambiente;
use App\Models\Instructor;
use App\Traits\ValidacionesSena;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FichaCaracterizacionValidationService
{
    use ValidacionesSena;

    /**
     * Valida completamente una ficha de caracterización antes de guardarla.
     *
     * @param array $datos Datos de la ficha
     * @param int|null $excluirFichaId ID de ficha a excluir (para actualizaciones)
     * @return array Resultado de la validación
     */
    public function validarFichaCompleta($datos, $excluirFichaId = null)
    {
        $errores = [];
        $advertencias = [];

        try {
            Log::info('Iniciando validación completa de ficha', [
                'datos' => $datos,
                'excluir_ficha_id' => $excluirFichaId,
                'timestamp' => now()
            ]);

            // 1. Validar disponibilidad del ambiente
            if (isset($datos['ambiente_id']) && isset($datos['fecha_inicio']) && isset($datos['fecha_fin'])) {
                $validacionAmbiente = $this->validarDisponibilidadAmbiente(
                    $datos['ambiente_id'],
                    $datos['fecha_inicio'],
                    $datos['fecha_fin'],
                    $excluirFichaId
                );

                if (!$validacionAmbiente['valido']) {
                    $errores[] = $validacionAmbiente['mensaje'];
                }
            }

            // 2. Validar disponibilidad del instructor
            if (isset($datos['instructor_id']) && isset($datos['fecha_inicio']) && isset($datos['fecha_fin'])) {
                $validacionInstructor = $this->validarDisponibilidadInstructor(
                    $datos['instructor_id'],
                    $datos['fecha_inicio'],
                    $datos['fecha_fin'],
                    $excluirFichaId
                );

                if (!$validacionInstructor['valido']) {
                    $errores[] = $validacionInstructor['mensaje'];
                }
            }

            // 3. Validar unicidad de la ficha por programa
            if (isset($datos['ficha']) && isset($datos['programa_formacion_id'])) {
                $validacionFicha = $this->validarFichaUnicaPorPrograma(
                    $datos['ficha'],
                    $datos['programa_formacion_id'],
                    $excluirFichaId
                );

                if (!$validacionFicha['valido']) {
                    $errores[] = $validacionFicha['mensaje'];
                }
            }

            // 4. Validar reglas de negocio específicas del SENA
            $validacionReglas = $this->validarReglasNegocioSena($datos, $excluirFichaId);
            if (!$validacionReglas['valido']) {
                $errores[] = $validacionReglas['mensaje'];
            }

            // 5. Validaciones adicionales específicas
            $validacionesAdicionales = $this->validacionesAdicionales($datos, $excluirFichaId);
            $errores = array_merge($errores, $validacionesAdicionales['errores']);
            $advertencias = array_merge($advertencias, $validacionesAdicionales['advertencias']);

            $resultado = [
                'valido' => count($errores) === 0,
                'errores' => $errores,
                'advertencias' => $advertencias,
                'mensaje' => count($errores) === 0 
                    ? 'Todas las validaciones se cumplieron correctamente.' 
                    : 'Se encontraron errores en la validación.'
            ];

            Log::info('Validación completa de ficha finalizada', [
                'valido' => $resultado['valido'],
                'total_errores' => count($errores),
                'total_advertencias' => count($advertencias),
                'timestamp' => now()
            ]);

            return $resultado;

        } catch (\Exception $e) {
            Log::error('Error en validación completa de ficha', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'timestamp' => now()
            ]);

            return [
                'valido' => false,
                'errores' => ['Error interno en la validación: ' . $e->getMessage()],
                'advertencias' => [],
                'mensaje' => 'Error interno en la validación.'
            ];
        }
    }

    /**
     * Validaciones adicionales específicas del negocio.
     *
     * @param array $datos Datos de la ficha
     * @param int|null $excluirFichaId ID de ficha a excluir
     * @return array Resultado con errores y advertencias
     */
    private function validacionesAdicionales($datos, $excluirFichaId = null)
    {
        $errores = [];
        $advertencias = [];

        try {
            // 1. Validar que no se superpongan horarios de instructores en el mismo ambiente
            if (isset($datos['instructor_id']) && isset($datos['ambiente_id']) && 
                isset($datos['fecha_inicio']) && isset($datos['fecha_fin'])) {
                
                $validacionSuperposicion = $this->validarSuperposicionHorariosAmbiente(
                    $datos['instructor_id'],
                    $datos['ambiente_id'],
                    $datos['fecha_inicio'],
                    $datos['fecha_fin'],
                    $excluirFichaId
                );

                if (!$validacionSuperposicion['valido']) {
                    $errores[] = $validacionSuperposicion['mensaje'];
                }
            }

            // 2. Validar capacidad del ambiente
            if (isset($datos['ambiente_id']) && isset($datos['total_horas'])) {
                $validacionCapacidad = $this->validarCapacidadAmbiente($datos['ambiente_id'], $datos['total_horas']);
                if (!$validacionCapacidad['valido']) {
                    $advertencias[] = $validacionCapacidad['mensaje'];
                }
            }

            // 3. Validar carga horaria del instructor
            if (isset($datos['instructor_id']) && isset($datos['total_horas'])) {
                $validacionCargaHoraria = $this->validarCargaHorariaInstructor($datos['instructor_id'], $datos['total_horas']);
                if (!$validacionCargaHoraria['valido']) {
                    $advertencias[] = $validacionCargaHoraria['mensaje'];
                }
            }

            // 4. Validar fechas según calendario académico del SENA
            if (isset($datos['fecha_inicio']) && isset($datos['fecha_fin'])) {
                $validacionCalendario = $this->validarCalendarioAcademico($datos['fecha_inicio'], $datos['fecha_fin']);
                if (!$validacionCalendario['valido']) {
                    $errores[] = $validacionCalendario['mensaje'];
                }
            }

            return [
                'errores' => $errores,
                'advertencias' => $advertencias
            ];

        } catch (\Exception $e) {
            return [
                'errores' => ['Error en validaciones adicionales: ' . $e->getMessage()],
                'advertencias' => []
            ];
        }
    }

    /**
     * Valida que no se superpongan horarios de instructores en el mismo ambiente.
     */
    private function validarSuperposicionHorariosAmbiente($instructorId, $ambienteId, $fechaInicio, $fechaFin, $excluirFichaId = null)
    {
        try {
            // Buscar otras fichas que usen el mismo ambiente en el mismo horario
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

            if ($excluirFichaId) {
                $query->where('id', '!=', $excluirFichaId);
            }

            $fichasConflictivas = $query->get();

            if ($fichasConflictivas->count() > 0) {
                $fichasConflictivasStr = $fichasConflictivas->pluck('ficha')->implode(', ');
                return [
                    'valido' => false,
                    'mensaje' => "El ambiente ya está siendo usado por otras fichas en el mismo horario: {$fichasConflictivasStr}."
                ];
            }

            return [
                'valido' => true,
                'mensaje' => 'No hay conflictos de horarios en el ambiente.'
            ];

        } catch (\Exception $e) {
            return [
                'valido' => false,
                'mensaje' => 'Error al validar superposición de horarios: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Valida la capacidad del ambiente para el número de horas.
     */
    private function validarCapacidadAmbiente($ambienteId, $totalHoras)
    {
        try {
            $ambiente = Ambiente::find($ambienteId);
            if (!$ambiente) {
                return [
                    'valido' => false,
                    'mensaje' => 'El ambiente no existe.'
                ];
            }

            // Capacidad máxima de horas por semana (configurable)
            $capacidadMaximaSemanal = $ambiente->capacidad_maxima_horas ?? 40;

            if ($totalHoras > $capacidadMaximaSemanal) {
                return [
                    'valido' => false,
                    'mensaje' => "El ambiente tiene una capacidad máxima de {$capacidadMaximaSemanal} horas semanales."
                ];
            }

            return [
                'valido' => true,
                'mensaje' => 'La capacidad del ambiente es adecuada.'
            ];

        } catch (\Exception $e) {
            return [
                'valido' => false,
                'mensaje' => 'Error al validar capacidad del ambiente: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Valida la carga horaria del instructor.
     */
    private function validarCargaHorariaInstructor($instructorId, $totalHoras)
    {
        try {
            // Carga horaria máxima por instructor (configurable)
            $cargaMaximaSemanal = 40; // horas

            if ($totalHoras > $cargaMaximaSemanal) {
                return [
                    'valido' => false,
                    'mensaje' => "El instructor no puede tener una carga horaria superior a {$cargaMaximaSemanal} horas semanales."
                ];
            }

            return [
                'valido' => true,
                'mensaje' => 'La carga horaria del instructor es adecuada.'
            ];

        } catch (\Exception $e) {
            return [
                'valido' => false,
                'mensaje' => 'Error al validar carga horaria del instructor: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Valida las fechas según el calendario académico del SENA.
     */
    private function validarCalendarioAcademico($fechaInicio, $fechaFin)
    {
        try {
            $fechaInicio = \Carbon\Carbon::parse($fechaInicio);
            $fechaFin = \Carbon\Carbon::parse($fechaFin);

            // Validar que no sea en época de vacaciones (diciembre-enero)
            if (($fechaInicio->month === 12 && $fechaInicio->day >= 15) || 
                ($fechaFin->month === 1 && $fechaFin->day <= 15)) {
                return [
                    'valido' => false,
                    'mensaje' => 'Las fechas seleccionadas coinciden con la época de vacaciones del SENA (15 diciembre - 15 enero).'
                ];
            }

            // Validar que no sea en época de exámenes (última semana de cada trimestre)
            $ultimaSemanaTrimestre = [
                3 => [20, 26], // Marzo
                6 => [20, 26], // Junio
                9 => [20, 26], // Septiembre
                12 => [20, 26] // Diciembre
            ];

            foreach ($ultimaSemanaTrimestre as $mes => $rango) {
                if (($fechaInicio->month === $mes && $fechaInicio->day >= $rango[0] && $fechaInicio->day <= $rango[1]) ||
                    ($fechaFin->month === $mes && $fechaFin->day >= $rango[0] && $fechaFin->day <= $rango[1])) {
                    return [
                        'valido' => false,
                        'mensaje' => 'Las fechas seleccionadas coinciden con la época de exámenes del SENA.'
                    ];
                }
            }

            return [
                'valido' => true,
                'mensaje' => 'Las fechas son válidas según el calendario académico del SENA.'
            ];

        } catch (\Exception $e) {
            return [
                'valido' => false,
                'mensaje' => 'Error al validar calendario académico: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Obtiene un resumen de las validaciones realizadas.
     *
     * @param array $resultado Resultado de la validación
     * @return string Resumen en texto
     */
    public function obtenerResumenValidacion($resultado)
    {
        $resumen = "Estado de validación: " . ($resultado['valido'] ? 'VÁLIDA' : 'INVÁLIDA') . "\n";
        
        if (count($resultado['errores']) > 0) {
            $resumen .= "\nErrores encontrados:\n";
            foreach ($resultado['errores'] as $error) {
                $resumen .= "- {$error}\n";
            }
        }

        if (count($resultado['advertencias']) > 0) {
            $resumen .= "\nAdvertencias:\n";
            foreach ($resultado['advertencias'] as $advertencia) {
                $resumen .= "- {$advertencia}\n";
            }
        }

        return $resumen;
    }
}
