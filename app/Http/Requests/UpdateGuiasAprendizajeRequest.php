<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGuiasAprendizajeRequest extends FormRequest
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
            'codigo' => [
                'required',
                'string',
                'max:50',
                Rule::unique('guia_aprendizajes', 'codigo')->ignore($this->route('guia_aprendizaje'))
            ],
            'nombre' => 'required|string|max:255',
            'status' => 'nullable|boolean',
            'resultados_aprendizaje' => 'required|array|min:1',
            'resultados_aprendizaje.*' => 'exists:resultados_aprendizajes,id',
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
            'codigo.required' => 'El código es obligatorio.',
            'codigo.string' => 'El código debe ser una cadena de texto.',
            'codigo.max' => 'El código no puede tener más de 50 caracteres.',
            'codigo.unique' => 'El código ya existe en el sistema.',
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.string' => 'El nombre debe ser una cadena de texto.',
            'nombre.max' => 'El nombre no puede tener más de 255 caracteres.',
            'status.boolean' => 'El estado debe ser verdadero o falso.',
            'resultados_aprendizaje.required' => 'Debe seleccionar al menos un resultado de aprendizaje.',
            'resultados_aprendizaje.array' => 'Los resultados de aprendizaje deben ser un arreglo.',
            'resultados_aprendizaje.min' => 'Debe seleccionar al menos un resultado de aprendizaje.',
            'resultados_aprendizaje.*.exists' => 'Uno o más resultados de aprendizaje no existen en el sistema.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'codigo' => 'código',
            'nombre' => 'nombre',
            'status' => 'estado',
            'resultados_aprendizaje' => 'resultados de aprendizaje',
        ];
    }
}
