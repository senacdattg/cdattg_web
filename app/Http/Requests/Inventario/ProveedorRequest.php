<?php

namespace App\Http\Requests\Inventario;

use Illuminate\Foundation\Http\FormRequest;

class ProveedorRequest extends FormRequest
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
            $proveedorId = $this->route('proveedor');
            return [
                'proveedor' => 'required|unique:proveedores,proveedor,' . $proveedorId,
                'nit' => 'nullable|string|max:50|unique:proveedores,nit,' . $proveedorId,
                'email' => 'nullable|email|max:255|unique:proveedores,email,' . $proveedorId,
                'telefono' => 'nullable|string|max:10',
                'direccion' => 'nullable|string|max:255',
                'departamento_id' => 'nullable|exists:departamentos,id',
                'municipio_id' => 'nullable|exists:municipios,id',
                'contacto' => 'nullable|string|max:100',
                'estado_id' => 'nullable|exists:parametros_temas,id'
            ];
        }

        // Store
        return [
            'proveedor' => 'required|unique:proveedores,proveedor',
            'nit' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:10',
            'direccion' => 'nullable|string|max:255',
            'departamento_id' => 'nullable|exists:departamentos,id',
            'municipio_id' => 'nullable|exists:municipios,id',
            'contacto' => 'nullable|string|max:100',
            'estado_id' => 'nullable|exists:parametros_temas,id'
        ];
    }
}
