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
                    ? 'Todas las validaciones pasaron correctamente.' 
                    : 'Se encontraron errores en la validación.'
            ];

            Log::info('Validación completa de ficha finalizada', [
                'resultado' => $resultado,
                'timestamp' => now()
            ]);

            return $resultado;

        } catch (\Exception $e) {
            Log::error('Error en validación completa de ficha', [
                'error' => $e->getMessage(),
                'datos' => $datos,
                'file' => $e->getFile(),
                'line' => $e->getLine()
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
     * Validaciones adicionales específicas para el SENA.
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
            // 1. Validar que el programa esté activo
            if (isset($datos['programa_formacion_id'])) {
                $programa = \App\Models\ProgramaFormacion::find($datos['programa_formacion_id']);
                if ($programa && isset($programa->status) && !$programa->status) {
                    $errores[] = 'El programa de formación seleccionado está inactivo.';
                }
            }

            // 2. Validar que la sede esté activa
            if (isset($datos['sede_id'])) {
                $sede = \App\Models\Sede::find($datos['sede_id']);
                if ($sede && isset($sede->status) && !$sede->status) {
                    $errores[] = 'La sede seleccionada está inactiva.';
                }
            }

            // 3. Validar que el ambiente esté activo
            if (isset($datos['ambiente_id'])) {
                $ambiente = Ambiente::find($datos['ambiente_id']);
                if ($ambiente && isset($ambiente->status) && !$ambiente->status) {
                    $errores[] = 'El ambiente seleccionado está inactivo.';
                }
            }

            // 4. Validar que el instructor esté activo
            if (isset($datos['instructor_id'])) {
                $instructor = Instructor::find($datos['instructor_id']);
                if ($instructor && isset($instructor->status) && !$instructor->status) {
                    $errores[] = 'El instructor seleccionado está inactivo.';
                }
            }

            // 5. Validar duración mínima según modalidad
            if (isset($datos['modalidad_formacion_id']) && isset($datos['fecha_inicio']) && isset($datos['fecha_fin'])) {
                $duracionDias = \Carbon\Carbon::parse($datos['fecha_inicio'])->diffInDays(\Carbon\Carbon::parse($datos['fecha_fin']));
                
                // Duración mínima según modalidad
                $duracionesMinimas = [
                    1 => 30,  // Presencial
                    2 => 60,  // Virtual
                    3 => 90,  // Mixta
                ];

                $duracionMinima = $duracionesMinimas[$datos['modalidad_formacion_id']] ?? 30;
                
                if ($duracionDias < $duracionMinima) {
                    $advertencias[] = "La duración del programa es menor a lo recomendado para esta modalidad ({$duracionMinima} días mínimo).";
                }
            }

            // 6. Validar que no se superpongan fechas con programas similares
            if (isset($datos['programa_formacion_id']) && isset($datos['fecha_inicio']) && isset($datos['fecha_fin'])) {
                $programasSimilares = FichaCaracterizacion::where('programa_formacion_id', $datos['programa_formacion_id'])
                    ->where('status', true)
                    ->where('id', '!=', $excluirFichaId)
                    ->where(function ($q) use ($datos) {
                        $q->whereBetween('fecha_inicio', [$datos['fecha_inicio'], $datos['fecha_fin']])
                            ->orWhereBetween('fecha_fin', [$datos['fecha_inicio'], $datos['fecha_fin']])
                            ->orWhere(function ($subQuery) use ($datos) {
                                $subQuery->where('fecha_inicio', '<=', $datos['fecha_inicio'])
                                    ->where('fecha_fin', '>=', $datos['fecha_fin']);
                            });
                    })
                    ->count();

                if ($programasSimilares > 0) {
                    $advertencias[] = 'Ya existen programas similares en las fechas seleccionadas. Se recomienda verificar la disponibilidad.';
                }
            }

            // 7. Validar que el instructor no tenga más de X fichas en el mismo período
            if (isset($datos['instructor_id']) && isset($datos['fecha_inicio']) && isset($datos['fecha_fin'])) {
                $fichasInstructor = FichaCaracterizacion::where('instructor_id', $datos['instructor_id'])
                    ->where('status', true)
                    ->where('id', '!=', $excluirFichaId)
                    ->where(function ($q) use ($datos) {
                        $q->whereBetween('fecha_inicio', [$datos['fecha_inicio'], $datos['fecha_fin']])
                            ->orWhereBetween('fecha_fin', [$datos['fecha_inicio'], $datos['fecha_fin']]);
                    })
                    ->count();

                if ($fichasInstructor >= 2) {
                    $advertencias[] = 'El instructor ya tiene múltiples fichas asignadas en el período seleccionado.';
                }
            }

            // 8. Validar que el ambiente no esté en mantenimiento
            if (isset($datos['ambiente_id'])) {
                $ambiente = Ambiente::find($datos['ambiente_id']);
                if ($ambiente && isset($ambiente->estado) && $ambiente->estado === 'MANTENIMIENTO') {
                    $errores[] = 'El ambiente seleccionado está en mantenimiento y no está disponible.';
                }
            }

            // 9. Validar que las fechas no sean muy lejanas en el futuro
            if (isset($datos['fecha_inicio'])) {
                $fechaInicio = \Carbon\Carbon::parse($datos['fecha_inicio']);
                $fechaActual = \Carbon\Carbon::now();
                $diferenciaMeses = $fechaActual->diffInMonths($fechaInicio);

                if ($diferenciaMeses > 12) {
                    $advertencias[] = 'La fecha de inicio está muy lejana en el futuro (más de 12 meses).';
                }
            }

            // 10. Validar que el programa tenga instructores disponibles
            if (isset($datos['programa_formacion_id'])) {
                $instructoresDisponibles = Instructor::where('status', true)
                    ->whereHas('competencias', function ($q) use ($datos) {
                        $q->whereHas('programas', function ($subQ) use ($datos) {
                            $subQ->where('programa_id', $datos['programa_formacion_id']);
                        });
                    })
                    ->count();

                if ($instructoresDisponibles === 0) {
                    $advertencias[] = 'No se encontraron instructores con las competencias requeridas para este programa.';
                }
            }

            return [
                'errores' => $errores,
                'advertencias' => $advertencias
            ];

        } catch (\Exception $e) {
            Log::error('Error en validaciones adicionales', [
                'error' => $e->getMessage(),
                'datos' => $datos,
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return [
                'errores' => ['Error en validaciones adicionales: ' . $e->getMessage()],
                'advertencias' => []
            ];
        }
    }

    /**
     * Valida si una ficha puede ser eliminada según las reglas de negocio.
     *
     * @param int $fichaId ID de la ficha
     * @return array Resultado de la validación
     */
    public function validarEliminacionFicha($fichaId)
    {
        try {
            $ficha = FichaCaracterizacion::find($fichaId);
            
            if (!$ficha) {
                return [
                    'valido' => false,
                    'mensaje' => 'La ficha no existe.'
                ];
            }

            $errores = [];

            // 1. Verificar si tiene aprendices asignados
            if ($ficha->tieneAprendices()) {
                $errores[] = 'No se puede eliminar la ficha porque tiene aprendices asignados.';
            }

            // 2. Verificar si tiene asistencias registradas
            $tieneAsistencias = DB::table('asistencia_aprendices')
                ->join('aprendiz_fichas_caracterizacion', 'asistencia_aprendices.aprendiz_id', '=', 'aprendiz_fichas_caracterizacion.aprendiz_id')
                ->where('aprendiz_fichas_caracterizacion.ficha_id', $fichaId)
                ->exists();

            if ($tieneAsistencias) {
                $errores[] = 'No se puede eliminar la ficha porque tiene asistencias registradas.';
            }

            // 3. Verificar si ya comenzó el programa
            if ($ficha->fecha_inicio && $ficha->fecha_inicio <= now()) {
                $errores[] = 'No se puede eliminar la ficha porque el programa ya ha comenzado.';
            }

            // 4. Verificar si tiene instructores asignados
            if ($ficha->instructorFicha()->count() > 0) {
                $errores[] = 'No se puede eliminar la ficha porque tiene instructores asignados.';
            }

            return [
                'valido' => count($errores) === 0,
                'mensaje' => count($errores) === 0 
                    ? 'La ficha puede ser eliminada.' 
                    : implode(' ', $errores),
                'errores' => $errores
            ];

        } catch (\Exception $e) {
            Log::error('Error al validar eliminación de ficha', [
                'ficha_id' => $fichaId,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return [
                'valido' => false,
                'mensaje' => 'Error interno al validar eliminación: ' . $e->getMessage(),
                'errores' => ['Error interno en la validación']
            ];
        }
    }

    /**
     * Valida si una ficha puede ser editada según las reglas de negocio.
     *
     * @param int $fichaId ID de la ficha
     * @return array Resultado de la validación
     */
    public function validarEdicionFicha($fichaId)
    {
        try {
            $ficha = FichaCaracterizacion::find($fichaId);
            
            if (!$ficha) {
                return [
                    'valido' => false,
                    'mensaje' => 'La ficha no existe.'
                ];
            }

            $errores = [];
            $advertencias = [];

            // 1. Verificar si tiene aprendices asignados
            if ($ficha->tieneAprendices()) {
                $advertencias[] = 'La ficha tiene aprendices asignados. Algunos campos pueden tener restricciones de edición.';
            }

            // 2. Verificar si ya comenzó el programa
            if ($ficha->fecha_inicio && $ficha->fecha_inicio <= now()) {
                $advertencias[] = 'El programa ya ha comenzado. Las fechas no pueden ser modificadas.';
            }

            // 3. Verificar si tiene asistencias registradas
            $tieneAsistencias = DB::table('asistencia_aprendices')
                ->join('aprendiz_fichas_caracterizacion', 'asistencia_aprendices.aprendiz_id', '=', 'aprendiz_fichas_caracterizacion.aprendiz_id')
                ->where('aprendiz_fichas_caracterizacion.ficha_id', $fichaId)
                ->exists();

            if ($tieneAsistencias) {
                $advertencias[] = 'La ficha tiene asistencias registradas. Algunos cambios pueden afectar los registros existentes.';
            }

            return [
                'valido' => true,
                'mensaje' => 'La ficha puede ser editada.',
                'errores' => $errores,
                'advertencias' => $advertencias
            ];

        } catch (\Exception $e) {
            Log::error('Error al validar edición de ficha', [
                'ficha_id' => $fichaId,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return [
                'valido' => false,
                'mensaje' => 'Error interno al validar edición: ' . $e->getMessage(),
                'errores' => ['Error interno en la validación'],
                'advertencias' => []
            ];
        }
    }
}