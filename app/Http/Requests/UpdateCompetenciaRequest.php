<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCompetenciaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $competenciaId = $this->route('competencia');

        return [
            'descripcion' => 'required|string|max:1000',
            'codigo' => 'required|string|max:50|unique:competencias,codigo,' . $competenciaId,
            'nombre' => 'required|string|max:255',
            'duracion' => 'required|integer|min:1|max:9999',
            'programas' => 'sometimes|array',
            'programas.*' => 'exists:programas_formacion,id',
            'status' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'descripcion.required' => 'La norma o unidad de competencia es obligatoria.',
            'descripcion.string' => 'La norma debe ser una cadena de texto.',
            'descripcion.max' => 'La norma no puede tener más de 1000 caracteres.',
            'codigo.required' => 'El código de norma es obligatorio.',
            'codigo.string' => 'El código debe ser una cadena de texto.',
            'codigo.max' => 'El código no puede tener más de 50 caracteres.',
            'codigo.unique' => 'Ya existe una competencia con este código.',
            'nombre.required' => 'El nombre de la competencia es obligatorio.',
            'nombre.string' => 'El nombre debe ser una cadena de texto.',
            'nombre.max' => 'El nombre no puede tener más de 255 caracteres.',
            'duracion.required' => 'La duración máxima es obligatoria.',
            'duracion.integer' => 'La duración debe ser un número entero válido.',
            'duracion.min' => 'La duración debe ser de al menos 1 hora.',
            'duracion.max' => 'La duración no puede superar las 9999 horas.',
            'programas.array' => 'El formato de los programas seleccionados no es válido.',
            'programas.*.exists' => 'Alguno de los programas seleccionados no existe.',
            'status.boolean' => 'El estado debe ser verdadero o falso.',
        ];
    }

    public function attributes(): array
    {
        return [
            'descripcion' => 'norma o unidad de competencia',
            'codigo' => 'código de norma',
            'nombre' => 'nombre de la competencia',
            'duracion' => 'duración máxima',
            'programas' => 'programas de formación',
            'status' => 'estado',
        ];
    }
}

