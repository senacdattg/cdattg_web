<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAprendizRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para realizar esta solicitud.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Obtiene las reglas de validación que se aplican a la solicitud.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'persona_id' => 'required|exists:personas,id|unique:aprendices,persona_id',
            'ficha_caracterizacion_id' => 'required|exists:fichas_caracterizacion,id',
            'estado' => 'required|boolean',
        ];
    }

    /**
     * Obtiene mensajes personalizados para errores de validación.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'persona_id.required' => 'La persona es obligatoria.',
            'persona_id.exists' => 'La persona seleccionada no existe.',
            'persona_id.unique' => 'Esta persona ya está registrada como aprendiz.',
            'ficha_caracterizacion_id.required' => 'La ficha de caracterización es obligatoria.',
            'ficha_caracterizacion_id.exists' => 'La ficha de caracterización seleccionada no existe.',
            'estado.required' => 'El estado es obligatorio.',
            'estado.boolean' => 'El estado debe ser verdadero o falso.',
        ];
    }
}

