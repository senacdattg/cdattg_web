<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Instructor;
use App\Models\FichaCaracterizacion;
use App\Models\InstructorFichaCaracterizacion;
use App\Models\Parametro;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AsignarInstructoresRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('EDITAR INSTRUCTOR') || $this->user()->can('CREAR INSTRUCTOR');
    }

    /**
     * Preparar datos antes de validación
     */
    protected function prepareForValidation(): void
    {
        // Si no se proporciona instructor_principal_id, obtenerlo de la ficha
        if (!$this->has('instructor_principal_id') || !$this->input('instructor_principal_id')) {
            $fichaId = $this->route('id');
            $ficha = FichaCaracterizacion::find($fichaId);
            
            if ($ficha && $ficha->instructor_id) {
                $this->merge([
                    'instructor_principal_id' => $ficha->instructor_id
                ]);
            }
        }
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'instructores' => 'required|array|min:1|max:10',
            'instructores.*.instructor_id' => [
                'required',
                'integer',
                'exists:instructors,id',
                function ($attribute, $value, $fail) {
                    $this->validarInstructorActivo($value, $fail);
                    $this->validarLimiteFichasActivas($value, $fail);
                }
            ],
            'instructores.*.fecha_inicio' => [
                'required',
                'date',
                'after_or_equal:today',
                function ($attribute, $value, $fail) {
                    $this->validarFechaInicioFicha($value, $fail);
                }
            ],
            'instructores.*.fecha_fin' => [
                'required',
                'date',
                'after_or_equal:instructores.*.fecha_inicio',
                function ($attribute, $value, $fail) {
                    $this->validarFechaFinFicha($value, $fail);
                }
            ],
            'instructores.*.total_horas_instructor' => 'nullable|integer|min:1|max:1000',
            // Validación principal: array simple de IDs de días
            'instructores.*.dias_semana' => 'required|array|min:1|max:7',
            'instructores.*.dias_semana.*' => 'required|integer|exists:parametros_temas,id',
            // Validación de días con horarios específicos (formato alternativo para modal)
            'instructores.*.dias' => 'nullable|array',
            'instructores.*.dias.*.hora_inicio' => 'required_with:instructores.*.dias|date_format:H:i',
            'instructores.*.dias.*.hora_fin' => 'required_with:instructores.*.dias|date_format:H:i|after:instructores.*.dias.*.hora_inicio',
            // Soporte para formato antiguo
            'instructores.*.dias_formacion' => 'nullable|array|min:1|max:7',
            'instructores.*.dias_formacion.*.dia_id' => 'exists:parametros_temas,id',
            'instructor_principal_id' => [
                'nullable',
                'integer',
                'exists:instructors,id'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'instructores.required' => 'Debe seleccionar al menos un instructor.',
            'instructores.min' => 'Debe seleccionar al menos un instructor.',
            'instructores.max' => 'No se pueden asignar más de 10 instructores a una ficha.',
            'instructores.*.instructor_id.required' => 'Debe seleccionar un instructor.',
            'instructores.*.instructor_id.exists' => 'El instructor seleccionado no existe.',
            'instructores.*.fecha_inicio.required' => 'La fecha de inicio es obligatoria.',
            'instructores.*.fecha_inicio.date' => 'La fecha de inicio debe ser una fecha válida.',
            'instructores.*.fecha_inicio.after_or_equal' => 'La fecha de inicio no puede ser anterior a hoy.',
            'instructores.*.fecha_fin.required' => 'La fecha de fin es obligatoria.',
            'instructores.*.fecha_fin.date' => 'La fecha de fin debe ser una fecha válida.',
            'instructores.*.fecha_fin.after_or_equal' => 'La fecha de fin debe ser posterior o igual a la fecha de inicio.',
            'instructores.*.total_horas_instructor.integer' => 'Las horas totales deben ser un número entero.',
            'instructores.*.total_horas_instructor.min' => 'Las horas totales deben ser al menos 1.',
            'instructores.*.total_horas_instructor.max' => 'Las horas totales no pueden exceder 1000.',
            // Mensajes para días_semana (formato principal)
            'instructores.*.dias_semana.required' => 'Debe seleccionar al menos un día de formación.',
            'instructores.*.dias_semana.array' => 'Los días de formación deben ser una lista válida.',
            'instructores.*.dias_semana.min' => 'Debe seleccionar al menos un día de formación.',
            'instructores.*.dias_semana.max' => 'No se pueden asignar más de 7 días de formación.',
            'instructores.*.dias_semana.*.required' => 'El día seleccionado no es válido.',
            'instructores.*.dias_semana.*.integer' => 'El ID del día debe ser un número.',
            'instructores.*.dias_semana.*.exists' => 'El día seleccionado no existe en el sistema.',
            // Mensajes para días con horarios (formato alternativo)
            'instructores.*.dias.*.hora_inicio.required_with' => 'La hora de inicio es obligatoria cuando se selecciona un día.',
            'instructores.*.dias.*.hora_inicio.date_format' => 'El formato de la hora de inicio debe ser HH:MM.',
            'instructores.*.dias.*.hora_fin.required_with' => 'La hora de fin es obligatoria cuando se selecciona un día.',
            'instructores.*.dias.*.hora_fin.date_format' => 'El formato de la hora de fin debe ser HH:MM.',
            'instructores.*.dias.*.hora_fin.after' => 'La hora de fin debe ser posterior a la hora de inicio.',
            // Mensajes para formato antiguo
            'instructores.*.dias_formacion.min' => 'Debe seleccionar al menos un día de formación.',
            'instructores.*.dias_formacion.max' => 'No se pueden asignar más de 7 días de formación.',
            'instructores.*.dias_formacion.*.dia_id.exists' => 'El día seleccionado no existe.',
            'instructor_principal_id.exists' => 'El instructor líder seleccionado no existe en el sistema.',
            'instructor_principal_id.integer' => 'El instructor líder debe ser un identificador válido.'
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->validarConflictosFechas($validator);
            $this->validarEspecialidadesRequeridas($validator);
            $this->validarDisponibilidadHoraria($validator);
            $this->validarReglasSENA($validator);
            
            // Las sugerencias han sido removidas por solicitud del usuario
        });
    }

    /**
     * Validar que el instructor esté activo
     */
    private function validarInstructorActivo($instructorId, $fail): void
    {
        $instructor = Instructor::find($instructorId);
        if ($instructor && !$instructor->status) {
            $fail("El instructor {$instructor->nombre_completo} está inactivo.");
        }
    }

    /**
     * Validar límite de fichas activas por instructor
     */
    private function validarLimiteFichasActivas($instructorId, $fail): void
    {
        $instructor = Instructor::find($instructorId);
        if (!$instructor) return;

        $fichasActivas = $instructor->instructorFichas()
            ->whereHas('ficha', function($q) {
                $q->where('status', true)
                  ->where('fecha_fin', '>=', now()->toDateString());
            })
            ->count();

        if ($fichasActivas >= 5) { // Máximo 5 fichas activas según reglas SENA
            $fail("El instructor {$instructor->nombre_completo} ya tiene el máximo de fichas activas (5).");
        }
    }

    /**
     * Validar que la fecha de inicio no sea anterior a la fecha de inicio de la ficha
     */
    private function validarFechaInicioFicha($fechaInicio, $fail): void
    {
        $fichaId = $this->route('id');
        $ficha = FichaCaracterizacion::find($fichaId);
        
        if ($ficha && $ficha->fecha_inicio) {
            $fechaInicioFicha = Carbon::parse($ficha->fecha_inicio);
            $fechaInicioInstructor = Carbon::parse($fechaInicio);
            
            if ($fechaInicioInstructor->lt($fechaInicioFicha)) {
                $fail("La fecha de inicio del instructor debe ser posterior o igual a la fecha de inicio de la ficha ({$fechaInicioFicha->format('d/m/Y')}).");
            }
        }
    }

    /**
     * Validar que la fecha de fin no sea posterior a la fecha de fin de la ficha
     */
    private function validarFechaFinFicha($fechaFin, $fail): void
    {
        $fichaId = $this->route('id');
        $ficha = FichaCaracterizacion::find($fichaId);
        
        if ($ficha && $ficha->fecha_fin) {
            $fechaFinFicha = Carbon::parse($ficha->fecha_fin);
            $fechaFinInstructor = Carbon::parse($fechaFin);
            
            if ($fechaFinInstructor->gt($fechaFinFicha)) {
                $fail("La fecha de fin del instructor debe ser anterior o igual a la fecha de fin de la ficha ({$fechaFinFicha->format('d/m/Y')}).");
            }
        }
    }

    /**
     * Validar que el instructor principal esté en la lista de instructores
     * NOTA: Esta validación está deshabilitada porque el instructor principal
     * es el líder de la ficha asignado en la creación, no necesariamente
     * tiene que estar en la lista de instructores adicionales.
     */
    private function validarInstructorPrincipalEnLista($instructorPrincipalId, $fail): void
    {
        // Validación deshabilitada - El instructor principal puede ser independiente
        // de los instructores adicionales asignados
        return;
    }

    /**
     * Validar conflictos de fechas entre instructores (considerando jornadas y días)
     */
    private function validarConflictosFechas($validator): void
    {
        $instructores = $this->input('instructores', []);
        $fichaId = $this->route('id');
        $ficha = FichaCaracterizacion::find($fichaId);
        $jornadaIdFicha = $ficha ? $ficha->jornada_id : null;

        foreach ($instructores as $index => $instructorData) {
            $instructorId = $instructorData['instructor_id'];
            $fechaInicio = Carbon::parse($instructorData['fecha_inicio']);
            $fechaFin = Carbon::parse($instructorData['fecha_fin']);
            
            // Extraer IDs de días según el formato proporcionado
            $diasNuevos = [];
            if (isset($instructorData['dias']) && is_array($instructorData['dias'])) {
                $diasNuevos = array_keys($instructorData['dias']); // Nuevo formato: ['12' => ['hora_inicio' => '08:00', ...]]
            } elseif (isset($instructorData['dias_semana']) && is_array($instructorData['dias_semana'])) {
                $diasNuevos = $instructorData['dias_semana']; // Array simple de IDs
            } elseif (isset($instructorData['dias_formacion']) && is_array($instructorData['dias_formacion'])) {
                $diasNuevos = collect($instructorData['dias_formacion'])->pluck('dia_id')->filter()->toArray(); // Formato antiguo
            }

            // 1. Verificar conflictos con otras fichas del mismo instructor
            $this->validarConflictosOtrosInstructor($validator, $instructorId, $fechaInicio, $fechaFin, $diasNuevos, $jornadaIdFicha, $index);

            // 2. Verificar conflictos con otros instructores en la MISMA ficha
            $this->validarConflictosMismaFicha($validator, $instructores, $index, $instructorId, $fechaInicio, $fechaFin, $diasNuevos);
        }
    }

    /**
     * Validar conflictos con otras fichas del mismo instructor
     */
    private function validarConflictosOtrosInstructor($validator, $instructorId, $fechaInicio, $fechaFin, $diasNuevos, $jornadaIdFicha, $index): void
    {
        $conflictosQuery = InstructorFichaCaracterizacion::where('instructor_id', $instructorId)
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
            ->with(['ficha.jornadaFormacion', 'instructorFichaDias.dia']);

        $conflictosExistentes = $conflictosQuery->get();

        // Filtrar conflictos por días de la semana si se especifican
        if (!empty($diasNuevos)) {
            $conflictosExistentes = $conflictosExistentes->filter(function($conflicto) use ($diasNuevos) {
                $diasExistentes = $conflicto->instructorFichaDias->pluck('dia_id')->toArray();
                $diasEnComun = array_intersect($diasNuevos, $diasExistentes);
                return !empty($diasEnComun); // Solo es conflicto si hay días en común
            });
        }

            if ($conflictosExistentes->isNotEmpty()) {
                $instructor = Instructor::find($instructorId);
            $conflictosText = $conflictosExistentes->map(function($conflicto) use ($diasNuevos) {
                    $programaNombre = $conflicto->ficha->programaFormacion->nombre ?? 'Sin programa';
                $jornada = $conflicto->ficha->jornadaFormacion->jornada ?? 'Sin jornada';
                
                // Mostrar días en conflicto
                $diasExistentes = $conflicto->instructorFichaDias->pluck('dia_id')->toArray();
                $diasEnComun = array_intersect($diasNuevos, $diasExistentes);
                $diasNombres = $conflicto->instructorFichaDias
                    ->whereIn('dia_id', $diasEnComun)
                    ->pluck('dia.name')
                    ->filter()
                    ->implode(', ');
                
                $diasInfo = $diasNombres ? " - Días en conflicto: {$diasNombres}" : '';
                return "Ficha {$conflicto->ficha->ficha} ({$programaNombre}) - Jornada: {$jornada}{$diasInfo} del " . Carbon::parse($conflicto->fecha_inicio)->format('d/m/Y') . " al " . Carbon::parse($conflicto->fecha_fin)->format('d/m/Y');
                })->implode(', ');

                $validator->errors()->add(
                    "instructores.{$index}.fecha_inicio",
                "📅 El instructor {$instructor->nombre_completo} ya tiene fichas con fechas superpuestas en la misma jornada y días: {$conflictosText}. Ajuste las fechas, jornada o días para evitar conflictos."
            );
        }
    }

    /**
     * Validar conflictos entre instructores en la misma ficha
     */
    private function validarConflictosMismaFicha($validator, $instructores, $indexActual, $instructorIdActual, $fechaInicioActual, $fechaFinActual, $diasActuales): void
    {
        $fichaId = $this->route('id');
        
        \Log::info('🔍 VALIDACIÓN MISMA FICHA', [
            'instructores_total' => count($instructores),
            'index_actual' => $indexActual,
            'instructor_actual' => $instructorIdActual,
            'fecha_actual' => $fechaInicioActual->format('Y-m-d') . ' a ' . $fechaFinActual->format('Y-m-d'),
            'dias_actuales' => $diasActuales,
            'ficha_id' => $fichaId
        ]);

        // 1. Verificar conflictos con otros instructores en el mismo formulario
        foreach ($instructores as $indexOtro => $instructorOtro) {
            // No comparar consigo mismo
            if ($indexActual === $indexOtro) continue;

            $instructorIdOtro = $instructorOtro['instructor_id'];
            $fechaInicioOtro = Carbon::parse($instructorOtro['fecha_inicio']);
            $fechaFinOtro = Carbon::parse($instructorOtro['fecha_fin']);
            
            // Extraer IDs de días del otro instructor
            $diasOtros = [];
            if (isset($instructorOtro['dias']) && is_array($instructorOtro['dias'])) {
                $diasOtros = array_keys($instructorOtro['dias']);
            } elseif (isset($instructorOtro['dias_semana']) && is_array($instructorOtro['dias_semana'])) {
                $diasOtros = $instructorOtro['dias_semana'];
            } elseif (isset($instructorOtro['dias_formacion']) && is_array($instructorOtro['dias_formacion'])) {
                $diasOtros = collect($instructorOtro['dias_formacion'])->pluck('dia_id')->filter()->toArray();
            }

            \Log::info('🔍 COMPARANDO CON INSTRUCTOR EN FORMULARIO', [
                'index_otro' => $indexOtro,
                'instructor_otro' => $instructorIdOtro,
                'fecha_otro' => $fechaInicioOtro->format('Y-m-d') . ' a ' . $fechaFinOtro->format('Y-m-d'),
                'dias_otros' => $diasOtros
            ]);

            // Verificar si hay superposición de fechas
            $haySuperposicion = $this->haySuperposicionFechas($fechaInicioActual, $fechaFinActual, $fechaInicioOtro, $fechaFinOtro);
            
            \Log::info('🔍 SUPERPOSICIÓN DE FECHAS', [
                'hay_superposicion' => $haySuperposicion
            ]);
            
            if ($haySuperposicion) {
                // Verificar si hay días en común
                $diasEnComun = array_intersect($diasActuales, $diasOtros);
                
                \Log::info('🔍 DÍAS EN COMÚN', [
                    'dias_en_comun' => $diasEnComun,
                    'hay_conflicto' => !empty($diasEnComun)
                ]);
                
                if (!empty($diasEnComun)) {
                    $instructorActual = Instructor::find($instructorIdActual);
                    $instructorOtro = Instructor::find($instructorIdOtro);
                    
                    // Obtener nombres de los días en común desde Parametro
                    $diasNombres = Parametro::whereIn('id', $diasEnComun)->pluck('name')->implode(', ');
                    
                    \Log::error('❌ CONFLICTO DETECTADO EN FORMULARIO', [
                        'instructor_actual' => $instructorActual->nombre_completo,
                        'instructor_otro' => $instructorOtro->nombre_completo,
                        'dias_conflicto' => $diasNombres
                    ]);
                    
                    $validator->errors()->add(
                        "instructores.{$indexActual}.fecha_inicio",
                        "⚠️ CONFLICTO EN LA MISMA FICHA: El instructor {$instructorActual->nombre_completo} no puede ser asignado en las mismas fechas y días ({$diasNombres}) que el instructor {$instructorOtro->nombre_completo}. Ajuste las fechas o días para evitar el conflicto."
                    );
                }
            }
        }

        // 2. Verificar conflictos con instructores ya asignados en la ficha
        $this->validarConflictosConAsignacionesExistentes($validator, $instructorIdActual, $fechaInicioActual, $fechaFinActual, $diasActuales, $indexActual, $fichaId);
    }

    /**
     * Validar conflictos con instructores ya asignados en la ficha
     */
    private function validarConflictosConAsignacionesExistentes($validator, $instructorIdActual, $fechaInicioActual, $fechaFinActual, $diasActuales, $indexActual, $fichaId): void
    {
        // Obtener asignaciones existentes en la ficha (excluyendo el instructor actual si ya está asignado)
        $asignacionesExistentes = InstructorFichaCaracterizacion::where('ficha_id', $fichaId)
            ->where('instructor_id', '!=', $instructorIdActual) // Excluir el instructor actual
            ->with(['instructor.persona', 'instructorFichaDias.dia'])
            ->get();

        \Log::info('🔍 ASIGNACIONES EXISTENTES EN FICHA', [
            'ficha_id' => $fichaId,
            'total_existentes' => $asignacionesExistentes->count(),
            'asignaciones' => $asignacionesExistentes->map(function($a) {
                return [
                    'instructor_id' => $a->instructor_id,
                    'instructor_nombre' => $a->instructor->nombre_completo ?? 'Sin nombre',
                    'fecha_inicio' => $a->fecha_inicio,
                    'fecha_fin' => $a->fecha_fin,
                    'dias' => $a->instructorFichaDias->pluck('dia_id')->toArray()
                ];
            })->toArray()
        ]);

        foreach ($asignacionesExistentes as $asignacionExistente) {
            $instructorIdExistente = $asignacionExistente->instructor_id;
            $fechaInicioExistente = Carbon::parse($asignacionExistente->fecha_inicio);
            $fechaFinExistente = Carbon::parse($asignacionExistente->fecha_fin);
            $diasExistentes = $asignacionExistente->instructorFichaDias->pluck('dia_id')->toArray();

            \Log::info('🔍 COMPARANDO CON ASIGNACIÓN EXISTENTE', [
                'instructor_existente' => $instructorIdExistente,
                'fecha_existente' => $fechaInicioExistente->format('Y-m-d') . ' a ' . $fechaFinExistente->format('Y-m-d'),
                'dias_existentes' => $diasExistentes
            ]);

            // Verificar si hay superposición de fechas
            $haySuperposicion = $this->haySuperposicionFechas($fechaInicioActual, $fechaFinActual, $fechaInicioExistente, $fechaFinExistente);
            
            \Log::info('🔍 SUPERPOSICIÓN CON EXISTENTE', [
                'hay_superposicion' => $haySuperposicion
            ]);
            
            if ($haySuperposicion) {
                // Verificar si hay días en común
                $diasEnComun = array_intersect($diasActuales, $diasExistentes);
                
                \Log::info('🔍 DÍAS EN COMÚN CON EXISTENTE', [
                    'dias_en_comun' => $diasEnComun,
                    'hay_conflicto' => !empty($diasEnComun)
                ]);
                
                if (!empty($diasEnComun)) {
                    $instructorActual = Instructor::find($instructorIdActual);
                    $instructorExistente = Instructor::find($instructorIdExistente);
                    
                    // Obtener nombres de los días en común desde Parametro
                    $diasNombres = Parametro::whereIn('id', $diasEnComun)->pluck('name')->implode(', ');
                    
                    \Log::error('❌ CONFLICTO CON ASIGNACIÓN EXISTENTE', [
                        'instructor_actual' => $instructorActual->nombre_completo,
                        'instructor_existente' => $instructorExistente->nombre_completo,
                        'dias_conflicto' => $diasNombres
                    ]);
                    
                    $validator->errors()->add(
                        "instructores.{$indexActual}.fecha_inicio",
                        "⚠️ CONFLICTO CON INSTRUCTOR YA ASIGNADO: El instructor {$instructorActual->nombre_completo} no puede ser asignado en las mismas fechas y días ({$diasNombres}) que el instructor {$instructorExistente->nombre_completo} que ya está asignado a esta ficha. Ajuste las fechas o días para evitar el conflicto."
                    );
                }
            }
        }
    }

    /**
     * Verificar si dos rangos de fechas se superponen
     */
    private function haySuperposicionFechas($fechaInicio1, $fechaFin1, $fechaInicio2, $fechaFin2): bool
    {
        return $fechaInicio1->lte($fechaFin2) && $fechaFin1->gte($fechaInicio2);
    }

    /**
     * Validar especialidades requeridas
     * NOTA: Esta validación se maneja en InstructorBusinessRulesService para evitar duplicados
     */
    private function validarEspecialidadesRequeridas($validator): void
    {
        // La validación de especialidades se maneja en InstructorBusinessRulesService
        // para evitar duplicados con verificarDisponibilidad()
        return;
    }

    /**
     * Validar disponibilidad horaria (considerando jornadas y días de la semana)
     * NOTA: Esta validación ahora se maneja en validarConflictosFechas() para evitar duplicados
     */
    private function validarDisponibilidadHoraria($validator): void
    {
        // La validación de días y jornadas se maneja en validarConflictosFechas()
        // para evitar duplicados y tener una lógica centralizada
            return;
    }

    /**
     * Validar reglas específicas del SENA
     */
    private function validarReglasSENA($validator): void
    {
        $fichaId = $this->route('id');
        $ficha = FichaCaracterizacion::with('sede.regional')->find($fichaId);
        $instructores = $this->input('instructores', []);

        foreach ($instructores as $index => $instructorData) {
            $instructor = Instructor::find($instructorData['instructor_id']);
            if (!$instructor) continue;

            // Verificar que el instructor pertenezca a la misma regional
            $fichaRegionalId = $ficha && $ficha->sede ? $ficha->sede->regional_id : null;
            if ($ficha && $fichaRegionalId && $instructor->regional_id !== $fichaRegionalId) {
                $validator->errors()->add(
                    "instructores.{$index}.instructor_id",
                    "El instructor {$instructor->nombre_completo} debe pertenecer a la misma regional que la ficha."
                );
            }

            // Verificar experiencia mínima
            // NOTA: Esta validación se maneja en InstructorBusinessRulesService para evitar duplicados
            // if (($instructor->anos_experiencia ?? 0) < 1) {
            //     $validator->errors()->add(
            //         "instructores.{$index}.instructor_id",
            //         "👨‍🏫 El instructor {$instructor->nombre_completo} no cumple con la experiencia mínima requerida (1 año). Seleccione un instructor con más experiencia."
            //     );
            // }
        }
    }

}
