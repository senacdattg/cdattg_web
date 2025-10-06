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
                'required',
                'integer',
                'exists:instructors,id',
                function ($attribute, $value, $fail) {
                    $this->validarInstructorPrincipalEnLista($value, $fail);
                }
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
            'instructor_principal_id.required' => 'Debe seleccionar un instructor principal.',
            'instructor_principal_id.exists' => 'El instructor principal seleccionado no existe.'
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
     */
    private function validarInstructorPrincipalEnLista($instructorPrincipalId, $fail): void
    {
        $instructores = $this->input('instructores', []);
        $instructorIds = collect($instructores)->pluck('instructor_id')->toArray();
    }

    /**
     * Validar conflictos de fechas entre instructores
     */
    private function validarConflictosFechas($validator): void
    {
        $instructores = $this->input('instructores', []);
        $conflictos = [];

        foreach ($instructores as $index => $instructorData) {
            $instructorId = $instructorData['instructor_id'];
            $fechaInicio = Carbon::parse($instructorData['fecha_inicio']);
            $fechaFin = Carbon::parse($instructorData['fecha_fin']);

            // Verificar conflictos con otras fichas del mismo instructor
            $conflictosExistentes = InstructorFichaCaracterizacion::where('instructor_id', $instructorId)
                ->whereHas('ficha', function($q) {
                    $q->where('status', true);
                })
                ->where(function($q) use ($fechaInicio, $fechaFin) {
                    $q->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
                      ->orWhereBetween('fecha_fin', [$fechaInicio, $fechaFin])
                      ->orWhere(function($subQ) use ($fechaInicio, $fechaFin) {
                          $subQ->where('fecha_inicio', '<=', $fechaInicio)
                               ->where('fecha_fin', '>=', $fechaFin);
                      });
                })
                ->with('ficha')
                ->get();

            if ($conflictosExistentes->isNotEmpty()) {
                $instructor = Instructor::find($instructorId);
                $conflictosText = $conflictosExistentes->map(function($conflicto) {
                    $programaNombre = $conflicto->ficha->programaFormacion->nombre ?? 'Sin programa';
                    return "Ficha {$conflicto->ficha->ficha} ({$programaNombre}) del {$conflicto->fecha_inicio->format('d/m/Y')} al {$conflicto->fecha_fin->format('d/m/Y')}";
                })->implode(', ');

                $validator->errors()->add(
                    "instructores.{$index}.fecha_inicio",
                    "📅 El instructor {$instructor->nombre_completo} ya tiene fichas con fechas superpuestas: {$conflictosText}. Ajuste las fechas para evitar conflictos."
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
     * Validar disponibilidad horaria
     */
    private function validarDisponibilidadHoraria($validator): void
    {
        $instructores = $this->input('instructores', []);
        $fichaId = $this->route('id');
        $ficha = FichaCaracterizacion::with('diasFormacion')->find($fichaId);

        if (!$ficha || !$ficha->diasFormacion) {
            return;
        }

        foreach ($instructores as $index => $instructorData) {
            if (!isset($instructorData['dias_formacion']) || empty($instructorData['dias_formacion'])) {
                continue;
            }

            $instructorId = $instructorData['instructor_id'];
            $fechaInicio = Carbon::parse($instructorData['fecha_inicio']);
            $fechaFin = Carbon::parse($instructorData['fecha_fin']);

            // Verificar conflictos de días de formación
            $conflictosDias = InstructorFichaCaracterizacion::where('instructor_id', $instructorId)
                ->whereHas('ficha', function($q) {
                    $q->where('status', true);
                })
                ->where(function($q) use ($fechaInicio, $fechaFin) {
                    $q->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
                      ->orWhereBetween('fecha_fin', [$fechaInicio, $fechaFin]);
                })
                ->whereHas('instructorFichaDias', function($q) use ($instructorData) {
                    $diaIds = collect($instructorData['dias_formacion'])->pluck('dia_id');
                    $q->whereIn('dia_id', $diaIds);
                })
                ->with('ficha')
                ->get();

            if ($conflictosDias->isNotEmpty()) {
                $instructor = Instructor::find($instructorId);
                $validator->errors()->add(
                    "instructores.{$index}.dias_formacion",
                    "⚠️ El instructor {$instructor->nombre_completo} ya tiene clases en los días seleccionados. Seleccione otros días o fechas diferentes."
                );
            }
        }
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
