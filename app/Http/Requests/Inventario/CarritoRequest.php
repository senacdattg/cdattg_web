<?php

namespace App\Http\Requests\Inventario;

use Illuminate\Foundation\Http\FormRequest;

class CarritoRequest extends FormRequest
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
        // Si viene la ruta 'actualizar', validar solo cantidad
        if ($this->routeIs('inventario.carrito.actualizar')) {
            return [
                'cantidad' => 'required|integer|min:1',
            ];
        }

        // Para 'agregar', validar array de items
        return [
            'items' => 'required|array',
            'items.*.producto_id' => 'required|integer|exists:productos,id',
            'items.*.cantidad' => 'required|integer|min:1',
        ];
    }
}
