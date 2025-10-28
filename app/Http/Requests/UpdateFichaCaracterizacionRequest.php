<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Traits\ValidacionesSena;

class UpdateFichaCaracterizacionRequest extends FormRequest
{
    use ValidacionesSena;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('EDITAR PROGRAMA DE CARACTERIZACION');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $fichaId = $this->route('fichaCaracterizacion') ?? $this->route('id');
        
        return [
            // Validación del número de ficha (ignorando el registro actual)
            'ficha' => [
                'required',
                'string',
                'max:50',
                Rule::unique('fichas_caracterizacion', 'ficha')->ignore($fichaId)
            ],

            // Validación de relaciones obligatorias
            'programa_formacion_id' => [
                'required',
                'integer',
                'exists:programas_formacion,id'
            ],

            // Validación de fechas (sin restricción de fecha mínima)
            'fecha_inicio' => [
                'required',
                'date'
            ],
            'fecha_fin' => [
                'required',
                'date',
                'after_or_equal:fecha_inicio'
            ],

            // Validación de relaciones opcionales
            'instructor_id' => [
                'nullable',
                'integer',
                'exists:instructors,id'
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
        // Mantener valores existentes si no se proporcionan
        if (!$this->has('status')) {
            $this->merge(['status' => true]);
        }
        
        if (!$this->has('total_horas')) {
            $this->merge(['total_horas' => 0]);
        }
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
            $fichaId = $this->route('fichaCaracterizacion') ?? $this->route('id');

            // 1. Validar disponibilidad del ambiente (excluyendo la ficha actual)
            if ($this->ambiente_id && $this->fecha_inicio && $this->fecha_fin) {
                $validacionAmbiente = $this->validarDisponibilidadAmbiente(
                    $this->ambiente_id,
                    $this->fecha_inicio,
                    $this->fecha_fin,
                    $fichaId
                );
                
                if (!$validacionAmbiente['valido']) {
                    $validator->errors()->add('ambiente_id', $validacionAmbiente['mensaje']);
                }
            }

            // 2. Validar disponibilidad del instructor (excluyendo la ficha actual)
            if ($this->instructor_id && $this->fecha_inicio && $this->fecha_fin) {
                $validacionInstructor = $this->validarDisponibilidadInstructor(
                    $this->instructor_id,
                    $this->fecha_inicio,
                    $this->fecha_fin,
                    $fichaId
                );
                
                if (!$validacionInstructor['valido']) {
                    $validator->errors()->add('instructor_id', $validacionInstructor['mensaje']);
                }
            }

            // 3. Validar que el número de ficha sea único por programa (excluyendo la ficha actual)
            if ($this->ficha && $this->programa_formacion_id) {
                $validacionFicha = $this->validarFichaUnicaPorPrograma(
                    $this->ficha,
                    $this->programa_formacion_id,
                    $fichaId
                );
                
                if (!$validacionFicha['valido']) {
                    $validator->errors()->add('ficha', $validacionFicha['mensaje']);
                }
            }

            // 4. Validar reglas de negocio específicas del SENA (excluyendo la ficha actual)
            $datos = $this->all();
            $validacionReglas = $this->validarReglasNegocioSena($datos, $fichaId);
            
            if (!$validacionReglas['valido']) {
                $validator->errors()->add('reglas_negocio', $validacionReglas['mensaje']);
            }

            // 5. Validación adicional: verificar que no se cambien fechas si ya hay aprendices asignados
            if ($fichaId) {
                $ficha = \App\Models\FichaCaracterizacion::find($fichaId);
                if ($ficha && $ficha->tieneAprendices()) {
                    // Si hay aprendices, no permitir cambiar fechas de inicio
                    if ($this->fecha_inicio && $this->fecha_inicio !== $ficha->fecha_inicio) {
                        $validator->errors()->add('fecha_inicio', 'No se puede cambiar la fecha de inicio cuando ya hay aprendices asignados.');
                    }
                }
            }
        });
    }
}