<?php

namespace Database\Factories\Inventario;

use App\Models\Inventario\Orden;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Inventario\Orden>
 */
class OrdenFactory extends Factory
{
    protected $model = Orden::class;

    public function definition(): array
    {
        $tipoOrdenId = $this->faker->randomElement([44, 45]);
        $fechaDevolucion = $tipoOrdenId === 44
            ? $this->faker->dateTimeBetween('+1 week', '+4 months')->format('Y-m-d')
            : null;

        return [
            'descripcion_orden' => strtoupper($this->faker->sentence(4)),
            'tipo_orden_id' => $tipoOrdenId,
            'fecha_devolucion' => $fechaDevolucion,
            'user_create_id' => 1,
            'user_update_id' => 1,
        ];
    }
}


