<?php

namespace App\Services;

use App\Models\Instructor;
use App\Models\FichaCaracterizacion;
use App\Models\InstructorFichaCaracterizacion;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class InstructorBusinessRulesService
{
    /**
     * Límite máximo de fichas activas por instructor según reglas SENA
     */
    const MAX_FICHAS_ACTIVAS = 5;

    /**
     * Experiencia mínima requerida para instructores
     */
    const EXPERIENCIA_MINIMA = 1;

    /**
     * Horas máximas por semana para un instructor
     */
    const MAX_HORAS_SEMANA = 48;

    /**
     * Verificar disponibilidad del instructor para una nueva ficha
     */
    public function verificarDisponibilidad(Instructor $instructor, array $datosFicha, ?int $fichaIdActual = null): array
    {
        $resultado = [
            'disponible' => true,
            'razones' => [],
            'conflictos' => []
        ];

        try {
            // Verificar si el instructor está activo
            if (!$instructor->status) {
                $resultado['disponible'] = false;
                $resultado['razones'][] = 'El instructor está inactivo';
                return $resultado;
            }

            $fechaInicio = Carbon::parse($datosFicha['fecha_inicio']);
            $fechaFin = Carbon::parse($datosFicha['fecha_fin']);

            // Verificar límite de fichas activas
            if ($this->excedeLimiteFichasActivas($instructor)) {
                $resultado['disponible'] = false;
                $fichasActivas = $this->obtenerFichasActivas($instructor);
                $ejemploFichas = $fichasActivas->take(2)->map(function($ficha) {
                    return "Ficha {$ficha->ficha} ({$ficha->programaFormacion->nombre})";
                })->implode(', ');
                $resultado['razones'][] = "El instructor excede el límite máximo de fichas activas (" . count($fichasActivas) . "/" . self::MAX_FICHAS_ACTIVAS . "). Ejemplo: {$ejemploFichas}";
            }

            // Verificar superposición de fechas (considerando jornadas y días de la semana)
            $jornadaId = $datosFicha['jornada_id'] ?? null;
            $diasFormacion = $datosFicha['dias_formacion'] ?? [];
            $conflictos = $this->verificarSuperposicionFechas($instructor, $fechaInicio, $fechaFin, $jornadaId, $diasFormacion, $fichaIdActual);
            if (!empty($conflictos)) {
                $resultado['disponible'] = false;
                $resultado['conflictos'] = $conflictos;
                $ejemploConflicto = $conflictos[0];
                $programaNombre = $ejemploConflicto['programa'] ?? 'Sin programa';
                $jornadaInfo = isset($ejemploConflicto['jornada']) ? " (Jornada: {$ejemploConflicto['jornada']})" : '';
                $diasInfo = isset($ejemploConflicto['dias_conflicto']) ? " en los días: {$ejemploConflicto['dias_conflicto']}" : '';
                $resultado['razones'][] = "El instructor tiene fichas con fechas superpuestas en la misma jornada{$diasInfo}. Ejemplo: Ficha {$ejemploConflicto['ficha_numero']} ({$programaNombre}){$jornadaInfo} del " . Carbon::parse($ejemploConflicto['fecha_inicio'])->format('d/m/Y') . " al " . \Carbon\Carbon::parse($ejemploConflicto['fecha_fin'])->format('d/m/Y');
            }

            // VALIDACIÓN DESHABILITADA: Verificar carga horaria semanal
            // Esta validación estaba comparando incorrectamente horas totales vs límite semanal
            // $cargaHoraria = $this->calcularCargaHorariaSemanal($instructor, $fechaInicio, $fechaFin, $datosFicha['horas_semanales'] ?? 0, $jornadaId);
            // if ($cargaHoraria > self::MAX_HORAS_SEMANA) {
            //     $resultado['disponible'] = false;
            //     $resultado['razones'][] = "El instructor excedería la carga horaria máxima semanal en esta jornada ({$cargaHoraria}h > " . self::MAX_HORAS_SEMANA . "h). Ejemplo: Actualmente tiene " . ($cargaHoraria - ($datosFicha['horas_semanales'] ?? 0)) . "h semanales asignadas en la misma jornada";
            // }

            // NOTA: Validación de especialidades deshabilitada por solicitud del usuario
            // No se valida la especialidad del instructor para la ficha

        } catch (\Exception $e) {
            Log::error('Error verificando disponibilidad del instructor', [
                'instructor_id' => $instructor->id,
                'error' => $e->getMessage(),
                'datos_ficha' => $datosFicha
            ]);
            
            $resultado['disponible'] = false;
            $resultado['razones'][] = 'Error interno al verificar disponibilidad';
        }

        return $resultado;
    }

    /**
     * Verificar si el instructor excede el límite de fichas activas
     */
    public function excedeLimiteFichasActivas(Instructor $instructor): bool
    {
        $fichasActivas = $instructor->instructorFichas()
            ->whereHas('ficha', function($q) {
                $q->where('status', true)
                  ->where('fecha_fin', '>=', now()->toDateString());
            })
            ->count();

        return $fichasActivas >= self::MAX_FICHAS_ACTIVAS;
    }

    /**
     * Verificar superposición de fechas con fichas existentes considerando jornada y días de la semana
     * 
     * @param Instructor $instructor
     * @param Carbon $fechaInicio
     * @param Carbon $fechaFin
     * @param int|null $jornadaId Jornada de la ficha a asignar
     * @param array $diasFormacion Días de formación de la nueva asignación (opcional)
     * @param int|null $fichaIdActual ID de la ficha actual (para excluir si es instructor principal)
     * @return array
     */
    public function verificarSuperposicionFechas(Instructor $instructor, Carbon $fechaInicio, Carbon $fechaFin, ?int $jornadaId = null, array $diasFormacion = [], ?int $fichaIdActual = null): array
    {
        $conflictos = [];

        $fichasExistentes = $instructor->instructorFichas()
            ->with(['ficha.jornadaFormacion', 'instructorFichaDias.dia'])
            ->whereHas('ficha', function($q) {
                $q->where('status', true);
            })
            ->get();

        // Obtener IDs de los días de formación de la nueva asignación
        $diasNuevos = collect($diasFormacion)->pluck('dia_id')->filter()->toArray();

        foreach ($fichasExistentes as $instructorFicha) {
            $ficha = $instructorFicha->ficha;
            
            // Si es la misma ficha y el instructor es el instructor principal, NO es conflicto
            if ($fichaIdActual && $ficha->id == $fichaIdActual && $ficha->instructor_id == $instructor->id) {
                continue; // No es conflicto, es el instructor principal de la misma ficha
            }
            
            $fechaInicioExistente = Carbon::parse($ficha->fecha_inicio);
            $fechaFinExistente = Carbon::parse($ficha->fecha_fin);

            // Verificar si hay superposición de fechas
            if ($this->haySuperposicion($fechaInicio, $fechaFin, $fechaInicioExistente, $fechaFinExistente)) {
                // Si las jornadas son diferentes, NO hay conflicto (puede estar en mañana y tarde el mismo día)
                if ($jornadaId && $ficha->jornada_id && $jornadaId !== $ficha->jornada_id) {
                    continue; // No es conflicto, diferente jornada
                }
                
                // Si hay días de formación, verificar si se solapan
                if (!empty($diasNuevos)) {
                    $diasExistentes = $instructorFicha->instructorFichaDias->pluck('dia_id')->toArray();
                    
                    // Verificar si hay días en común
                    $diasEnComun = array_intersect($diasNuevos, $diasExistentes);
                    
                    // Si NO hay días en común, NO es conflicto
                    if (empty($diasEnComun)) {
                        continue;
                    }
                    
                    // Si hay días en común, registrar conflicto con detalle
                    $diasNombres = $instructorFicha->instructorFichaDias
                        ->whereIn('dia_id', $diasEnComun)
                        ->pluck('dia.name')
                        ->filter()
                        ->implode(', ');
                        
                    $conflictos[] = [
                        'ficha_id' => $ficha->id,
                        'ficha_numero' => $ficha->ficha,
                        'fecha_inicio' => $ficha->fecha_inicio,
                        'fecha_fin' => $ficha->fecha_fin,
                        'programa' => $ficha->programaFormacion->nombre ?? 'Sin programa',
                        'jornada' => $ficha->jornadaFormacion->jornada ?? 'Sin jornada',
                        'dias_conflicto' => $diasNombres
                    ];
                } else {
                    // Si no se especifican días, validar solo por fechas y jornada
                    $conflictos[] = [
                        'ficha_id' => $ficha->id,
                        'ficha_numero' => $ficha->ficha,
                        'fecha_inicio' => $ficha->fecha_inicio,
                        'fecha_fin' => $ficha->fecha_fin,
                        'programa' => $ficha->programaFormacion->nombre ?? 'Sin programa',
                        'jornada' => $ficha->jornadaFormacion->jornada ?? 'Sin jornada'
                    ];
                }
            }
        }

        return $conflictos;
    }

    /**
     * Verificar si dos rangos de fechas se superponen
     */
    protected function haySuperposicion(Carbon $inicio1, Carbon $fin1, Carbon $inicio2, Carbon $fin2): bool
    {
        return $inicio1->lte($fin2) && $fin1->gte($inicio2);
    }

    /**
     * Calcular carga horaria semanal del instructor
     * 
     * @param Instructor $instructor
     * @param Carbon $fechaInicio
     * @param Carbon $fechaFin
     * @param int $horasNuevaFicha Horas de la nueva ficha
     * @param int|null $jornadaId Jornada de la nueva ficha (para filtrar solo misma jornada)
     * @return int Total de horas semanales
     */
    public function calcularCargaHorariaSemanal(Instructor $instructor, Carbon $fechaInicio, Carbon $fechaFin, int $horasNuevaFicha = 0, ?int $jornadaId = null): int
    {
        // Obtener fichas activas en el período
        $fichasActivas = $instructor->instructorFichas()
            ->whereHas('ficha', function($q) use ($fechaInicio, $fechaFin, $jornadaId) {
                $q->where('status', true)
                  ->where(function($query) use ($fechaInicio, $fechaFin) {
                      $query->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
                            ->orWhereBetween('fecha_fin', [$fechaInicio, $fechaFin])
                            ->orWhere(function($subQuery) use ($fechaInicio, $fechaFin) {
                                $subQuery->where('fecha_inicio', '<=', $fechaInicio)
                                         ->where('fecha_fin', '>=', $fechaFin);
                            });
                  });
                
                // Filtrar solo por la misma jornada si se proporciona
                if ($jornadaId) {
                    $q->where('jornada_id', $jornadaId);
                }
            })
            ->get();

        $totalHoras = 0;
        
        // Sumar horas de fichas existentes en la misma jornada
        foreach ($fichasActivas as $instructorFicha) {
            $totalHoras += $this->calcularHorasSemanalesFicha($instructorFicha->ficha);
        }

        // Agregar horas de la nueva ficha
        $totalHoras += $horasNuevaFicha;

        return $totalHoras;
    }

    /**
     * Calcular horas semanales de una ficha específica
     */
    protected function calcularHorasSemanalesFicha(FichaCaracterizacion $ficha): int
    {
        // Calcular días de formación por semana
        $diasFormacion = $ficha->diasFormacion->count();
        
        // Calcular horas por día (asumiendo 8 horas por día como estándar SENA)
        $horasPorDia = 8;
        
        return $diasFormacion * $horasPorDia;
    }

    /**
     * Verificar si el instructor tiene las especialidades requeridas
     */
    public function tieneEspecialidadesRequeridas(Instructor $instructor, ?string $especialidadRequerida): bool
    {
        if (!$especialidadRequerida) {
            return true; // Si no hay especialidad requerida, cualquier instructor puede tomar la ficha
        }

        $especialidades = is_array($instructor->especialidades) 
            ? $instructor->especialidades 
            : (is_string($instructor->especialidades) ? json_decode($instructor->especialidades, true) : []);
        $especialidadPrincipal = $especialidades['principal'] ?? null;
        $especialidadesSecundarias = $especialidades['secundarias'] ?? [];

        // Verificar si la especialidad requerida coincide con la principal o alguna secundaria
        if ($especialidadPrincipal === $especialidadRequerida) {
            return true;
        }

        return in_array($especialidadRequerida, $especialidadesSecundarias);
    }

    /**
     * Validar que el instructor tenga experiencia mínima
     */
    public function validarExperienciaMinima(Instructor $instructor): bool
    {
        $anosExperiencia = $instructor->anos_experiencia ?? 0;
        return $anosExperiencia >= self::EXPERIENCIA_MINIMA;
    }

    /**
     * Obtener instructores disponibles para una ficha específica
     */
    public function obtenerInstructoresDisponibles(array $criterios): array
    {
        $fechaInicio = Carbon::parse($criterios['fecha_inicio']);
        $fechaFin = Carbon::parse($criterios['fecha_fin']);
        $especialidadRequerida = $criterios['especialidad_requerida'] ?? null;
        $regionalId = $criterios['regional_id'] ?? null;

        $query = Instructor::with(['persona', 'regional'])
            ->where('status', true);

        // Filtrar por regional
        if ($regionalId) {
            $query->where('regional_id', $regionalId);
        }

        // Filtrar por especialidad si se especifica
        if ($especialidadRequerida) {
            $query->where(function($q) use ($especialidadRequerida) {
                $q->whereJsonContains('especialidades->principal', $especialidadRequerida)
                  ->orWhereJsonContains('especialidades->secundarias', $especialidadRequerida);
            });
        }

        $instructores = $query->get();
        $disponibles = [];

        foreach ($instructores as $instructor) {
            $disponibilidad = $this->verificarDisponibilidad($instructor, $criterios);
            
            if ($disponibilidad['disponible']) {
                $disponibles[] = [
                    'instructor' => $instructor,
                    'disponibilidad' => $disponibilidad
                ];
            }
        }

        return $disponibles;
    }

    /**
     * Validar reglas específicas del SENA para asignación de fichas
     */
    public function validarReglasSENA(Instructor $instructor, array $datosFicha): array
    {
        $resultado = [
            'valido' => true,
            'errores' => [],
            'advertencias' => []
        ];

        try {
            // Verificar experiencia mínima
            if (!$this->validarExperienciaMinima($instructor)) {
                $resultado['valido'] = false;
                $experienciaActual = $instructor->anos_experiencia ?? 0;
                $resultado['errores'][] = "El instructor no cumple con la experiencia mínima requerida ({$experienciaActual}/" . self::EXPERIENCIA_MINIMA . " años). Ejemplo: Necesita al menos " . self::EXPERIENCIA_MINIMA . " año de experiencia";
            }

            // Verificar que el instructor esté en la misma regional que la ficha
            if (isset($datosFicha['regional_id']) && $instructor->regional_id != $datosFicha['regional_id']) {
                $resultado['valido'] = false;
                $regionalInstructor = $instructor->regional->nombre ?? 'Sin regional';
                $regionalFicha = $datosFicha['regional_nombre'] ?? 'Sin especificar';
                $resultado['errores'][] = "El instructor debe pertenecer a la misma regional que la ficha. Instructor: {$regionalInstructor}, Ficha: {$regionalFicha}";
            }

            // Verificar disponibilidad general (solo si no hay errores previos para evitar duplicados)
            if ($resultado['valido']) {
                $disponibilidad = $this->verificarDisponibilidad($instructor, $datosFicha);
                if (!$disponibilidad['disponible']) {
                    $resultado['valido'] = false;
                    $resultado['errores'] = array_merge($resultado['errores'], $disponibilidad['razones']);
                }
            }

            // Advertencias adicionales
            if ($instructor->instructorFichas()->count() >= 4) {
                $resultado['advertencias'][] = 'El instructor ya tiene 4 fichas asignadas, considere la carga de trabajo';
            }

            if (($instructor->anos_experiencia ?? 0) < 3) {
                $resultado['advertencias'][] = 'Instructor con poca experiencia, considere asignar fichas básicas';
            }

        } catch (\Exception $e) {
            Log::error('Error validando reglas SENA', [
                'instructor_id' => $instructor->id,
                'error' => $e->getMessage(),
                'datos_ficha' => $datosFicha
            ]);
            
            $resultado['valido'] = false;
            $resultado['errores'][] = 'Error interno al validar reglas de negocio';
        }

        return $resultado;
    }

    /**
     * Obtener estadísticas de carga de trabajo por instructor
     */
    public function obtenerEstadisticasCargaTrabajo(): array
    {
        $instructores = Instructor::with(['instructorFichas.ficha'])
            ->where('status', true)
            ->get();

        $estadisticas = [];

        foreach ($instructores as $instructor) {
            $fichasActivas = $instructor->instructorFichas()
                ->whereHas('ficha', function($q) {
                    $q->where('status', true)
                      ->where('fecha_fin', '>=', now()->toDateString());
                })
                ->count();

            $totalHoras = $instructor->instructorFichas()
                ->whereHas('ficha', function($q) {
                    $q->where('status', true)
                      ->where('fecha_fin', '>=', now()->toDateString());
                })
                ->sum('total_horas_instructor');

            $estadisticas[] = [
                'instructor_id' => $instructor->id,
                'nombre' => $instructor->nombre_completo,
                'fichas_activas' => $fichasActivas,
                'total_horas' => $totalHoras,
                'carga_alta' => $fichasActivas >= 4 || $totalHoras >= 200,
                'disponible_para_mas' => $fichasActivas < self::MAX_FICHAS_ACTIVAS
            ];
        }

        return $estadisticas;
    }

    /**
     * Obtener fichas activas del instructor
     */
    private function obtenerFichasActivas(Instructor $instructor)
    {
        return $instructor->instructorFichas()
            ->with(['ficha.programaFormacion'])
            ->whereHas('ficha', function($q) {
                $q->where('status', true)
                  ->where('fecha_fin', '>=', now()->toDateString());
            })
            ->get()
            ->pluck('ficha');
    }

    /**
     * Obtener especialidades del instructor en formato legible
     */
    private function obtenerEspecialidadesInstructor(Instructor $instructor): string
    {
        $especialidades = [];
        
        if ($instructor->especialidades) {
            $data = is_array($instructor->especialidades) 
                ? $instructor->especialidades
                : (is_string($instructor->especialidades) ? json_decode($instructor->especialidades, true) : []);
            
            if (isset($data['principal']) && $data['principal']) {
                $especialidades[] = $data['principal'] . ' (Principal)';
            }
            
            if (isset($data['secundarias']) && is_array($data['secundarias'])) {
                foreach ($data['secundarias'] as $secundaria) {
                    $especialidades[] = $secundaria . ' (Secundaria)';
                }
            }
        }
        
        return empty($especialidades) ? 'Ninguna' : implode(', ', $especialidades);
    }
}
