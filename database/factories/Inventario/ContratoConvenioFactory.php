<?php

namespace Database\Factories\Inventario;

use App\Models\Inventario\ContratoConvenio;
use App\Models\Inventario\Proveedor;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Inventario\ContratoConvenio>
 */
class ContratoConvenioFactory extends Factory
{
    protected $model = ContratoConvenio::class;

    public function definition(): array
    {
        $fechaInicio = Carbon::instance($this->faker->dateTimeBetween('-3 months', 'now'));
        $fechaFin = (clone $fechaInicio)->addYear();

        return [
            'name' => strtoupper($this->faker->words(3, true)),
            'codigo' => strtoupper($this->faker->bothify('??-##??-####')),
            'proveedor_id' => Proveedor::factory(),
            'fecha_inicio' => $fechaInicio->format('Y-m-d'),
            'fecha_fin' => $fechaFin->format('Y-m-d'),
            'estado_id' => 1,
            'user_create_id' => 1,
            'user_update_id' => 1,
        ];
    }
}


