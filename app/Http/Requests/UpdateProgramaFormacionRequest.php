<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProgramaFormacionRequest extends FormRequest
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
        $programaId = $this->route('programa');
        
        return [
            'codigo' => 'required|string|max:50|unique:programas_formacion,codigo,' . $programaId,
            'nombre' => 'required|string|max:255|unique:programas_formacion,nombre,' . $programaId,
            'red_conocimiento_id' => 'required|exists:red_conocimientos,id',
            'nivel_formacion_id' => 'required|exists:parametros,id',
            'horas_totales' => 'required|integer|min:1|max:20000',
            'horas_etapa_lectiva' => 'required|integer|min:1|max:20000',
            'horas_etapa_productiva' => 'required|integer|min:1|max:20000',
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
            'codigo.required' => 'El código del programa es obligatorio.',
            'codigo.string' => 'El código debe ser una cadena de texto.',
            'codigo.max' => 'El código no puede tener más de 50 caracteres.',
            'codigo.unique' => 'Ya existe un programa con este código.',
            
            'nombre.required' => 'El nombre del programa es obligatorio.',
            'nombre.string' => 'El nombre debe ser una cadena de texto.',
            'nombre.max' => 'El nombre no puede tener más de 255 caracteres.',
            'nombre.unique' => 'Ya existe un programa con este nombre.',
            
            'red_conocimiento_id.required' => 'La red de conocimiento es obligatoria.',
            'red_conocimiento_id.exists' => 'La red de conocimiento seleccionada no es válida.',
            
            'nivel_formacion_id.required' => 'El nivel de formación es obligatorio.',
            'nivel_formacion_id.exists' => 'El nivel de formación seleccionado no es válido.',

            'horas_totales.required' => 'Las horas totales del programa son obligatorias.',
            'horas_totales.integer' => 'Las horas totales deben ser un número entero.',
            'horas_totales.min' => 'Las horas totales deben ser al menos 1.',
            'horas_totales.max' => 'Las horas totales no pueden exceder 20000.',

            'horas_etapa_lectiva.required' => 'Las horas de la etapa lectiva son obligatorias.',
            'horas_etapa_lectiva.integer' => 'Las horas de la etapa lectiva deben ser un número entero.',
            'horas_etapa_lectiva.min' => 'Las horas de la etapa lectiva deben ser al menos 1.',
            'horas_etapa_lectiva.max' => 'Las horas de la etapa lectiva no pueden exceder 20000.',

            'horas_etapa_productiva.required' => 'Las horas de la etapa productiva son obligatorias.',
            'horas_etapa_productiva.integer' => 'Las horas de la etapa productiva deben ser un número entero.',
            'horas_etapa_productiva.min' => 'Las horas de la etapa productiva deben ser al menos 1.',
            'horas_etapa_productiva.max' => 'Las horas de la etapa productiva no pueden exceder 20000.',
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
            'codigo' => 'código del programa',
            'nombre' => 'nombre del programa',
            'red_conocimiento_id' => 'red de conocimiento',
            'nivel_formacion_id' => 'nivel de formación',
            'horas_totales' => 'horas totales del programa',
            'horas_etapa_lectiva' => 'horas etapa lectiva',
            'horas_etapa_productiva' => 'horas etapa productiva',
        ];
    }

    /**
     * Valida que la suma de duraciones y horas por etapa no supere el total del programa.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $horasTotales = (int) $this->input('horas_totales');
            $horasLectiva = (int) $this->input('horas_etapa_lectiva');
            $horasProductiva = (int) $this->input('horas_etapa_productiva');

            if (($horasLectiva + $horasProductiva) !== $horasTotales) {
                $validator->errors()->add(
                    'horas_totales',
                    'La suma de las horas lectivas y productivas debe ser igual al total de horas del programa.'
                );
            }
        });
    }
}
