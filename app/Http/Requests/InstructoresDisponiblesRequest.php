<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InstructoresDisponiblesRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        if (!$user) {
            return false;
        }

        return $user->can('VER INSTRUCTOR');
    }

    public function rules(): array
    {
        return [
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['required', 'date', 'after_or_equal:fecha_inicio'],
            'especialidad_requerida' => ['nullable', 'string'],
            'regional_id' => ['nullable', 'integer', 'exists:regionals,id'],
        ];
    }
}
