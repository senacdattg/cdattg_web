<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PersonaImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'archivo_excel' => 'required|file|mimes:xlsx,xls,csv|max:20480',
        ];
    }

    public function messages(): array
    {
        return [
            'archivo_excel.required' => 'Debes seleccionar un archivo para importar.',
            'archivo_excel.mimes' => 'El archivo debe ser de tipo Excel (.xlsx, .xls) o CSV.',
            'archivo_excel.max' => 'El archivo no debe superar los 20MB.',
        ];
    }
}
