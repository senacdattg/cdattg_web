<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Instructor;
use App\Models\FichaCaracterizacion;
use Carbon\Carbon;

class InstructorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $instructorId = $this->route('instructor');
        
        return [
            // Validaciones básicas
            'persona_id' => [
                'required',
                'integer',
                'exists:personas,id',
                Rule::unique('instructors')->ignore($instructorId)
            ],
            'regional_id' => 'required|integer|exists:regionals,id',
            'status' => 'boolean',
            
            // Validaciones de especialidades
            'especialidades' => 'nullable|array',
            'especialidades.principal' => 'nullable|string|max:255',
            'especialidades.secundarias' => 'nullable|array',
            'especialidades.secundarias.*' => 'string|max:255',
            
            // Validaciones de competencias
            'competencias' => 'nullable|array',
            'competencias.*' => 'string|max:255',
            
            // Validaciones de experiencia
            'anos_experiencia' => 'nullable|integer|min:0|max:50',
            'experiencia_laboral' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'persona_id.required' => 'La persona es obligatoria.',
            'persona_id.unique' => 'Esta persona ya está registrada como instructor.',
            'persona_id.exists' => 'La persona seleccionada no existe.',
            'regional_id.required' => 'La regional es obligatoria.',
            'regional_id.exists' => 'La regional seleccionada no existe.',
            'especialidades.array' => 'Las especialidades deben ser un arreglo.',
            'especialidades.principal.string' => 'La especialidad principal debe ser texto.',
            'especialidades.secundarias.array' => 'Las especialidades secundarias deben ser un arreglo.',
            'especialidades.secundarias.*.string' => 'Cada especialidad secundaria debe ser texto.',
            'competencias.array' => 'Las competencias deben ser un arreglo.',
            'competencias.*.string' => 'Cada competencia debe ser texto.',
            'anos_experiencia.integer' => 'Los años de experiencia deben ser un número entero.',
            'anos_experiencia.min' => 'Los años de experiencia no pueden ser negativos.',
            'anos_experiencia.max' => 'Los años de experiencia no pueden exceder 50 años.',
            'experiencia_laboral.string' => 'La experiencia laboral debe ser texto.',
            'experiencia_laboral.max' => 'La experiencia laboral no puede exceder 1000 caracteres.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->validateBusinessRules($validator);
        });
    }

    /**
     * Validaciones de reglas de negocio específicas del SENA
     */
    protected function validateBusinessRules($validator): void
    {
        $instructorId = $this->route('instructor');
        $personaId = $this->input('persona_id');
        $regionalId = $this->input('regional_id');
        $especialidades = $this->input('especialidades', []);

        // Validar que el instructor no tenga fichas superpuestas
        if ($this->hasFichasSuperpuestas($instructorId, $validator)) {
            $validator->errors()->add('fichas', 'El instructor tiene fichas con fechas superpuestas.');
        }

        // Validar límite de fichas activas por instructor
        if ($this->exceedsMaxFichasActivas($instructorId, $validator)) {
            $validator->errors()->add('fichas', 'El instructor excede el límite máximo de fichas activas (5 fichas).');
        }

        // Validar que el instructor tenga al menos una especialidad principal
        if ($this->isCreating() && empty($especialidades['principal'])) {
            $validator->errors()->add('especialidades.principal', 'El instructor debe tener al menos una especialidad principal.');
        }

        // Validar que las especialidades pertenezcan a la regional del instructor
        if (!$this->validateEspecialidadesPorRegional($especialidades, $regionalId, $validator)) {
            $validator->errors()->add('especialidades', 'Las especialidades deben pertenecer a la regional del instructor.');
        }

        // Validar que el instructor tenga experiencia mínima
        if ($this->input('anos_experiencia') && $this->input('anos_experiencia') < 1) {
            $validator->errors()->add('anos_experiencia', 'El instructor debe tener al menos 1 año de experiencia.');
        }

        // Validar disponibilidad del instructor para nuevas fichas
        if ($this->hasConflictsWithNewFichas($instructorId, $validator)) {
            $validator->errors()->add('disponibilidad', 'El instructor no está disponible para nuevas asignaciones en el período solicitado.');
        }
    }

    /**
     * Verificar si el instructor tiene fichas con fechas superpuestas
     */
    protected function hasFichasSuperpuestas($instructorId, $validator): bool
    {
        if (!$instructorId) return false;

        $instructor = Instructor::find($instructorId);
        if (!$instructor) return false;

        $fichas = $instructor->instructorFichas()
            ->with('ficha')
            ->whereHas('ficha', function($q) {
                $q->where('status', true)
                  ->where('fecha_fin', '>=', now()->toDateString());
            })
            ->get();

        foreach ($fichas as $instructorFicha) {
            $ficha = $instructorFicha->ficha;
            $fechaInicio = Carbon::parse($ficha->fecha_inicio);
            $fechaFin = Carbon::parse($ficha->fecha_fin);

            // Verificar superposición con otras fichas
            $hasOverlap = $instructor->instructorFichas()
                ->where('id', '!=', $instructorFicha->id)
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
                ->exists();

            if ($hasOverlap) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verificar si el instructor excede el límite máximo de fichas activas
     */
    protected function exceedsMaxFichasActivas($instructorId, $validator): bool
    {
        if (!$instructorId) return false;

        $instructor = Instructor::find($instructorId);
        if (!$instructor) return false;

        $fichasActivas = $instructor->instructorFichas()
            ->whereHas('ficha', function($q) {
                $q->where('status', true)
                  ->where('fecha_fin', '>=', now()->toDateString());
            })
            ->count();

        // Límite máximo de 5 fichas activas por instructor según reglas SENA
        return $fichasActivas > 5;
    }

    /**
     * Validar que las especialidades pertenezcan a la regional del instructor
     */
    protected function validateEspecialidadesPorRegional($especialidades, $regionalId, $validator): bool
    {
        if (empty($especialidades)) return true;

        $regional = \App\Models\Regional::find($regionalId);
        if (!$regional) return false;

        // Obtener redes de conocimiento de la regional
        $redesConocimiento = \App\Models\RedConocimiento::where('regionals_id', $regionalId)
            ->where('status', true)
            ->pluck('nombre')
            ->toArray();

        // Validar especialidad principal
        if (!empty($especialidades['principal'])) {
            if (!in_array($especialidades['principal'], $redesConocimiento)) {
                return false;
            }
        }

        // Validar especialidades secundarias
        if (!empty($especialidades['secundarias']) && is_array($especialidades['secundarias'])) {
            foreach ($especialidades['secundarias'] as $especialidad) {
                if (!in_array($especialidad, $redesConocimiento)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Verificar si hay conflictos con nuevas fichas
     */
    protected function hasConflictsWithNewFichas($instructorId, $validator): bool
    {
        if (!$instructorId) return false;

        // Esta validación se puede extender para verificar conflictos con fichas específicas
        // cuando se esté asignando una nueva ficha al instructor
        return false;
    }

    /**
     * Verificar si se está creando un nuevo instructor
     */
    protected function isCreating(): bool
    {
        return $this->route('instructor') === null;
    }

    /**
     * Verificar si se está actualizando un instructor existente
     */
    protected function isUpdating(): bool
    {
        return $this->route('instructor') !== null;
    }
}
