<?php

namespace Database\Factories;

use App\Models\Ambiente;
use App\Models\ComplementarioOfertado;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ComplementarioOfertado>
 */
class ComplementarioOfertadoFactory extends Factory
{
    protected $model = ComplementarioOfertado::class;

    public function definition(): array
    {
        $nombres = [
            'Auxiliar de Cocina',
            'Acabados en Madera',
            'Confección de Prendas',
            'Mecánica Básica Automotriz',
            'Cultivos de Huertas Urbanas',
            'Normatividad Laboral',
        ];

        $nombre = $this->faker->unique()->randomElement($nombres);
        $modalidades = [18, 19, 20];
        $jornadas = [1, 2, 3, 4];
        $ambienteId = Ambiente::query()->inRandomOrder()->value('id') ?? 1;

        return [
            'codigo' => strtoupper($this->faker->bothify('COMP###')),
            'nombre' => $nombre,
            'descripcion' => $this->faker->sentence(15),
            'duracion' => $this->faker->numberBetween(30, 80),
            'cupos' => $this->faker->numberBetween(10, 30),
            'estado' => $this->faker->randomElement([0, 1, 2]),
            'modalidad_id' => $this->faker->randomElement($modalidades),
            'jornada_id' => $this->faker->randomElement($jornadas),
            'ambiente_id' => $ambienteId,
        ];
    }
}


