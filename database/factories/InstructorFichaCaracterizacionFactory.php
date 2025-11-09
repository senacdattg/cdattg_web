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
        $mesesAtras = rand(0, 4);
        $fechaInicio = Carbon::now()->subMonths($mesesAtras);
        $mesesDuracion = rand(6, 18);
        $fechaFin = (clone $fechaInicio)->addMonths($mesesDuracion);

        return [
            'instructor_id' => Instructor::factory(),
            'ficha_id' => FichaCaracterizacion::factory(),
            'fecha_inicio' => $fechaInicio->format('Y-m-d'),
            'fecha_fin' => $fechaFin->format('Y-m-d'),
            'total_horas_instructor' => rand(180, 1200),
        ];
    }
}


