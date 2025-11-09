<?php

namespace Database\Factories\Inventario;

use App\Models\Inventario\Aprobacion;
use App\Models\Inventario\DetalleOrden;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Inventario\Aprobacion>
 */
class AprobacionFactory extends Factory
{
    protected $model = Aprobacion::class;

    public function definition(): array
    {
        return [
            'detalle_orden_id' => DetalleOrden::factory(),
            'estado_aprobacion_id' => [49, 50][array_rand([49, 50])],
            'user_create_id' => 1,
            'user_update_id' => 1,
        ];
    }
}


