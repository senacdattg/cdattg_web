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
        $genero = $this->faker->randomElement([9, 10, 11]); // 9: MASCULINO, 10: FEMENINO, 11: NO DEFINE
        $primerNombre = $genero === 9 ? $this->faker->firstNameMale() : $this->faker->firstNameFemale();
        
        return [
            'tipo_documento' => $this->faker->randomElement([3, 4, 5, 6, 7, 8]), // IDs de tipos de documento
            'numero_documento' => $this->faker->unique()->numerify('##########'),
            'primer_nombre' => $primerNombre,
            'segundo_nombre' => $this->faker->optional(0.7)->firstName(),
            'primer_apellido' => $this->faker->lastName(),
            'segundo_apellido' => $this->faker->optional(0.7)->lastName(),
            'fecha_nacimiento' => $this->faker->dateTimeBetween('-60 years', '-18 years')->format('Y-m-d'),
            'genero' => $genero,
            'telefono' => $this->faker->optional(0.8)->numerify('##########'),
            'celular' => $this->faker->numerify('3##########'),
            'email' => $this->faker->unique()->safeEmail(),
            'pais_id' => 1, // Colombia por defecto
            'departamento_id' => $this->faker->numberBetween(1, 32), // Departamentos de Colombia
            'municipio_id' => $this->faker->numberBetween(1, 1100), // Municipios de Colombia
            'direccion' => $this->faker->address(),
            'status' => $this->faker->randomElement([1, 2]), // 1: ACTIVO, 2: INACTIVO
            'user_create_id' => 1,
            'user_edit_id' => 1,
        ];
    }
}
