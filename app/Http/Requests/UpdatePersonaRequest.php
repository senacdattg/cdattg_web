<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePersonaRequest extends FormRequest
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
        // Se usa route model binding para obtener la instancia de Persona
        $persona = $this->route('persona');
        $personaId = $persona ? $persona->id : null;
        // Se verifica que exista un usuario asociado; de lo contrario, se ignora la validación de unicidad en users
        $userId = ($persona && $persona->user) ? $persona->user->id : null;

        return [
            'tipo_documento'      => 'required',
            'numero_documento'    => 'required',
            'primer_nombre'       => 'required|string',
            'segundo_nombre'      => 'nullable|string',
            'primer_apellido'     => 'required|string',
            'segundo_apellido'    => 'nullable|string',
            'fecha_nacimiento'    => 'required|date',
            'genero'              => 'required',
            'telefono'            => 'nullable|unique:personas,telefono,' . $personaId,
            'celular'             => 'nullable|unique:personas,celular,' . $personaId,
            'email'               => [
                'required',
                'email',
                Rule::unique('personas', 'email')->ignore($personaId),
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'pais_id'             => 'required|exists:pais,id',
            'departamento_id'     => 'required|exists:departamentos,id',
            'municipio_id'        => 'required|exists:municipios,id',
            'direccion'           => 'required|string|max:255',
            'caracterizacion_ids'   => 'nullable|array',
            'caracterizacion_ids.*' => 'integer|exists:categorias_caracterizacion_complementarios,id',
        ];
    }

    /**
     * (Opcional) Mensajes personalizados para la validación.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'tipo_documento.required'      => 'El tipo de documento es obligatorio.',
            'numero_documento.required'    => 'El número de documento es obligatorio.',
            'primer_nombre.required'       => 'El primer nombre es obligatorio.',
            'primer_apellido.required'     => 'El primer apellido es obligatorio.',
            'fecha_nacimiento.required'    => 'La fecha de nacimiento es obligatoria.',
            'fecha_nacimiento.date'        => 'La fecha de nacimiento debe ser una fecha válida.',
            'genero.required'              => 'El género es obligatorio.',
            'email.required'               => 'El correo electrónico es obligatorio.',
            'email.email'                  => 'El correo electrónico debe ser válido.',
            'email.unique'                 => 'El correo electrónico ya está en uso.',
            'telefono.unique'              => 'El número de teléfono ya está en uso.',
            'celular.unique'               => 'El número de celular ya está en uso.',
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
