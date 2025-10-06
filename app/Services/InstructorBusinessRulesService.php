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
    public function verificarDisponibilidad(Instructor $instructor, array $datosFicha): array
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
                $resultado['razones'][] = 'El instructor excede el límite máximo de fichas activas (' . self::MAX_FICHAS_ACTIVAS . ')';
            }

            // Verificar superposición de fechas
            $conflictos = $this->verificarSuperposicionFechas($instructor, $fechaInicio, $fechaFin);
            if (!empty($conflictos)) {
                $resultado['disponible'] = false;
                $resultado['conflictos'] = $conflictos;
                $resultado['razones'][] = 'El instructor tiene fichas con fechas superpuestas';
            }

            // Verificar carga horaria semanal
            $cargaHoraria = $this->calcularCargaHorariaSemanal($instructor, $fechaInicio, $fechaFin, $datosFicha['horas_semanales'] ?? 0);
            if ($cargaHoraria > self::MAX_HORAS_SEMANA) {
                $resultado['disponible'] = false;
                $resultado['razones'][] = "El instructor excedería la carga horaria máxima semanal ({$cargaHoraria}h > " . self::MAX_HORAS_SEMANA . "h)";
            }

            // Verificar especialidades requeridas
            if (!$this->tieneEspecialidadesRequeridas($instructor, $datosFicha['especialidad_requerida'] ?? null)) {
                $resultado['disponible'] = false;
                $resultado['razones'][] = 'El instructor no tiene las especialidades requeridas para esta ficha';
            }

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
     * Verificar superposición de fechas con fichas existentes
     */
    public function verificarSuperposicionFechas(Instructor $instructor, Carbon $fechaInicio, Carbon $fechaFin): array
    {
        $conflictos = [];

        $fichasExistentes = $instructor->instructorFichas()
            ->with('ficha')
            ->whereHas('ficha', function($q) {
                $q->where('status', true);
            })
            ->get();

        foreach ($fichasExistentes as $instructorFicha) {
            $ficha = $instructorFicha->ficha;
            $fechaInicioExistente = Carbon::parse($ficha->fecha_inicio);
            $fechaFinExistente = Carbon::parse($ficha->fecha_fin);

            // Verificar si hay superposición
            if ($this->haySuperposicion($fechaInicio, $fechaFin, $fechaInicioExistente, $fechaFinExistente)) {
                $conflictos[] = [
                    'ficha_id' => $ficha->id,
                    'ficha_numero' => $ficha->ficha,
                    'fecha_inicio' => $ficha->fecha_inicio,
                    'fecha_fin' => $ficha->fecha_fin,
                    'programa' => $ficha->programaFormacion->nombre ?? 'Sin programa'
                ];
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
     */
    public function calcularCargaHorariaSemanal(Instructor $instructor, Carbon $fechaInicio, Carbon $fechaFin, int $horasNuevaFicha = 0): int
    {
        // Obtener fichas activas en el período
        $fichasActivas = $instructor->instructorFichas()
            ->whereHas('ficha', function($q) use ($fechaInicio, $fechaFin) {
                $q->where('status', true)
                  ->where(function($query) use ($fechaInicio, $fechaFin) {
                      $query->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
                            ->orWhereBetween('fecha_fin', [$fechaInicio, $fechaFin])
                            ->orWhere(function($subQuery) use ($fechaInicio, $fechaFin) {
                                $subQuery->where('fecha_inicio', '<=', $fechaInicio)
                                         ->where('fecha_fin', '>=', $fechaFin);
                            });
                  });
            })
            ->get();

        $totalHoras = 0;
        
        // Sumar horas de fichas existentes
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

        $especialidades = $instructor->especialidades ?? [];
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
                $resultado['errores'][] = 'El instructor no cumple con la experiencia mínima requerida (' . self::EXPERIENCIA_MINIMA . ' año)';
            }

            // Verificar que el instructor esté en la misma regional que la ficha
            if (isset($datosFicha['regional_id']) && $instructor->regional_id != $datosFicha['regional_id']) {
                $resultado['valido'] = false;
                $resultado['errores'][] = 'El instructor debe pertenecer a la misma regional que la ficha';
            }

            // Verificar disponibilidad general
            $disponibilidad = $this->verificarDisponibilidad($instructor, $datosFicha);
            if (!$disponibilidad['disponible']) {
                $resultado['valido'] = false;
                $resultado['errores'] = array_merge($resultado['errores'], $disponibilidad['razones']);
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
}
