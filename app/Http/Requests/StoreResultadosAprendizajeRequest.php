<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreResultadosAprendizajeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'codigo' => 'required|string|max:50|unique:resultados_aprendizajes,codigo',
            'nombre' => 'required|string|max:500',
            'duracion' => 'nullable|integer|min:1|max:9999',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'competencia_id' => 'nullable|exists:competencias,id',
        ];
    }

    public function messages(): array
    {
        return [
            'codigo.required' => 'El código es obligatorio.',
            'codigo.string' => 'El código debe ser una cadena de texto.',
            'codigo.max' => 'El código no puede tener más de 50 caracteres.',
            'codigo.unique' => 'El código ya existe en el sistema.',
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.string' => 'El nombre debe ser una cadena de texto.',
            'nombre.max' => 'El nombre no puede tener más de 500 caracteres.',
            'duracion.integer' => 'La duración debe ser un número entero.',
            'duracion.min' => 'La duración debe ser de al menos 1 hora.',
            'duracion.max' => 'La duración no puede superar las 9999 horas.',
            'fecha_inicio.date' => 'La fecha de inicio debe ser una fecha válida.',
            'fecha_fin.date' => 'La fecha de fin debe ser una fecha válida.',
            'fecha_fin.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio.',
            'competencia_id.exists' => 'La competencia seleccionada no existe en el sistema.',
        ];
    }

    public function attributes(): array
    {
        return [
            'codigo' => 'código',
            'nombre' => 'nombre',
            'duracion' => 'duración',
            'fecha_inicio' => 'fecha de inicio',
            'fecha_fin' => 'fecha de fin',
            'competencia_id' => 'competencia',
        ];
    }
}
