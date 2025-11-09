<?php

namespace Database\Factories;

use App\Models\Aprendiz;
use App\Models\AprendizFicha;
use App\Models\FichaCaracterizacion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AprendizFicha>
 */
class AprendizFichaFactory extends Factory
{
    protected $model = AprendizFicha::class;

    public function definition(): array
    {
        return [
            'aprendiz_id' => Aprendiz::factory(),
            'ficha_id' => FichaCaracterizacion::factory(),
        ];
    }
}


