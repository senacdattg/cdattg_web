<?php

namespace App\Http\Requests;

use App\Models\AsignacionInstructor;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAsignacionInstructorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $asignacion = $this->route('asignacion');
        if ($asignacion instanceof AsignacionInstructor) {
            $asignacion = $asignacion->id;
        }

        return [
            'ficha_id' => ['required', 'exists:fichas_caracterizacion,id'],
            'instructor_id' => ['required', 'exists:instructors,id'],
            'competencia_id' => [
                'required',
                'exists:competencias,id',
                Rule::unique('asignaciones_instructores')
                    ->where(function ($query) {
                        return $query->where('ficha_id', $this->input('ficha_id'))
                            ->where('instructor_id', $this->input('instructor_id'));
                    })
                    ->ignore($asignacion),
            ],
            'resultados' => ['required', 'array', 'min:1'],
            'resultados.*' => ['exists:resultados_aprendizajes,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'ficha_id' => 'ficha de caracterización',
            'instructor_id' => 'instructor',
            'competencia_id' => 'competencia',
            'resultados' => 'resultados de aprendizaje',
        ];
    }

    public function messages(): array
    {
        return [
            'resultados.required' => 'Debe seleccionar al menos un resultado de aprendizaje.',
            'resultados.min' => 'Seleccione al menos un resultado de aprendizaje.',
            'competencia_id.unique' => 'Ya existe una asignación para esta ficha, instructor y competencia.',
        ];
    }
}

