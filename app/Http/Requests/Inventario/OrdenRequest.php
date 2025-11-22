<?php

namespace App\Http\Requests\Inventario;

use Illuminate\Foundation\Http\FormRequest;

class OrdenRequest extends FormRequest
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
        // Validación para préstamos y salidas
        if ($this->routeIs('inventario.prestamos-salidas.store')) {
            return [
                'rol' => 'required|string|max:100',
                'programa_formacion' => 'required|string|max:255',
                'tipo' => 'required|in:prestamo,salida',
                'fecha_devolucion' => 'required_if:tipo,prestamo|nullable|date|after:today',
                'descripcion' => 'required|string',
                'carrito' => 'required|json'
            ];
        }

        // Validación para store y update (órdenes normales)
        return [
            'descripcion_orden' => 'required|string',
            'tipo_orden_id' => 'required|exists:parametros_temas,id',
            'fecha_devolucion' => 'nullable|date|after:today',
            'productos' => 'required|array|min:1',
            'productos.*.producto_id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.estado_orden_id' => 'required|exists:parametros_temas,id'
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
            'fecha_devolucion.after' => 'La fecha de devolución debe ser posterior a hoy.',
            'fecha_devolucion.required_if' => 'La fecha de devolución es obligatoria para préstamos.',
            'fecha_devolucion.date' => 'La fecha de devolución debe ser una fecha válida.',
        ];
    }
}
