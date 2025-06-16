<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRegionalRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'nombre' => 'required|string|unique:regionals,nombre',
            'departamento_id' => 'required|exists:departamentos,id',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre de la regional es requerido',
            'nombre.string' => 'El nombre de la regional debe ser una cadena de texto',
            'nombre.unique' => 'El nombre de la regional ya existe',
            'departamento_id.required' => 'El departamento es requerido',
            'departamento_id.exists' => 'El departamento seleccionado no es válido',
        ];
    }
}
