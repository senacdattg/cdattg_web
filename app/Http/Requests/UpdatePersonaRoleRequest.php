<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePersonaRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->can('ASIGNAR PERMISOS');
    }

    public function rules(): array
    {
        return [
            'role' => [
                'required',
                'string',
                Rule::exists('roles', 'name'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'role.required' => 'Debes seleccionar un rol.',
            'role.exists' => 'El rol seleccionado no es válido.',
        ];
    }
}

