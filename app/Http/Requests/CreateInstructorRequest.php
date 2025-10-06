<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateInstructorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // La autorización se maneja en el controlador
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'persona_id' => [
                'required',
                'integer',
                'exists:personas,id'
            ],
            'regional_id' => [
                'required',
                'integer',
                'exists:regionals,id'
            ],
            'anos_experiencia' => [
                'nullable',
                'integer',
                'min:0',
                'max:50'
            ],
            'experiencia_laboral' => [
                'nullable',
                'string',
                'max:1000'
            ],
            'especialidades' => [
                'nullable',
                'array'
            ],
            'especialidades.*' => [
                'integer',
                'exists:red_conocimientos,id'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'persona_id.required' => 'Debe seleccionar una persona.',
            'persona_id.exists' => 'La persona seleccionada no existe o ya es instructor.',
            'regional_id.required' => 'Debe seleccionar una regional.',
            'regional_id.exists' => 'La regional seleccionada no existe.',
            'anos_experiencia.integer' => 'Los años de experiencia deben ser un número entero.',
            'anos_experiencia.min' => 'Los años de experiencia no pueden ser negativos.',
            'anos_experiencia.max' => 'Los años de experiencia no pueden ser mayores a 50.',
            'experiencia_laboral.max' => 'La experiencia laboral no puede exceder los 1000 caracteres.',
            'especialidades.array' => 'Las especialidades deben ser una lista válida.',
            'especialidades.*.exists' => 'Una o más especialidades seleccionadas no existen.'
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validaciones adicionales de negocio
            if ($this->has('persona_id')) {
                $persona = \App\Models\Persona::with(['instructor', 'user'])->find($this->input('persona_id'));
                
                if (!$persona) {
                    $validator->errors()->add('persona_id', 'La persona seleccionada no existe.');
                    return;
                }
                
                if ($persona->instructor) {
                    $validator->errors()->add('persona_id', 'Esta persona ya es instructor.');
                }
                
                if (!$persona->user) {
                    $validator->errors()->add('persona_id', 'Esta persona no tiene un usuario asociado.');
                }
            }
        });
    }
}
