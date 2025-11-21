<?php

namespace App\Http\Requests;

use App\Models\Instructor;
use App\Models\Persona;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInstructorRequest extends FormRequest
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
        $instructor = $this->route('instructor');

        return [
            // Información institucional
            'regional_id' => 'required|integer|exists:regionals,id',
            'centro_formacion_id' => 'nullable|integer|exists:centro_formacions,id',
            'tipo_vinculacion_id' => 'nullable|integer|exists:parametros_temas,id',
            'jornadas' => 'nullable|array',
            'jornadas.*' => 'exists:parametros_temas,id',
            'fecha_ingreso_sena' => 'nullable|date|before_or_equal:today',
            'status' => 'required|boolean',
            
            // Experiencia
            'anos_experiencia' => 'nullable|integer|min:0|max:50',
            'experiencia_instructor_meses' => 'nullable|integer|min:0',
            'experiencia_laboral' => 'nullable|string|max:1000',
            
            // Formación académica
            'nivel_academico_id' => 'nullable|integer|exists:parametros_temas,id',
            'formacion_pedagogia' => 'nullable|string|max:500',
            'titulos_obtenidos' => 'nullable|array',
            'titulos_obtenidos.*' => 'nullable|string|max:255',
            'instituciones_educativas' => 'nullable|array',
            'instituciones_educativas.*' => 'nullable|string|max:255',
            'certificaciones_tecnicas' => 'nullable|array',
            'certificaciones_tecnicas.*' => 'nullable|string|max:255',
            'cursos_complementarios' => 'nullable|array',
            'cursos_complementarios.*' => 'nullable|string|max:255',
            
            // Competencias y habilidades
            'areas_experticia' => 'nullable',
            'competencias_tic' => 'nullable',
            'idiomas' => 'nullable|array',
            'idiomas.*.idioma' => 'nullable|string|max:100',
            'idiomas.*.nivel' => 'nullable|string|in:básico,intermedio,avanzado,nativo',
            'habilidades_pedagogicas' => 'nullable|array',
            'habilidades_pedagogicas.*' => 'in:virtual,presencial,dual',
            
            // Especialidades
            'especialidades' => 'nullable|array',
            'especialidades.*' => 'exists:red_conocimientos,id',
            
            // Información administrativa
            'numero_contrato' => 'nullable|string|max:100',
            'fecha_inicio_contrato' => 'nullable|date',
            'fecha_fin_contrato' => 'nullable|date|after_or_equal:fecha_inicio_contrato',
            'supervisor_contrato' => 'nullable|string|max:255',
            'eps' => 'nullable|string|max:100',
            'arl' => 'nullable|string|max:100',
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
            'regional_id.required' => 'La regional es obligatoria.',
            'regional_id.exists' => 'La regional seleccionada no es válida.',
            'centro_formacion_id.exists' => 'El centro de formación seleccionado no es válido.',
            'tipo_vinculacion_id.exists' => 'El tipo de vinculación seleccionado no es válido.',
            'jornadas.*.exists' => 'Una o más jornadas seleccionadas no son válidas.',
            'fecha_ingreso_sena.before_or_equal' => 'La fecha de ingreso no puede ser posterior a hoy.',
            'status.required' => 'El estado es obligatorio.',
            'anos_experiencia.max' => 'Los años de experiencia no pueden ser mayores a 50.',
            'experiencia_instructor_meses.min' => 'La experiencia como instructor no puede ser negativa.',
            'nivel_academico_id.exists' => 'El nivel académico seleccionado no es válido.',
            'idiomas.*.nivel.in' => 'El nivel de idioma debe ser: básico, intermedio, avanzado o nativo.',
            'habilidades_pedagogicas.*.in' => 'Las habilidades pedagógicas deben ser: virtual, presencial o dual.',
            'especialidades.*.exists' => 'Una o más especialidades seleccionadas no son válidas.',
            'fecha_fin_contrato.after_or_equal' => 'La fecha de fin de contrato debe ser posterior o igual a la fecha de inicio.'
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
            'regional_id' => 'regional',
            'centro_formacion_id' => 'centro de formación',
            'tipo_vinculacion_id' => 'tipo de vinculación',
            'jornadas' => 'jornadas de trabajo',
            'fecha_ingreso_sena' => 'fecha de ingreso al SENA',
            'anos_experiencia' => 'años de experiencia',
            'experiencia_instructor_meses' => 'experiencia como instructor',
            'experiencia_laboral' => 'experiencia laboral',
            'nivel_academico_id' => 'nivel académico',
            'formacion_pedagogia' => 'formación en pedagogía',
            'titulos_obtenidos' => 'títulos obtenidos',
            'instituciones_educativas' => 'instituciones educativas',
            'certificaciones_tecnicas' => 'certificaciones técnicas',
            'cursos_complementarios' => 'cursos complementarios',
            'areas_experticia' => 'áreas de experticia',
            'competencias_tic' => 'competencias TIC',
            'idiomas' => 'idiomas',
            'habilidades_pedagogicas' => 'habilidades pedagógicas',
            'especialidades' => 'especialidades',
            'numero_contrato' => 'número de contrato',
            'fecha_inicio_contrato' => 'fecha de inicio de contrato',
            'fecha_fin_contrato' => 'fecha de fin de contrato',
            'supervisor_contrato' => 'supervisor de contrato',
            'eps' => 'EPS',
            'arl' => 'ARL'
        ];
    }
}
