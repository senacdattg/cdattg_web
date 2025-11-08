<?php

namespace Database\Factories;

use App\Models\Persona;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Persona>
 */
class PersonaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $genero = $this->faker->randomElement([9, 10, 11]);
        $primerNombre = match ($genero) {
            9 => $this->faker->firstNameMale(),
            10 => $this->faker->firstNameFemale(),
            default => $this->faker->firstName(),
        };

        $ubicaciones = [
            ['departamento_id' => 95, 'municipio_id' => 824],
            ['departamento_id' => 11, 'municipio_id' => 100],
            ['departamento_id' => 25, 'municipio_id' => 126],
            ['departamento_id' => 50, 'municipio_id' => 113],
            ['departamento_id' => 63, 'municipio_id' => 339],
            ['departamento_id' => 5, 'municipio_id' => 1],
            ['departamento_id' => 73, 'municipio_id' => 432],
        ];
        $ubicacion = $this->faker->randomElement($ubicaciones);

        return [
            'tipo_documento' => $this->faker->randomElement([3, 4, 5, 6]),
            'numero_documento' => $this->faker->unique()->numerify('##########'),
            'primer_nombre' => $primerNombre,
            'segundo_nombre' => $this->faker->optional(0.5)->firstName(),
            'primer_apellido' => $this->faker->lastName(),
            'segundo_apellido' => $this->faker->optional(0.5)->lastName(),
            'fecha_nacimiento' => $this->faker->dateTimeBetween('-55 years', '-20 years')->format('Y-m-d'),
            'genero' => $genero,
            'telefono' => $this->faker->optional(0.6)->numerify('60########'),
            'celular' => $this->faker->numerify('3#########'),
            'email' => $this->faker->unique()->safeEmail(),
            'pais_id' => 1,
            'departamento_id' => $ubicacion['departamento_id'],
            'municipio_id' => $ubicacion['municipio_id'],
            'direccion' => $this->faker->streetAddress(),
            'status' => 1,
            'user_create_id' => 1,
            'user_edit_id' => 1,
        ];
    }
}
