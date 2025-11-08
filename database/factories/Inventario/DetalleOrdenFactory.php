<?php

namespace Database\Factories\Inventario;

use App\Models\Inventario\DetalleOrden;
use App\Models\Inventario\Orden;
use App\Models\Inventario\Producto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Inventario\DetalleOrden>
 */
class DetalleOrdenFactory extends Factory
{
    protected $model = DetalleOrden::class;

    public function definition(): array
    {
        return [
            'orden_id' => Orden::factory(),
            'producto_id' => Producto::factory(),
            'cantidad' => $this->faker->numberBetween(1, 10),
            'estado_orden_id' => $this->faker->randomElement([46, 47, 48]),
            'user_create_id' => 1,
            'user_update_id' => 1,
        ];
    }
}


