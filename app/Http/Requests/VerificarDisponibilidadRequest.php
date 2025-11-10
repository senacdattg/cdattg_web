<?php

namespace App\Http\Requests;

use App\Models\Instructor;
use Illuminate\Foundation\Http\FormRequest;

class VerificarDisponibilidadRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        /** @var Instructor|null $instructor */
        $instructor = $this->route('instructor');

        if (!$user || !$instructor) {
            return false;
        }

        return $user->can('verificarDisponibilidad', $instructor);
    }

    public function rules(): array
    {
        return [
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['required', 'date', 'after_or_equal:fecha_inicio'],
            'especialidad_requerida' => ['nullable', 'string'],
            'horas_semanales' => ['nullable', 'integer', 'min:0', 'max:48'],
        ];
    }
}
