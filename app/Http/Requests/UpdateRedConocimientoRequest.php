<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRedConocimientoRequest extends FormRequest
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
        $redConocimientoId = $this->route('red_conocimiento');
        
        return [
            'nombre' => [
                'required', 
                'string', 
                'max:255',
                Rule::unique('red_conocimientos', 'nombre')->ignore($redConocimientoId)
            ],
            'regionals_id' => ['nullable', 'integer', 'exists:regionals,id'],
        ];
    }

    /**
     * Get custom error messages for validator.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre de la red de conocimiento es obligatorio.',
            'nombre.string' => 'El nombre debe ser una cadena de texto.',
            'nombre.max' => 'El nombre no puede exceder los 255 caracteres.',
            'nombre.unique' => 'Ya existe una red de conocimiento con este nombre.',
            'regionals_id.integer' => 'El ID de la regional debe ser un número entero.',
            'regionals_id.exists' => 'La regional seleccionada no existe.',
        ];
    }
}
