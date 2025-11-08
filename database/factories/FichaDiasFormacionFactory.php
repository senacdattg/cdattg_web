<?php

namespace Database\Factories;

use App\Models\FichaCaracterizacion;
use App\Models\FichaDiasFormacion;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FichaDiasFormacion>
 */
class FichaDiasFormacionFactory extends Factory
{
    protected $model = FichaDiasFormacion::class;

    public function definition(): array
    {
        $horaInicio = $this->faker->randomElement(['06:30:00', '08:00:00', '13:00:00']);
        $duracionHoras = $this->faker->numberBetween(2, 6);
        $horaFin = Carbon::createFromFormat('H:i:s', $horaInicio)
            ->addHours($duracionHoras)
            ->format('H:i:s');

        return [
            'ficha_id' => FichaCaracterizacion::factory(),
            'dia_id' => $this->faker->numberBetween(12, 17),
            'hora_inicio' => $horaInicio,
            'hora_fin' => $horaFin,
        ];
    }
}


