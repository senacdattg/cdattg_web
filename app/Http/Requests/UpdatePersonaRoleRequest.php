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
            'roles' => [
                'nullable',
                'array',
            ],
            'roles.*' => [
                'string',
                Rule::exists('roles', 'name'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'roles.array' => 'La selección de roles no es válida.',
            'roles.*.exists' => 'Alguno de los roles seleccionados no es válido.',
        ];
    }
}

