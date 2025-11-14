<?php

namespace App\Http\Requests\Auth;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    private const STRING_MAX_LENGTH = 191;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tipo_documento' => ['required', 'integer'],
            'numero_documento' => [
                'required',
                'string',
                'max:' . 10,
                'unique:personas,numero_documento'
            ],
            'primer_nombre' => ['required', 'string', 'max:' . self::STRING_MAX_LENGTH],
            'segundo_nombre' => ['nullable', 'string', 'max:' . self::STRING_MAX_LENGTH],
            'primer_apellido' => ['required', 'string', 'max:' . self::STRING_MAX_LENGTH],
            'segundo_apellido' => ['nullable', 'string', 'max:' . self::STRING_MAX_LENGTH],
            'fecha_nacimiento' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    unset($attribute);
                    $fechaNacimiento = Carbon::parse($value);
                    $edadMinima = Carbon::now()->subYears(14);

                    if ($fechaNacimiento->gt($edadMinima)) {
                        $fail('Debe tener al menos 14 años para registrarse.');
                    }
                },
            ],
            'genero' => ['required', 'integer'],
            'telefono' => ['nullable', 'string', 'max:' . 7],
            'celular' => ['required', 'string', 'max:' . 10],
            'email' => [
                'required',
                'email',
                'max:' . self::STRING_MAX_LENGTH,
                'unique:personas,email',
                'unique:users,email'
            ],
            'pais_id' => ['required', 'exists:pais,id'],
            'departamento_id' => ['required', 'exists:departamentos,id'],
            'municipio_id' => ['required', 'exists:municipios,id'],
            'direccion' => ['required', 'string', 'max:' . self::STRING_MAX_LENGTH],
            'caracterizacion_ids' => ['required', 'array', 'min:1'],
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
            'caracterizacion_ids.required' => 'Debe seleccionar al menos una caracterización.',
            'caracterizacion_ids.array' => 'Las caracterizaciones seleccionadas no son válidas.',
            'caracterizacion_ids.min' => 'Debe seleccionar al menos una caracterización.',
            'caracterizacion_ids.*.integer' => 'Cada caracterización debe ser un identificador numérico válido.',
            'caracterizacion_ids.*.exists' => 'Alguna de las caracterizaciones seleccionadas no existe.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $caracterizacionIds = collect($this->input('caracterizacion_ids', []))
                ->filter()
                ->map(fn($id) => (int) $id)
                ->values()
                ->all();

            if (in_array(235, $caracterizacionIds, true) && count($caracterizacionIds) > 1) {
                $validator->errors()->add(
                    'caracterizacion_ids',
                    'No puede seleccionar "NINGUNA" junto con otras caracterizaciones.'
                );
            }
        });
    }

    protected function prepareForValidation(): void
    {
        $email = $this->input('email');

        $this->merge([
            'email' => $email ? strtolower($email) : $email,
        ]);
    }
}
