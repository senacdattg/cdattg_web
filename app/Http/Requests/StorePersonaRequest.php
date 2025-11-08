<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePersonaRequest extends FormRequest
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
            'tipo_documento'      => 'required|exists:parametros_temas,id',
            'numero_documento'    => 'required|unique:personas,numero_documento',
            'primer_nombre'       => 'required|string',
            'segundo_nombre'      => 'nullable|string',
            'primer_apellido'     => 'required|string',
            'segundo_apellido'    => 'nullable|string',
            'fecha_nacimiento'    => 'required|date',
            'genero'              => 'required',
            'telefono'            => 'nullable|unique:personas,telefono',
            'celular'             => 'nullable|unique:personas,celular',
            'email'               => 'required|email|unique:personas,email|unique:users,email',
            'pais_id'             => 'required|exists:pais,id',
            'departamento_id'     => 'required|exists:departamentos,id',
            'municipio_id'        => 'required|exists:municipios,id',
            'direccion'           => 'required|string|max:255',
            'caracterizacion_ids'   => 'nullable|array',
            'caracterizacion_ids.*' => 'integer|exists:categorias_caracterizacion_complementarios,id',
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
            'tipo_documento.required'      => 'El tipo de documento es obligatorio.',
            'tipo_documento.exists'        => 'El tipo de documento seleccionado no es válido.',
            'numero_documento.required'    => 'El número de documento es obligatorio.',
            'numero_documento.unique'      => 'El número de documento ya está registrado.',
            'primer_nombre.required'       => 'El primer nombre es obligatorio.',
            'primer_nombre.string'         => 'El primer nombre debe ser una cadena de texto.',
            'primer_apellido.required'     => 'El primer apellido es obligatorio.',
            'primer_apellido.string'       => 'El primer apellido debe ser una cadena de texto.',
            'fecha_nacimiento.required'    => 'La fecha de nacimiento es obligatoria.',
            'fecha_nacimiento.date'        => 'La fecha de nacimiento debe ser una fecha válida.',
            'genero.required'              => 'El género es obligatorio.',
            'telefono.unique'              => 'El número de teléfono ya está registrado.',
            'celular.unique'               => 'El número de celular ya está registrado.',
            'email.required'               => 'El correo electrónico es obligatorio.',
            'email.email'                  => 'El correo electrónico debe ser válido.',
            'email.unique'                 => 'El correo electrónico ya está en uso.',
            'pais_id.required'             => 'El país es obligatorio.',
            'pais_id.exists'               => 'El país seleccionado no es válido.',
            'departamento_id.required'     => 'El departamento es obligatorio.',
            'departamento_id.exists'       => 'El departamento seleccionado no es válido.',
            'municipio_id.required'        => 'El municipio es obligatorio.',
            'municipio_id.exists'          => 'El municipio seleccionado no es válido.',
            'direccion.required'           => 'La dirección es obligatoria.',
            'direccion.string'             => 'La dirección debe ser una cadena de texto.',
            'direccion.max'                => 'La dirección no puede tener más de 255 caracteres.',
            'caracterizacion_ids.array'    => 'Las caracterizaciones deben enviarse en una lista válida.',
            'caracterizacion_ids.*.integer'=> 'El identificador de caracterización no es válido.',
            'caracterizacion_ids.*.exists' => 'Alguna de las caracterizaciones seleccionadas no existe.',
        ];
    }
}
