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
            // Campos básicos
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
            ],
            // Información laboral
            'tipo_vinculacion_id' => [
                'nullable',
                'integer',
                'exists:parametros_temas,id'
            ],
            'centro_formacion_id' => [
                'nullable',
                'integer',
                'exists:centro_formacions,id'
            ],
            'jornadas' => [
                'nullable',
                'array'
            ],
            'jornadas.*' => [
                'integer',
                'exists:jornadas_formacion,id'
            ],
            'experiencia_instructor_meses' => [
                'nullable',
                'integer',
                'min:0'
            ],
            'fecha_ingreso_sena' => [
                'nullable',
                'date'
            ],
            // Formación académica
            'nivel_academico_id' => [
                'nullable',
                'integer',
                'exists:parametros_temas,id'
            ],
            'titulos_obtenidos' => [
                'nullable',
                'array'
            ],
            'titulos_obtenidos.*' => [
                'string',
                'max:255'
            ],
            'instituciones_educativas' => [
                'nullable',
                'array'
            ],
            'instituciones_educativas.*' => [
                'string',
                'max:255'
            ],
            'certificaciones_tecnicas' => [
                'nullable',
                'array'
            ],
            'certificaciones_tecnicas.*' => [
                'string',
                'max:255'
            ],
            'cursos_complementarios' => [
                'nullable',
                'array'
            ],
            'cursos_complementarios.*' => [
                'string',
                'max:255'
            ],
            'formacion_pedagogia' => [
                'nullable',
                'string',
                'max:1000'
            ],
            // Competencias y habilidades
            'areas_experticia' => [
                'nullable'
            ],
            'competencias_tic' => [
                'nullable'
            ],
            'idiomas' => [
                'nullable',
                'array'
            ],
            'idiomas.*.idioma' => [
                'required_with:idiomas',
                'string',
                'max:100'
            ],
            'idiomas.*.nivel' => [
                'required_with:idiomas',
                'string',
                'in:básico,intermedio,avanzado,nativo'
            ],
            'habilidades_pedagogicas' => [
                'nullable',
                'array'
            ],
            'habilidades_pedagogicas.*' => [
                'string',
                'in:virtual,presencial,dual'
            ],
            // Documentos adjuntos
            'documentos_adjuntos' => [
                'nullable',
                'array'
            ],
            // Información administrativa
            'numero_contrato' => [
                'nullable',
                'string',
                'max:100'
            ],
            'fecha_inicio_contrato' => [
                'nullable',
                'date'
            ],
            'fecha_fin_contrato' => [
                'nullable',
                'date',
                'after_or_equal:fecha_inicio_contrato'
            ],
            'supervisor_contrato' => [
                'nullable',
                'string',
                'max:255'
            ],
            'eps' => [
                'nullable',
                'string',
                'max:255'
            ],
            'arl' => [
                'nullable',
                'string',
                'max:255'
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
            'especialidades.*.exists' => 'Una o más especialidades seleccionadas no existen.',
            'tipo_vinculacion.in' => 'El tipo de vinculación debe ser: planta, contratista o apoyo a la formación.',
            'centro_formacion_id.exists' => 'El centro de formación seleccionado no existe.',
            'jornada_trabajo_id.exists' => 'La jornada de trabajo seleccionada no existe.',
            'experiencia_instructor_meses.integer' => 'La experiencia como instructor en meses debe ser un número entero.',
            'experiencia_instructor_meses.min' => 'La experiencia como instructor en meses no puede ser negativa.',
            'fecha_ingreso_sena.date' => 'La fecha de ingreso al SENA debe ser una fecha válida.',
            'programas_formacion.*.exists' => 'Uno o más programas de formación seleccionados no existen.',
            'nivel_academico_id.exists' => 'El nivel académico seleccionado no existe.',
            'formacion_pedagogia.max' => 'La formación en pedagogía no puede exceder los 1000 caracteres.',
            'idiomas.*.idioma.required_with' => 'Debe especificar el idioma.',
            'idiomas.*.nivel.required_with' => 'Debe especificar el nivel del idioma.',
            'idiomas.*.nivel.in' => 'El nivel del idioma debe ser: básico, intermedio, avanzado o nativo.',
            'habilidades_pedagogicas.*.in' => 'Las habilidades pedagógicas deben ser: virtual, presencial o dual.',
            'fecha_fin_contrato.after_or_equal' => 'La fecha de fin de contrato debe ser igual o posterior a la fecha de inicio.'
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
