<?php

namespace App\Http\Requests\Inventario;

use Illuminate\Foundation\Http\FormRequest;

class ProductoRequest extends FormRequest
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
        // Validación para agregar al carrito
        if ($this->routeIs('inventario.productos.agregar-carrito')) {
            return [
                'producto_id' => 'required|exists:productos,id',
                'cantidad' => 'required|integer|min:1'
            ];
        }

        // Validación para update
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $productoId = $this->route('producto');
            return [
                'producto' => 'required|unique:productos,producto,' . $productoId,
                'tipo_producto_id' => 'required|exists:parametros_temas,id',
                'descripcion' => 'required|string',
                'peso' => 'required|numeric|min:0',
                'unidad_medida_id' => 'required|exists:parametros_temas,id',
                'cantidad' => 'required|integer|min:0',
                'codigo_barras' => 'nullable|string',
                'estado_producto_id' => 'required|exists:parametros_temas,id',
                'categoria_id' => 'required|exists:parametros,id',
                'marca_id' => 'required|exists:parametros,id',
                'contrato_convenio_id' => 'required|exists:contratos_convenios,id',
                'ambiente_id' => 'required|exists:ambientes,id',
                'fecha_vencimiento' => 'nullable|date',
                'imagen' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
            ];
        }

        // Validación para store (crear)
        return [
            'producto' => 'required|unique:productos',
            'tipo_producto_id' => 'required|exists:parametros_temas,id',
            'descripcion' => 'required|string',
            'peso' => 'required|numeric|min:0',
            'unidad_medida_id' => 'required|exists:parametros_temas,id',
            'cantidad' => 'required|integer|min:1',
            'codigo_barras' => 'nullable|string',
            'estado_producto_id' => 'required|exists:parametros_temas,id',
            'categoria_id' => 'required|exists:parametros,id',
            'marca_id' => 'required|exists:parametros,id',
            'contrato_convenio_id' => 'required|exists:contratos_convenios,id',
            'ambiente_id' => 'required|exists:ambientes,id',
            'proveedor_id' => 'required|exists:proveedores,id',
            'fecha_vencimiento' => 'nullable|date',
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ];
    }
}
