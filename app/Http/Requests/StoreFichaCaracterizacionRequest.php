<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFichaCaracterizacionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('CREAR PROGRAMA DE CARACTERIZACION');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            // Validación del número de ficha
            'ficha' => [
                'required',
                'string',
                'max:50',
                'unique:fichas_caracterizacion,ficha'
            ],

            // Validación de relaciones obligatorias
            'programa_formacion_id' => [
                'required',
                'integer',
                'exists:programas_formacion,id'
            ],

            // Validación de fechas
            'fecha_inicio' => [
                'required',
                'date',
                'after_or_equal:today'
            ],
            'fecha_fin' => [
                'required',
                'date',
                'after:fecha_inicio'
            ],

            // Validación de relaciones opcionales
            'instructor_id' => [
                'nullable',
                'integer',
                'exists:instructores,id'
            ],
            'ambiente_id' => [
                'nullable',
                'integer',
                'exists:ambientes,id'
            ],
            'modalidad_formacion_id' => [
                'nullable',
                'integer',
                'exists:parametros,id'
            ],
            'sede_id' => [
                'nullable',
                'integer',
                'exists:sedes,id'
            ],
            'jornada_id' => [
                'nullable',
                'integer',
                'exists:jornadas_formacion,id'
            ],

            // Validación de campos numéricos
            'total_horas' => [
                'nullable',
                'integer',
                'min:1',
                'max:9999'
            ],

            // Validación del estado
            'status' => [
                'nullable',
                'boolean'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            // Mensajes para el número de ficha
            'ficha.required' => 'El número de ficha es obligatorio.',
            'ficha.string' => 'El número de ficha debe ser texto.',
            'ficha.max' => 'El número de ficha no puede exceder 50 caracteres.',
            'ficha.unique' => 'Ya existe una ficha con este número.',

            // Mensajes para programa de formación
            'programa_formacion_id.required' => 'El programa de formación es obligatorio.',
            'programa_formacion_id.integer' => 'El programa de formación debe ser un número entero.',
            'programa_formacion_id.exists' => 'El programa de formación seleccionado no existe.',

            // Mensajes para fechas
            'fecha_inicio.required' => 'La fecha de inicio es obligatoria.',
            'fecha_inicio.date' => 'La fecha de inicio debe ser una fecha válida.',
            'fecha_inicio.after_or_equal' => 'La fecha de inicio no puede ser anterior a hoy.',
            'fecha_fin.required' => 'La fecha de fin es obligatoria.',
            'fecha_fin.date' => 'La fecha de fin debe ser una fecha válida.',
            'fecha_fin.after' => 'La fecha de fin debe ser posterior a la fecha de inicio.',

            // Mensajes para instructor
            'instructor_id.integer' => 'El instructor debe ser un número entero.',
            'instructor_id.exists' => 'El instructor seleccionado no existe.',

            // Mensajes para ambiente
            'ambiente_id.integer' => 'El ambiente debe ser un número entero.',
            'ambiente_id.exists' => 'El ambiente seleccionado no existe.',

            // Mensajes para modalidad de formación
            'modalidad_formacion_id.integer' => 'La modalidad de formación debe ser un número entero.',
            'modalidad_formacion_id.exists' => 'La modalidad de formación seleccionada no existe.',

            // Mensajes para sede
            'sede_id.integer' => 'La sede debe ser un número entero.',
            'sede_id.exists' => 'La sede seleccionada no existe.',

            // Mensajes para jornada
            'jornada_id.integer' => 'La jornada debe ser un número entero.',
            'jornada_id.exists' => 'La jornada seleccionada no existe.',

            // Mensajes para total de horas
            'total_horas.integer' => 'El total de horas debe ser un número entero.',
            'total_horas.min' => 'El total de horas debe ser al menos 1.',
            'total_horas.max' => 'El total de horas no puede exceder 9999.',

            // Mensajes para estado
            'status.boolean' => 'El estado debe ser verdadero o falso.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes()
    {
        return [
            'ficha' => 'número de ficha',
            'programa_formacion_id' => 'programa de formación',
            'fecha_inicio' => 'fecha de inicio',
            'fecha_fin' => 'fecha de fin',
            'instructor_id' => 'instructor',
            'ambiente_id' => 'ambiente',
            'modalidad_formacion_id' => 'modalidad de formación',
            'sede_id' => 'sede',
            'jornada_id' => 'jornada',
            'total_horas' => 'total de horas',
            'status' => 'estado'
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        // Establecer valores por defecto
        $this->merge([
            'status' => $this->status ?? true,
            'total_horas' => $this->total_horas ?? 0,
        ]);
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validación adicional: verificar que las fechas no excedan un año
            if ($this->fecha_inicio && $this->fecha_fin) {
                $fechaInicio = \Carbon\Carbon::parse($this->fecha_inicio);
                $fechaFin = \Carbon\Carbon::parse($this->fecha_fin);
                
                if ($fechaFin->diffInMonths($fechaInicio) > 12) {
                    $validator->errors()->add('fecha_fin', 'La duración del programa no puede exceder 12 meses.');
                }
            }

            // Validación adicional: verificar que el ambiente pertenezca a la sede
            if ($this->sede_id && $this->ambiente_id) {
                $ambiente = \App\Models\Ambiente::find($this->ambiente_id);
                if ($ambiente && $ambiente->sede_id !== (int) $this->sede_id) {
                    $validator->errors()->add('ambiente_id', 'El ambiente seleccionado no pertenece a la sede especificada.');
                }
            }
        });
    }
}