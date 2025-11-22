<?php

namespace App\Http\Requests\Inventario;

use Illuminate\Foundation\Http\FormRequest;

class DevolucionRequest extends FormRequest
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
        return [
            'detalle_orden_id' => 'required|integer|exists:detalle_ordenes,id',
            'cantidad_devuelta' => 'required|integer|min:0',
            'observaciones' => 'nullable|string|max:500'
        ];
    }
}
