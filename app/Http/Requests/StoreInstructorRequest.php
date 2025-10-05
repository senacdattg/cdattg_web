<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInstructorRequest extends FormRequest
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
            'tipo_documento' => [
                'required',
                'integer',
                'exists:parametros,id'
            ],
            'numero_documento' => [
                'required',
                'string',
                'max:20',
                'unique:personas,numero_documento'
            ],
            'primer_nombre' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/'
            ],
            'segundo_nombre' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/'
            ],
            'primer_apellido' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/'
            ],
            'segundo_apellido' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/'
            ],
            'fecha_de_nacimiento' => [
                'required',
                'date',
                'before:today',
                'after:1900-01-01'
            ],
            'genero' => [
                'required',
                'integer',
                'exists:parametros,id'
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:personas,email',
                'unique:users,email'
            ],
            'regional_id' => [
                'required',
                'integer',
                'exists:regionales,id'
            ],
            'telefono' => [
                'nullable',
                'string',
                'max:20',
                'unique:personas,telefono'
            ],
            'celular' => [
                'nullable',
                'string',
                'max:20',
                'unique:personas,celular'
            ],
            'direccion' => [
                'nullable',
                'string',
                'max:500'
            ]
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
            'tipo_documento.required' => 'El tipo de documento es obligatorio.',
            'tipo_documento.integer' => 'El tipo de documento debe ser un número entero.',
            'tipo_documento.exists' => 'El tipo de documento seleccionado no es válido.',
            
            'numero_documento.required' => 'El número de documento es obligatorio.',
            'numero_documento.string' => 'El número de documento debe ser una cadena de texto.',
            'numero_documento.max' => 'El número de documento no puede tener más de 20 caracteres.',
            'numero_documento.unique' => 'El número de documento ya está registrado.',
            
            'primer_nombre.required' => 'El primer nombre es obligatorio.',
            'primer_nombre.string' => 'El primer nombre debe ser una cadena de texto.',
            'primer_nombre.max' => 'El primer nombre no puede tener más de 255 caracteres.',
            'primer_nombre.regex' => 'El primer nombre solo puede contener letras y espacios.',
            
            'segundo_nombre.string' => 'El segundo nombre debe ser una cadena de texto.',
            'segundo_nombre.max' => 'El segundo nombre no puede tener más de 255 caracteres.',
            'segundo_nombre.regex' => 'El segundo nombre solo puede contener letras y espacios.',
            
            'primer_apellido.required' => 'El primer apellido es obligatorio.',
            'primer_apellido.string' => 'El primer apellido debe ser una cadena de texto.',
            'primer_apellido.max' => 'El primer apellido no puede tener más de 255 caracteres.',
            'primer_apellido.regex' => 'El primer apellido solo puede contener letras y espacios.',
            
            'segundo_apellido.string' => 'El segundo apellido debe ser una cadena de texto.',
            'segundo_apellido.max' => 'El segundo apellido no puede tener más de 255 caracteres.',
            'segundo_apellido.regex' => 'El segundo apellido solo puede contener letras y espacios.',
            
            'fecha_de_nacimiento.required' => 'La fecha de nacimiento es obligatoria.',
            'fecha_de_nacimiento.date' => 'La fecha de nacimiento debe ser una fecha válida.',
            'fecha_de_nacimiento.before' => 'La fecha de nacimiento debe ser anterior a hoy.',
            'fecha_de_nacimiento.after' => 'La fecha de nacimiento debe ser posterior a 1900.',
            
            'genero.required' => 'El género es obligatorio.',
            'genero.integer' => 'El género debe ser un número entero.',
            'genero.exists' => 'El género seleccionado no es válido.',
            
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe tener un formato válido.',
            'email.max' => 'El correo electrónico no puede tener más de 255 caracteres.',
            'email.unique' => 'El correo electrónico ya está registrado.',
            
            'regional_id.required' => 'La regional es obligatoria.',
            'regional_id.integer' => 'La regional debe ser un número entero.',
            'regional_id.exists' => 'La regional seleccionada no es válida.',
            
            'telefono.string' => 'El teléfono debe ser una cadena de texto.',
            'telefono.max' => 'El teléfono no puede tener más de 20 caracteres.',
            'telefono.unique' => 'El número de teléfono ya está registrado.',
            
            'celular.string' => 'El celular debe ser una cadena de texto.',
            'celular.max' => 'El celular no puede tener más de 20 caracteres.',
            'celular.unique' => 'El número de celular ya está registrado.',
            
            'direccion.string' => 'La dirección debe ser una cadena de texto.',
            'direccion.max' => 'La dirección no puede tener más de 500 caracteres.'
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
            'tipo_documento' => 'tipo de documento',
            'numero_documento' => 'número de documento',
            'primer_nombre' => 'primer nombre',
            'segundo_nombre' => 'segundo nombre',
            'primer_apellido' => 'primer apellido',
            'segundo_apellido' => 'segundo apellido',
            'fecha_de_nacimiento' => 'fecha de nacimiento',
            'genero' => 'género',
            'email' => 'correo electrónico',
            'regional_id' => 'regional',
            'telefono' => 'teléfono',
            'celular' => 'celular',
            'direccion' => 'dirección'
        ];
    }
}
