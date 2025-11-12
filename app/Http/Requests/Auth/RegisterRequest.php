<?php

namespace App\Http\Requests\Auth;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tipo_documento' => ['required', 'integer'],
            'numero_documento' => ['required', 'string', 'max:191', 'unique:personas,numero_documento'],
            'primer_nombre' => ['required', 'string', 'max:191'],
            'segundo_nombre' => ['nullable', 'string', 'max:191'],
            'primer_apellido' => ['required', 'string', 'max:191'],
            'segundo_apellido' => ['nullable', 'string', 'max:191'],
            'fecha_nacimiento' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $fechaNacimiento = Carbon::parse($value);
                    $edadMinima = Carbon::now()->subYears(14);

                    if ($fechaNacimiento->gt($edadMinima)) {
                        $fail('Debe tener al menos 14 años para registrarse.');
                    }
                },
            ],
            'genero' => ['required', 'integer'],
            'telefono' => ['nullable', 'string', 'max:191'],
            'celular' => ['required', 'string', 'max:191'],
            'email' => ['required', 'email', 'max:191', 'unique:personas,email'],
            'pais_id' => ['required', 'exists:pais,id'],
            'departamento_id' => ['required', 'exists:departamentos,id'],
            'municipio_id' => ['required', 'exists:municipios,id'],
            'direccion' => ['required', 'string', 'max:191'],
            'caracterizacion_ids' => ['nullable', 'array'],
            'caracterizacion_ids.*' => ['integer', 'exists:parametros,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'tipo_documento.required' => 'Seleccione un tipo de documento válido.',
            'numero_documento.unique' => 'El número de documento ya está registrado.',
            'email.unique' => 'El correo electrónico ya está registrado.',
            'pais_id.exists' => 'Seleccione un país válido.',
            'departamento_id.exists' => 'Seleccione un departamento válido.',
            'municipio_id.exists' => 'Seleccione un municipio válido.',
            'caracterizacion_ids.array' => 'Las caracterizaciones seleccionadas no son válidas.',
            'caracterizacion_ids.*.integer' => 'Cada caracterización debe ser un identificador numérico válido.',
            'caracterizacion_ids.*.exists' => 'Alguna de las caracterizaciones seleccionadas no existe.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $email = $this->input('email');

        $this->merge([
            'email' => $email ? strtolower($email) : $email,
        ]);
    }
}
