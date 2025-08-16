<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Inventario\Orden;

class OrdenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ordenes = [
            [
                'descripcion_orden' => 'DESCRIPCIÓN ORDEN 1',
                'tipo_orden_id' => 44,
                'fecha_devolucion' => '2025-08-16',
                'user_create_id' => 1,
                'user_update_id' => 1,
            ],
            [
                'descripcion_orden' => 'DESCRIPCIÓN ORDEN 2',
                'tipo_orden_id' => 45,
                'fecha_devolucion' => NULL,
                'user_create_id' => 1,
                'user_update_id' => 1,
            ],
            [
                'descripcion_orden' => 'DESCRIPCIÓN ORDEN 3',
                'tipo_orden_id' => 44,
                'fecha_devolucion' => '2025-07-22',
                'user_create_id' => 1,
                'user_update_id' => 1,
            ],
            [
                'descripcion_orden' => 'DESCRIPCIÓN ORDEN 4',
                'tipo_orden_id' => 45,
                'fecha_devolucion' => NULL,
                'user_create_id' => 1,
                'user_update_id' => 1,
            ],
            [
                'descripcion_orden' => 'DESCRIPCIÓN ORDEN 5',
                'tipo_orden_id' => 44,
                'fecha_devolucion' => '2025-08-03',
                'user_create_id' => 1,
                'user_update_id' => 1,
            ],
        ];
        foreach ($ordenes as $orden){
            Orden::create($orden);
        }
    }
}
