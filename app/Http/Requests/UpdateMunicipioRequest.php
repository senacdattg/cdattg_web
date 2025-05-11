<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMunicipioRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        // Obtener el ID del parámetro que se está actualizando.
        // Se asume que la ruta tiene un parámetro 'parametro'
        $parametroId = $this->route('parametro')->id;

        return [
            'name'   => 'required|string|max:255|unique:parametros,name,' . $parametroId,
            'status' => 'required|boolean',
        ];
    }
}
