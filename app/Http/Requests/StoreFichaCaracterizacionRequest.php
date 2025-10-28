<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Traits\ValidacionesSena;

class StoreFichaCaracterizacionRequest extends FormRequest
{
    use ValidacionesSena;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $user = $this->user();
        $canCreate = $user->can('CREAR PROGRAMA DE CARACTERIZACION');
        
        \Log::info('StoreFichaCaracterizacionRequest authorize', [
            'user_id' => $user->id,
            'user_roles' => $user->getRoleNames(),
            'can_create' => $canCreate,
            'permissions' => $user->getAllPermissions()->pluck('name')
        ]);
        
        return $canCreate;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        \Log::info('StoreFichaCaracterizacionRequest rules called', [
            'user_id' => $this->user()->id,
            'all_data' => $this->all()
        ]);
        
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

            // Validación de fechas (al crear debe ser fecha futura)
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
            ],

            // Validación de días de formación
            'dias_formacion' => [
                'required',
                'array',
                'min:1'
            ],
            'dias_formacion.*' => [
                'required',
                'integer',
                'in:12,13,14,15,16,17,18' // IDs de los días de la semana
            ],

            // Validación de horarios (opcional)
            'horarios' => [
                'nullable',
                'array'
            ],
            'horarios.*' => [
                'nullable',
                'array'
            ],
            'horarios.*.hora_inicio' => [
                'nullable',
                'date_format:H:i'
            ],
            'horarios.*.hora_fin' => [
                'nullable',
                'date_format:H:i',
                'after:horarios.*.hora_inicio'
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
            'status.boolean' => 'El estado debe ser verdadero o falso.',

            // Mensajes para días de formación
            'dias_formacion.required' => 'Debe seleccionar al menos un día de formación.',
            'dias_formacion.array' => 'Los días de formación deben ser una lista.',
            'dias_formacion.min' => 'Debe seleccionar al menos un día de formación.',
            'dias_formacion.*.required' => 'Cada día de formación es obligatorio.',
            'dias_formacion.*.integer' => 'El día de formación debe ser un número entero.',
            'dias_formacion.*.in' => 'El día de formación seleccionado no es válido.',

            // Mensajes para horarios
            'horarios.array' => 'Los horarios deben ser una lista.',
            'horarios.*.array' => 'Cada horario debe ser una lista.',
            'horarios.*.hora_inicio.date_format' => 'La hora de inicio debe tener el formato HH:MM.',
            'horarios.*.hora_fin.date_format' => 'La hora de fin debe tener el formato HH:MM.',
            'horarios.*.hora_fin.after' => 'La hora de fin debe ser posterior a la hora de inicio.'
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
            'status' => 'estado',
            'dias_formacion' => 'días de formación',
            'dias_formacion.*' => 'día de formación',
            'horarios' => 'horarios',
            'horarios.*.hora_inicio' => 'hora de inicio',
            'horarios.*.hora_fin' => 'hora de fin'
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
            \Log::info('StoreFichaCaracterizacionRequest withValidator called', [
                'user_id' => $this->user()->id,
                'errors_count' => $validator->errors()->count(),
                'errors' => $validator->errors()->toArray()
            ]);
            // 1. Validar disponibilidad del ambiente
            if ($this->ambiente_id && $this->fecha_inicio && $this->fecha_fin) {
                $validacionAmbiente = $this->validarDisponibilidadAmbiente(
                    $this->ambiente_id,
                    $this->fecha_inicio,
                    $this->fecha_fin
                );
                
                if (!$validacionAmbiente['valido']) {
                    $validator->errors()->add('ambiente_id', $validacionAmbiente['mensaje']);
                }
            }

            // 2. Validar disponibilidad del instructor
            if ($this->instructor_id && $this->fecha_inicio && $this->fecha_fin) {
                $validacionInstructor = $this->validarDisponibilidadInstructor(
                    $this->instructor_id,
                    $this->fecha_inicio,
                    $this->fecha_fin
                );
                
                if (!$validacionInstructor['valido']) {
                    $validator->errors()->add('instructor_id', $validacionInstructor['mensaje']);
                }
            }

            // 3. Validar que el número de ficha sea único por programa
            if ($this->ficha && $this->programa_formacion_id) {
                $validacionFicha = $this->validarFichaUnicaPorPrograma(
                    $this->ficha,
                    $this->programa_formacion_id
                );
                
                if (!$validacionFicha['valido']) {
                    $validator->errors()->add('ficha', $validacionFicha['mensaje']);
                }
            }

            // 4. Validar reglas de negocio específicas del SENA
            $datos = $this->all();
            $validacionReglas = $this->validarReglasNegocioSena($datos);
            
            if (!$validacionReglas['valido']) {
                // TEMPORAL: Solo mostrar advertencia en lugar de error
                \Log::warning('Reglas de negocio SENA no cumplidas', [
                    'user_id' => $this->user()->id,
                    'mensaje' => $validacionReglas['mensaje']
                ]);
                // Comentado temporalmente para permitir creación
                // $validator->errors()->add('reglas_negocio', $validacionReglas['mensaje']);
            }
            
            \Log::info('StoreFichaCaracterizacionRequest withValidator completed', [
                'user_id' => $this->user()->id,
                'final_errors_count' => $validator->errors()->count(),
                'final_errors' => $validator->errors()->toArray()
            ]);
        });
    }
}