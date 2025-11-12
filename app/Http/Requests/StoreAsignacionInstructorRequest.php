<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAsignacionInstructorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ficha_id' => 'required|exists:fichas_caracterizacion,id',
            'instructor_id' => 'required|exists:instructors,id',
            'competencia_id' => 'required|exists:competencias,id',
            'resultados' => 'required|array|min:1',
            'resultados.*' => 'exists:resultados_aprendizajes,id',
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
        ];
    }
}

