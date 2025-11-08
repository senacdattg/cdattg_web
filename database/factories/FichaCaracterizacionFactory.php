<?php

namespace Database\Factories;

use App\Models\Ambiente;
use App\Models\FichaCaracterizacion;
use App\Models\Instructor;
use App\Models\JornadaFormacion;
use App\Models\ProgramaFormacion;
use App\Models\Sede;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FichaCaracterizacion>
 */
class FichaCaracterizacionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = FichaCaracterizacion::class;

    public function definition(): array
    {
        $programaId = ProgramaFormacion::query()->inRandomOrder()->value('id') ?? 1;
        $modalidades = [18, 19, 20];
        $jornadaId = JornadaFormacion::query()->inRandomOrder()->value('id') ?? 1;
        $sedeId = Sede::query()->inRandomOrder()->value('id') ?? 1;
        $ambienteId = Ambiente::query()->inRandomOrder()->value('id') ?? 1;

        $fechaInicio = $this->faker->dateTimeBetween('-6 months', '+2 months');
        $fechaFin = (clone $fechaInicio)->modify('+' . $this->faker->numberBetween(12, 24) . ' months');

        return [
            'programa_formacion_id' => $programaId,
            'ficha' => $this->faker->unique()->numerify('29#####'),
            'instructor_id' => Instructor::factory(),
            'fecha_inicio' => $fechaInicio->format('Y-m-d'),
            'fecha_fin' => $fechaFin->format('Y-m-d'),
            'ambiente_id' => $ambienteId,
            'modalidad_formacion_id' => $this->faker->randomElement($modalidades),
            'sede_id' => $sedeId,
            'jornada_id' => $jornadaId,
            'total_horas' => $this->faker->numberBetween(1200, 3200),
            'user_create_id' => 1,
            'user_edit_id' => 1,
            'status' => $this->faker->boolean(90),
        ];
    }
}
