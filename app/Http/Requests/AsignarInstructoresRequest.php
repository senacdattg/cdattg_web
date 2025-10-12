<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Instructor;
use App\Models\FichaCaracterizacion;
use App\Models\InstructorFichaCaracterizacion;
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
            'instructores.*.total_horas_instructor' => 'required|integer|min:1|max:1000',
            'instructores.*.dias_formacion' => 'sometimes|array|max:7',
            'instructores.*.dias_formacion.*.dia_id' => 'required_with:instructores.*.dias_formacion|exists:parametros_temas,id',
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
            'instructores.*.total_horas_instructor.required' => 'Las horas totales son obligatorias.',
            'instructores.*.total_horas_instructor.integer' => 'Las horas totales deben ser un número entero.',
            'instructores.*.total_horas_instructor.min' => 'Las horas totales deben ser al menos 1.',
            'instructores.*.total_horas_instructor.max' => 'Las horas totales no pueden exceder 1000.',
            'instructores.*.dias_formacion.array' => 'Los días de formación deben ser una lista válida.',
            'instructores.*.dias_formacion.max' => 'No se pueden asignar más de 7 días de formación.',
            'instructores.*.dias_formacion.*.dia_id.required_with' => 'Debe seleccionar un día válido.',
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
            
            // Si hay errores, mostrar sugerencias
            if ($validator->errors()->any()) {
                $this->mostrarSugerenciasInstructores($validator);
            }
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
     * Validar conflictos de fechas entre instructores (considerando jornadas)
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
            $diasNuevos = isset($instructorData['dias_formacion']) 
                ? collect($instructorData['dias_formacion'])->pluck('dia_id')->filter()->toArray() 
                : [];

            // Verificar conflictos con otras fichas del mismo instructor
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
                    return "Ficha {$conflicto->ficha->ficha} ({$programaNombre}) - Jornada: {$jornada}{$diasInfo} del {$conflicto->fecha_inicio->format('d/m/Y')} al {$conflicto->fecha_fin->format('d/m/Y')}";
                })->implode(', ');

                $validator->errors()->add(
                    "instructores.{$index}.fecha_inicio",
                    "📅 El instructor {$instructor->nombre_completo} ya tiene fichas con fechas superpuestas en la misma jornada y días: {$conflictosText}. Ajuste las fechas, jornada o días para evitar conflictos."
                );
            }
        }
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

    /**
     * Mostrar sugerencias de instructores disponibles
     */
    private function mostrarSugerenciasInstructores($validator): void
    {
        $fichaId = $this->route('id');
        $ficha = FichaCaracterizacion::with(['sede.regional', 'programaFormacion.redConocimiento'])->find($fichaId);
        
        if (!$ficha) return;

        // Obtener instructores disponibles para esta ficha
        $instructoresDisponibles = Instructor::where('status', true)
            ->whereHas('persona.user')
            ->when($ficha->sede && $ficha->sede->regional_id, function($query) use ($ficha) {
                return $query->where('regional_id', $ficha->sede->regional_id);
            })
            ->with('persona')
            ->get();

        if ($instructoresDisponibles->isNotEmpty()) {
            $sugerencias = $instructoresDisponibles->take(3)->map(function($instructor) {
                return "• {$instructor->nombre_completo} (Doc: {$instructor->persona->numero_documento})";
            })->implode("\n");

            $validator->errors()->add(
                'sugerencias',
                "💡 <strong>Sugerencias de instructores disponibles:</strong>\n\n{$sugerencias}\n\n💡 <em>Vaya a 'Gestión de Instructores' para ver más opciones disponibles.</em>"
            );
        }
    }
}
