<?php

namespace Database\Factories;

use App\Models\FichaCaracterizacion;
use App\Models\Instructor;
use App\Models\InstructorFichaCaracterizacion;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InstructorFichaCaracterizacion>
 */
class InstructorFichaCaracterizacionFactory extends Factory
{
    protected $model = InstructorFichaCaracterizacion::class;

    public function definition(): array
    {
        $fechaInicio = Carbon::instance($this->faker->dateTimeBetween('-4 months', 'now'));
        $fechaFin = (clone $fechaInicio)->addMonths($this->faker->numberBetween(6, 18));

        return [
            'instructor_id' => Instructor::factory(),
            'ficha_id' => FichaCaracterizacion::factory(),
            'fecha_inicio' => $fechaInicio->format('Y-m-d'),
            'fecha_fin' => $fechaFin->format('Y-m-d'),
            'total_horas_instructor' => $this->faker->numberBetween(180, 1200),
        ];
    }
}


