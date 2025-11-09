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
        $observaciones = [
            'El aspirante cumple con todos los requisitos.',
            'Pendiente de documentación adicional.',
            'Completar proceso de inscripción.',
            'Revisar disponibilidad de horarios.',
            'Aspirante en lista de espera.',
        ];
        
        return [
            'persona_id' => Persona::factory(),
            'complementario_id' => ComplementarioOfertado::factory(),
            'observaciones' => $observaciones[array_rand($observaciones)],
            'estado' => [1, 2, 3][array_rand([1, 2, 3])],
        ];
    }
}


