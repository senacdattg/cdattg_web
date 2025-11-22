<?php

namespace App\Http\Requests\Inventario;

use Illuminate\Foundation\Http\FormRequest;

class ContratoConvenioRequest extends FormRequest
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
        // Update
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $contratoId = $this->route('contratos_convenio') ? $this->route('contratos_convenio')->id : null;
            return [
                'name' => 'required|string|max:255|unique:contratos_convenios,name,' . $contratoId,
                'codigo' => 'nullable|string|max:100|unique:contratos_convenios,codigo,' . $contratoId,
                'proveedor_id' => 'nullable|exists:proveedores,id',
                'fecha_inicio' => 'nullable|date',
                'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
                'estado_id' => 'required|exists:parametros_temas,id',
            ];
        }

        // Store
        return [
            'name' => 'required|string|max:255|unique:contratos_convenios,name',
            'codigo' => 'nullable|string|max:100|unique:contratos_convenios,codigo',
            'proveedor_id' => 'nullable|exists:proveedores,id',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'estado_id' => 'required|exists:parametros_temas,id',
        ];
    }
}
