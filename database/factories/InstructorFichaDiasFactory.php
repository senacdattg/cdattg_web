<?php

namespace Database\Factories;

use App\Models\InstructorFichaCaracterizacion;
use App\Models\InstructorFichaDias;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InstructorFichaDias>
 */
class InstructorFichaDiasFactory extends Factory
{
    protected $model = InstructorFichaDias::class;

    public function definition(): array
    {
        $horasInicio = ['06:30:00', '08:00:00', '14:00:00'];
        $horaInicio = $horasInicio[array_rand($horasInicio)];
        $duracion = rand(2, 5);
        $horaFin = Carbon::createFromFormat('H:i:s', $horaInicio)
            ->addHours($duracion)
            ->format('H:i:s');

        return [
            'instructor_ficha_id' => InstructorFichaCaracterizacion::factory(),
            'dia_id' => rand(12, 17),
            'hora_inicio' => $horaInicio,
            'hora_fin' => $horaFin,
        ];
    }
}


