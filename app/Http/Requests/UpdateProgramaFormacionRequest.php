<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProgramaFormacionRequest extends FormRequest
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
        $programaId = $this->route('programa');
        
        return [
            'codigo' => 'required|string|max:50|unique:programas_formacion,codigo,' . $programaId,
            'nombre' => 'required|string|max:255|unique:programas_formacion,nombre,' . $programaId,
            'red_conocimiento_id' => 'required|exists:red_conocimientos,id',
            'nivel_formacion_id' => 'required|exists:parametros,id',
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
            'codigo.required' => 'El código del programa es obligatorio.',
            'codigo.string' => 'El código debe ser una cadena de texto.',
            'codigo.max' => 'El código no puede tener más de 50 caracteres.',
            'codigo.unique' => 'Ya existe un programa con este código.',
            
            'nombre.required' => 'El nombre del programa es obligatorio.',
            'nombre.string' => 'El nombre debe ser una cadena de texto.',
            'nombre.max' => 'El nombre no puede tener más de 255 caracteres.',
            'nombre.unique' => 'Ya existe un programa con este nombre.',
            
            'red_conocimiento_id.required' => 'La red de conocimiento es obligatoria.',
            'red_conocimiento_id.exists' => 'La red de conocimiento seleccionada no es válida.',
            
            'nivel_formacion_id.required' => 'El nivel de formación es obligatorio.',
            'nivel_formacion_id.exists' => 'El nivel de formación seleccionado no es válido.',
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
            'codigo' => 'código del programa',
            'nombre' => 'nombre del programa',
            'red_conocimiento_id' => 'red de conocimiento',
            'nivel_formacion_id' => 'nivel de formación',
        ];
    }
}
