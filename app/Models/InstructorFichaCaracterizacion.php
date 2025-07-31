<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
// Usar la clase Carbon para manejo de fechas y horas
use Carbon\Carbon;


class InstructorFichaCaracterizacion extends Model
{
    protected $table = "instructor_fichas_caracterizacion";

    protected $fillable = [
        "instructor_id",
        "ficha_id",
        "fecha_inicio",
        "fecha_fin",
        "total_horas_ficha"
    ];

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(Instructor::class, 'instructor_id');
    }

    /**
     * Get the asistenciaAprendices that owns the InstructorFichaCaracterizacion
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function asistenciaAprendices(): BelongsTo
    {
        return $this->belongsTo(asistenciaAprendices::class, 'instructor_ficha_id');
    }

    public function ficha(): BelongsTo
    {
        return $this->belongsTo(FichaCaracterizacion::class, 'ficha_id');
    }

    /**
     * Relación con los días de formación de la ficha a través de la tabla intermedia.
     * Permite acceder a los días de formación asociados a esta relación instructor-ficha.
     */
    public function instructorFichaDias()
    {
        return $this->hasMany(InstructorFichaDias::class, 'instructor_ficha_id');
    }

    /**
     * Obtiene la próxima clase basada en la fecha y hora actual
     *
     * @return array|null Array con 'hora_inicio', 'hora_fin', 'dia_nombre', 'fecha_proxima' o null si no hay próxima clase
     */
    public function obtenerProximaClase()
    {
        $horaActual = now();
        $diaActual = $horaActual->dayOfWeek; // 0 = Domingo, 1 = Lunes, etc.

        // Mapeo de días: 0=Domingo->18, 1=Lunes->12, 2=Martes->13, etc.
        $diaIdActual = ($diaActual == 0) ? 18 : $diaActual + 11;

        // Obtener todos los días de formación ordenados por día
        $diasFormacion = $this->instructorFichaDias()
            ->orderBy('dia_id')
            ->get();

        if ($diasFormacion->isEmpty()) {
            return null;
        }

        // Buscar la próxima clase de hoy
        $claseHoy = $diasFormacion->where('dia_id', $diaIdActual)->first();

        if ($claseHoy) {
            $horaInicio = Carbon::parse($claseHoy->hora_inicio);
            $horaFin = Carbon::parse($claseHoy->hora_fin);

            // Si la clase de hoy ya terminó, buscar la próxima clase
            if ($horaActual->greaterThan($horaFin)) {
                return $this->buscarProximaClaseSemana($diasFormacion, $diaActual);
            }

            // Si la clase de hoy aún no ha comenzado o está en curso
            return [
                'hora_inicio' => $claseHoy->hora_inicio,
                'hora_fin' => $claseHoy->hora_fin,
                'dia_nombre' => $this->obtenerNombreDia($claseHoy->dia_id),
                'dia_id' => $clase->dia_id,
                'fecha_proxima' => $horaActual->format('Y-m-d'),
                'es_hoy' => true
            ];
        }

        // Si no hay clase hoy, buscar la próxima
        return $this->buscarProximaClaseSemana($diasFormacion, $diaActual);
    }

    /**
     * Busca la próxima clase en la semana
     */
    private function buscarProximaClaseSemana($diasFormacion, $diaActual)
    {
        $diasSemana = [1, 2, 3, 4, 5, 6, 0]; // Lunes a Domingo
        $diaActualIndex = array_search($diaActual, $diasSemana);

        // Buscar desde el día siguiente
        for ($i = 1; $i <= 7; $i++) {
            $diaIndex = ($diaActualIndex + $i) % 7;
            $diaSemana = $diasSemana[$diaIndex];
            $diaId = ($diaSemana == 0) ? 18 : $diaSemana + 11;

            $clase = $diasFormacion->where('dia_id', $diaId)->first();

            if ($clase) {
                $fechaProxima = now()->addDays($i);

                return [
                    'hora_inicio' => $clase->hora_inicio,
                    'hora_fin' => $clase->hora_fin,
                    'dia_nombre' => $this->obtenerNombreDia($clase->dia_id),
                    'dia_id' => $clase->dia_id,
                    'fecha_proxima' => $fechaProxima->format('Y-m-d'),
                    'es_hoy' => false,
                    'dias_restantes' => $i
                ];
            }
        }

        return null;
    }

    /**
     * Obtiene el nombre del día basado en el ID
     */
    private function obtenerNombreDia($diaId)
    {
        $dias = [
            12 => 'LUNES',
            13 => 'MARTES',
            14 => 'MIERCOLES',
            15 => 'JUEVES',
            16 => 'VIERNES',
            17 => 'SÁBADO',
            18 => 'DDOMINGO'
        ];

        return $dias[$diaId] ?? 'Desconocido';
    }

    /**
     * Obtiene el horario de la clase actual (si existe)
     */
    public function obtenerClaseActual()
    {
        $horaActual = now();
        $diaActual = $horaActual->dayOfWeek;
        $diaIdActual = ($diaActual == 0) ? 18 : $diaActual + 11;

        $claseHoy = $this->instructorFichaDias()
            ->where('dia_id', $diaIdActual)
            ->first();

        if (!$claseHoy) {
            return null;
        }

        $horaInicio = \Carbon\Carbon::parse($claseHoy->hora_inicio);
        $horaFin = \Carbon\Carbon::parse($claseHoy->hora_fin);

        // Verificar si estamos en horario de clase
        if ($horaActual->between($horaInicio, $horaFin)) {
            return [
                'hora_inicio' => $claseHoy->hora_inicio,
                'hora_fin' => $claseHoy->hora_fin,
                'dia_nombre' => $this->obtenerNombreDia($claseHoy->dia_id),
                'estado' => 'en_curso'
            ];
        }

        return null;
    }
}
