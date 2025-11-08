<?php

namespace Database\Factories;

use App\Models\AspiranteComplementario;
use App\Models\ComplementarioOfertado;
use App\Models\Persona;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AspiranteComplementario>
 */
class AspiranteComplementarioFactory extends Factory
{
    protected $model = AspiranteComplementario::class;

    public function definition(): array
    {
        return [
            'persona_id' => Persona::factory(),
            'complementario_id' => ComplementarioOfertado::factory(),
            'observaciones' => $this->faker->sentence(12),
            'estado' => $this->faker->randomElement([1, 2, 3]),
        ];
    }
}


