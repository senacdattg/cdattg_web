<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreevidenciasRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'resultado_aprendizaje_id' => 'required|exists:resultados_aprendizajes,id',
            'fecha_actividad' => 'required|date|after_or_equal:today',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre de la actividad es obligatorio.',
            'name.max' => 'El nombre de la actividad no puede tener más de 255 caracteres.',
            'resultado_aprendizaje_id.required' => 'Debe seleccionar un resultado de aprendizaje.',
            'resultado_aprendizaje_id.exists' => 'El resultado de aprendizaje seleccionado no es válido.',
            'fecha_actividad.required' => 'La fecha de la actividad es obligatoria.',
            'fecha_actividad.date' => 'La fecha de la actividad debe ser una fecha válida.',
            'fecha_actividad.after_or_equal' => 'La fecha de la actividad debe ser hoy o una fecha futura.',
        ];
    }
}
