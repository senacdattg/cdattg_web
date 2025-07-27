<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRegistroActividadesRequest extends FormRequest
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
            'nombre' => 'required|string|max:255',
            'fecha_evidencia' => 'required|date|after_or_equal:today',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre de la actividad es obligatorio.',
            'nombre.string' => 'El nombre de la actividad debe ser una cadena de texto.',
            'nombre.max' => 'El nombre de la actividad debe tener un máximo de 255 caracteres.',
            'fecha_evidencia.required' => 'La fecha de la actividad es obligatoria.',
            'fecha_evidencia.date' => 'La fecha de la actividad debe ser una fecha válida.',
            'fecha_evidencia.after_or_equal' => 'La fecha de la actividad debe ser una fecha válida.',
        ];
    }
}
